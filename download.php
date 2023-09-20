<?php
session_start();
require_once('./functions/config.php');
require_once('./functions/functions.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit;
}

$userRole = $_SESSION['user_role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $databaseTypes = isset($_POST['database_types']) ? $_POST['database_types'] : [];
    $databaseQuantity = ($_POST['quantity']) ? $_POST['quantity'] : [];

    $countDiffFromZero = 0;
    foreach ($databaseQuantity as $key => $value) {
        if ($value > 0)
            $countDiffFromZero++;
    }

    if (empty($_POST['database_types'])) {
        header("Location: download.php?e=1");
        exit();
    } else if ($countDiffFromZero==0) {
        header("Location: download.php?e=2");
        exit();
    } else if (empty($_POST['city'])) {
        header("Location: download.php?e=3");
        exit();
    }

    $todayDate = date('Y-m-d');
    $uploadDirectory = "./download/$todayDate/";

    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true); 
    }

    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Błąd połączenia z bazą danych: ' . $e->getMessage());
    }

    $conditions = [];
    $limits = [];

    foreach ($databaseTypes as $type) {
        $conditions[] = "
        (
            SELECT phone_number, first_name, last_name, city, postal_code, full_address, age, database_type, last_access_date, is_blocked 
            FROM numbers
            WHERE city = :city 
                AND database_type = '$type' 
                AND is_blocked = 0 
                AND last_access_date <= DATE_SUB(NOW(), INTERVAL $BLOCK_TIME DAY) 
            LIMIT $databaseQuantity[$type]
        )";
    }

    $whereClause = implode(' UNION ALL ', $conditions);

    // Zapytanie do pobrania numerów
    $query = "$whereClause ORDER BY last_access_date ASC LIMIT :limit"; 

    $stmt = $db->prepare($query);
    $stmt->bindParam(':city', $city, PDO::PARAM_STR);
    $numOfNumbers = 5;
    if ($userRole === 'Koordynator') {
        $numOfNumbers = 10;
    } elseif ($userRole === 'Admin') {
        $numOfNumbers = 100;
    }

    $stmt->bindParam(':limit', $numOfNumbers, PDO::PARAM_INT);

    $stmt->execute();

    $countTypesAndQuantity = [];

    $numbers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tworzenie pliku i zapis
    $timestamp = date('Y-m-d_H-i-s');
    $databaseTypeString = implode('_', $databaseTypes);
    $filename = "temp_{$timestamp}_numbers.csv";
    $filePath = $uploadDirectory . $filename;

    $csvData = fopen($filePath, 'w');
    $csvHeaders = array(
        'phone_number',
        'first_name',
        'last_name',
        'city',
        'postal_code',
        'full_address',
        'age',
        'database_type',
        'last_access_date',
        'is_blocked'
    );
    fputcsv($csvData, $csvHeaders);

    foreach ($numbers as $person) {
        $countTypesAndQuantity[$person["database_type"]] += 1;
        $updateQuery = "UPDATE numbers SET last_access_date = CURRENT_TIMESTAMP WHERE phone_number = :phone_number";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindParam(':phone_number', $person['phone_number'], PDO::PARAM_STR);
        $updateStmt->execute();
        
        fputcsv($csvData, $person);
    }
    
    $filenameTypesAndQuantity = "";
    foreach($countTypesAndQuantity as $key => $value) {
        $filenameTypesAndQuantity .= $key . "-" . $value . "_";
    }
    fclose($csvData);
    $newFileName = "{$city}_{$filenameTypesAndQuantity}{$timestamp}_numbers.csv"; 
    $newFilePath = $uploadDirectory . $newFileName; 
    if (rename($filePath, $newFilePath)) {
        echo "Plik został pomyślnie zmieniony nazwę na $newFilePath.";
    } else {
        echo "Nie udało się zmienić nazwy pliku.";
    }

    // Aktualizacja daty pobrania numerów na dzisiaj

    $logDate = date('Y-m-d H:i:s');
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'Nieautoryzowany dostęp';
    $downloadedCount = count($numbers); // Liczba numerów

    // Zapisanie info w logach o pobraniu numerów
    $logQuery = "INSERT INTO logs (log_date, user_id, city, downloadedCount) VALUES (:log_date, :user_id, :city, :downloadedCount)";
    $logStmt = $db->prepare($logQuery);
    $logStmt->bindParam(':log_date', $logDate, PDO::PARAM_STR);
    $logStmt->bindParam(':user_id', $userId, PDO::PARAM_STR);
    $logStmt->bindParam(':city', $city, PDO::PARAM_STR);
    $logStmt->bindParam(':downloadedCount', $downloadedCount, PDO::PARAM_STR);
    $logStmt->execute();

    $db = null;

    header('Location: ' . $newFilePath);
    
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NumX</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="scripts.js"></script>
</head>
<body>
    <?php
        include('menu.php');
    ?>
    <?php
        if (isset($_GET['e'])) {
            if ($_GET['e'] == 1) {
                echo '<div class="error-message">Nie wybrano bazy danych</div>';
            } else if ($_GET['e'] == 2) {
                echo '<div class="error-message">Nie wybrano ilości</div>';
            } else if ($_GET['e'] == 3) {
                echo '<div class="error-message">Nie wybrano miasta</div>';
            } else {
                echo '<div class="error-message">Nieznany błąd</div>';
            }
        }
    ?>
    <h1>Pobieranie Danych</h1>
    <form method="POST" action="">
        <table>
            <tr>
                <th>Miasto:</th>
                <td>
                    <select name="city" id="city" required onchange="citySelectionChanged()">
                        <option value="">Wybierz miasto</option>
                        <?php
                            try {
                                $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                die('Błąd połączenia z bazą danych: ' . $e->getMessage());
                            }

                            $query = "SELECT DISTINCT city FROM numbers";
                            $stmt = $db->query($query);
                            
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $cityName = $row['city'];
                                echo "<option value='$cityName'>$cityName</option>";
                            }
                            $db = null;
                        ?>
                    </select>
                </td>
            <?php
                try {
                    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    die('Błąd połączenia z bazą danych: ' . $e->getMessage());
                }

                $query = "SELECT DISTINCT database_type FROM numbers";
                $stmt = $db->query($query);
                
                $databases_type = [];

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $databases_type[] = $row['database_type'];
                }
                $db = null;

                foreach ($databases_type as $database_type) {
                    echo "<th rowspan='2'>$database_type:</th>";
                }
                echo "<th rowspan='2'>Wszystkie:</th>";
            ?>
            </tr>
            <tr>
                <th colspan="2">Rodzaj bazy:</th>
            </tr>
            <tr>
                <th colspan="2" rowspan = "2">Ile pobrać:</th>

                <?php
                    foreach ($databases_type as $database_type) {
                        echo "<td><input type='checkbox' name='database_types[$database_type]' value='$database_type' id='check$database_type' onclick='toggleQuantityInput(this)' disabled></td>";
                    }
                ?>
                <td rowspan = "2"></td>
            </tr>
            <tr>
                <?php
                    foreach ($databases_type as $database_type) {
                        echo "<td><input type='number' name='quantity[$database_type]' id='quantity$database_type' placeholder='Ilość' value='0' min='0' max='0' disabled required></td>";
                    }
                ?>
            </tr>

            <?php
                $numberTypes = ["Dostępne numery" => "quantity", "Wszystkie numery" => "all", "Zablokowane numery" => "blocked", "Czasowo niedostępne" => "temporary"];
                
                foreach ($numberTypes as $key => $value) {
                    echo "<tr>";
                    echo "<th colspan='2'>$key:</th>";
                    foreach ($databases_type as $database_type) {
                        echo "<td><label id='" . $value . $database_type . "Label'></label></td>";
                    }
                    echo "<td><label id='" . $value . "TotalLabel'></label></td>";
                    echo "</tr>";
                }
            ?>
            <tr>
                <th colspan="<?php echo count($databases_type) + 3; ?>">
                    <button type="submit">Pobierz Dane</button>
                </th>
            </tr>
        </table>
    </form>
                
    <br><br><br><br><br><br><br>

    <form method="POST" action="">
        <label for="phoneNumber">Numer Telefonu (opcjonalnie):</label>
        <input type="text" name="phoneNumber" id="phoneNumber"><br>
        <button type="button" onclick="updateLastAccessDate()">Ustaw Datę Ostatniego Dostępu na 1 stycznia 2023</button>
    </form>
    <?php
        echo '<script>';
        echo 'var databaseTypes = ' . json_encode($databases_type) .';';
        echo '</script>';
    ?>

</body>
</html>
