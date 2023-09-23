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
    <title>Historia pobrań - NumX</title>

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
    <h2>Historia pobrań</h2>
      <div class="table-responsive small">
        <table class="table table-striped table-sm">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Data</th>
              <th scope="col">Użytkownik</th>
              <th scope="col">Miasto</th>
              <th scope="col">Ilość pobranych numerów</th>
              <th scope="col">Pobierz plik</th>
            </tr>
          </thead>
          <tbody>
            <?php
                try {
                    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    die('Błąd połączenia z bazą danych: ' . $e->getMessage());
                }

                $query = "SELECT * FROM users";
                $stmt = $db->query($query);
                
                $users = [];
                $users["Nieautoryzowany dostęp"]["username"] = "Nieautoryzowany dostęp";
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $users[$row["id"]]["username"] = $row["username"];
                    $users[$row["id"]]["role"] = $row["role"];
                }


                $query = "SELECT * FROM logs ORDER BY id DESC";
                $stmt = $db->query($query);
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if($users[$row["user_id"]]["username"] == "Nieautoryzowany dostęp") {
                        $userId = 0;
                    } else {
                        $userId = $row["user_id"];
                    }

                    echo '<tr>
                        <td>'.$row["id"].'</td>
                        <td>'.$row["log_date"].'</td>
                        <td>
                            <a href="accounts.php?edit='.$userId.'">
                        '.$users[$row["user_id"]]["username"].'
                        
                            </a>
                        </td>
                        <td>'.$row["city"].'</td>
                        <td>'.$row["downloadedCount"].'</td>
                        <td>
                            <a href="'.$row["filePath"].'" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"></path>
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"></path>
                                </svg>
                            Pobierz
                            </a>
                        </td>
                        
                    </tr>';
                }
                $db = null;
            ?>
          </tbody>
        </table>
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
