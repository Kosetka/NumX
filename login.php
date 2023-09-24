<?php
    session_start();
    require_once('./functions/config.php');
    require_once('./functions/functions.php');

    if (isset($_SESSION['user_id'])) {
        header('Location: index.php'); 
        exit;
    }

    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["username"];
        $password = $_POST["password"];

        $stmt = $db->prepare("SELECT id, username, password, role, firstName, lastName, active FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        $row = $stmt->fetch($db::FETCH_ASSOC);

        $_SESSION['active'] = $row["active"];
        if (isset($_SESSION['active']) && $_SESSION['active'] == 0) {
            header("Location: login.php");
            exit();
        }

        if ($row && password_verify($password, $row["password"])) {
            $_SESSION['user_id'] = $row["id"];
            $_SESSION['user_role'] = $row["role"];
            $_SESSION['username'] = $row["username"];
            $_SESSION['firstName'] = $row["firstName"];
            $_SESSION['lastName'] = $row["lastName"];

            header("Location: index.php");
            exit();
        } else {
            $error_message = "Invalid username or password";
        }
    }
?>


<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - Advanced Phone Number Management</title>

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

        <main class="form-signin m-auto d-flex align-items-center py-3 px-5 bg-body-tertiary" style="border-radius: 25px">
            <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                <h2 class="h3 mb-3 fw-normal">Log in</h2>
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" required>
                    <label for="username">Username</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password">
                    <label for="password">Password</label>
                    <?php
                        if(isset($error_message)){
                            echo '<div class="alert alert-danger alert-dismissible fade show my-2" role="alert">
                                ' . $error_message . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                        }
                    ?>
                </div>
                <button class="btn btn-primary w-100 py-2 my-3" type="submit">Zaloguj</button>
            </form>
        </main>

    


    

    <!-- Stopka -->
    <footer class="text-center py-3">
        &copy; 2023 NumX - Advanced Phone Number Management
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>

