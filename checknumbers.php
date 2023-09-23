<?php
    session_start();
    require_once('./functions/config.php');
    require_once('./functions/functions.php');
    
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != "Admin") {
        header("Location: index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sprawdzanie numerów - NumX</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="scripts.js"></script>
</head>
<body>

    <!-- Menu na górze -->
    <?php
        include('menu.php');
    ?>

    <!-- Treść strony -->
    <div class="container mt-5 content">
        <div class="row">
            <div class="col">
                <h2>Sprawdzanie numerów</h2>
                <div class="mb-3">
                    <form class="needs-validation" novalidate method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data">
                        <label for="formFile" class="form-label">Wybierz plik CSV którego numery chcesz sprawdzić:</label>
                        <input class="form-control" type="file" id="formFile" name="filePostalcodes" accept=".csv">
                        <button class="w-100 btn btn-primary btn-lg my-2" type="submit">Sprawdź</button>
                    </form>
                </div>
    
    <?php
        $fileOk = False;
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["filePostalcodes"])) {
            $file = $_FILES["filePostalcodes"];
            
            // Sprawdzenie, czy przesłany plik ma rozszerzenie .csv
            $allowedExtensions = array("csv");
            $fileExtension = pathinfo($file["name"], PATHINFO_EXTENSION);
            
            $numbersStatDBType = [];
            $numbersStatPostalcodes = [];

            if (in_array($fileExtension, $allowedExtensions)) {
                // Sprawdzanie, czy plik został przesłany bez błędów
                if ($file["error"] == UPLOAD_ERR_OK) {
                    $fileTmpName = $file["tmp_name"];
        
                    // Otwieranie i przetwarzanie pliku CSV
                    if (($handle = fopen($fileTmpName, "r")) !== FALSE) {
                        // Odczytywanie i wyświetlanie zawartości pliku
                        $fileOk = True;
                        echo "<h3>Tabela z wynikami</h3>";
                        echo "<table class='table table-bordered'>";
                        $i = 0;
                        $total = 0;
                        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                            echo "<tr>";
                            if($i == 0) {
                                echo "<th>Numer telefonu</th>";
                                echo "<th>Rodzaj bazy</th>";
                                echo "<th>Kod pocztowy</th>";
                            } else {
                                foreach ($data as $cell) {
                                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                                    try {
                                        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                    } catch (PDOException $e) {
                                        die("Błąd połączenia z bazą danych: " . $e->getMessage());
                                    }
                
                                    $stmt = $db->prepare("SELECT * FROM numbers WHERE phone_number = :phone_number LIMIT 1");
                                    $stmt->bindParam(':phone_number', $cell);
                                    $stmt->execute();
                                    $db = null;
                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                    if ($row) {
                                        echo "<td>" . $row['database_type'] . "</td>";
                                        echo "<td>" . $row['postal_code'] . "</td>";
                                        if(isset($numbersStatDBType[$row['database_type']])) {
                                            $numbersStatDBType[$row['database_type']]++;
                                        } else {
                                            $numbersStatDBType[$row['database_type']] = 1;
                                        }
                                        if(isset($numbersStatPostalcodes[$row['postal_code']])) {
                                            $numbersStatPostalcodes[$row['postal_code']]++;
                                        } else {
                                            $numbersStatPostalcodes[$row['postal_code']] = 1;
                                        }
                                        $total++;
                                        // Dodaj kolejne pola z bazy danych, które chcesz wyświetlić
                                    } else {
                                        echo "<td style='color: red'>Brak numeru w bazie</td>";
                                        echo "<td style='color: red'>Brak numeru w bazie</td>";
                                        if(isset($numbersStatDBType["Brak numeru w bazie"])) {
                                            $numbersStatDBType['Brak numeru w bazie']++;
                                        } else {
                                            $numbersStatDBType['Brak numeru w bazie'] = 1;
                                        }
                                        if(isset($numbersStatPostalcodes['Brak kodu w bazie'])) {
                                            $numbersStatPostalcodes['Brak kodu w bazie']++;
                                        } else {
                                            $numbersStatPostalcodes['Brak kodu w bazie'] = 1;
                                        }
                                        $total++;
                                    }


                                }
                                echo "</tr>";
                            }
                            $i++;
                        }
                        echo "</table>";
        
                        fclose($handle);
                    } else {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Błąd odczytu pliku CSV.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                    }
                } else {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Błąd podczas przesyłania pliku.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
            } else {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Przesyłany plik musi mieć rozszerzenie .csv.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        }
    ?>
            </div>
            <?php
                if ($fileOk) {
            ?>
            <div class="col">
                <div class="row">
                    <h2>Szczegóły - rodzaj bazy</h2>
                    <div class="mb-3">
                        <?php
                            echo "<table class='table table-bordered'>";
                            echo "<tr>";
                            echo "<th>Rodzaj bazy</th>";
                            echo "<th>Ilość numerów</th>";
                            echo "<th>% występowania</th>";
                            echo "</tr>";
                            foreach ($numbersStatDBType as $key => $value) {
                                echo "<tr>";
                                echo "<td>".$key."</td>";
                                echo "<td>".$value."</td>";
                                $percent = round($value / $total * 100, 2);
                                echo "<td>".$percent."%</td>";
                                echo "</tr>";
                            }
                            echo "<tr>";
                            echo "<th>Ilość numerów</th>";
                            echo "<th>".$total."</th>";
                            echo "<th>100%</th>";
                            echo "</tr>";
                            echo "</table>";
                        ?>
                    </div>
                </div>
                <div class="row">
                    <h2>Szczegóły - kod pocztowy</h2>
                    <div class="mb-3">
                        <?php
                            echo "<table class='table table-bordered'>";
                            echo "<tr>";
                            echo "<th>Kod pocztowy</th>";
                            echo "<th>Ilość numerów</th>";
                            echo "<th>% występowania</th>";
                            echo "</tr>";
                            foreach ($numbersStatPostalcodes as $key => $value) {
                                echo "<tr>";
                                echo "<td>".$key."</td>";
                                echo "<td>".$value."</td>";
                                $percent = round($value / $total * 100, 2);
                                echo "<td>".$percent."%</td>";
                                echo "</tr>";
                            }
                            echo "<tr>";
                            echo "<th>Ilość numerów</th>";
                            echo "<th>".$total."</th>";
                            echo "<th>100%</th>";
                            echo "</tr>";
                            echo "</table>";
                        ?>
                    </div>
                </div>
            </div>
            <?php
                }
            ?>
        </div>
    </div>

    <!-- Stopka -->
    <footer class="text-center py-3">
        &copy; 2023 NumX - zarządzanie numerami
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
