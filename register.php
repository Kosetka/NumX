<?php
    session_start();
    require_once('./functions/config.php');
    require_once('./functions/functions.php');

    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != "Admin") {
        header("Location: index.php");
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $usernameCreated = $_POST["usernameCreated"];
        $passwordCreated = $_POST["passwordCreated"];
        $confirm_password = $_POST["confirm_password"];

        // Zabezpiecz dane przed SQL Injection
        $usernameCreated = htmlspecialchars($usernameCreated);
        $passwordCreated = htmlspecialchars($passwordCreated);
        $confirm_password = htmlspecialchars($confirm_password);

        // Sprawdź, czy hasło i potwierdzenie hasła są identyczne
        if ($passwordCreated != $confirm_password) {
            $error_message = "Hasło i potwierdzenie hasła są różne.";
        } else {
            try {
                $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Błąd połączenia z bazą danych: " . $e->getMessage());
            }

            // Zahaszuj hasło przed zapisaniem do bazy danych
            $hashed_password = password_hash($passwordCreated, PASSWORD_BCRYPT);

            // Wstaw użytkownika do bazy danych
            $stmt = $db->prepare("INSERT INTO users (username, password, role) VALUES (:usernameCreated, :passwordCreated, 'Koordynator')");
            $stmt->bindParam(':usernameCreated', $usernameCreated);
            $stmt->bindParam(':passwordCreated', $hashed_password);

            try {
                $stmt->execute();
                header("Location: register.php?action=success");
                exit();
            } catch (PDOException $e){
                header("Location: register.php?action=error");
                exit();
            }
            $db = null;
        }
    }
?>

<!DOCTYPE html>
<html lang="pl" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zakładanie konta - NumX</title>

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
        <?php
            if (isset($_GET["action"]) && ($_GET["action"] == "error")) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Błąd przy zakładaniu konta.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            } else if (isset($_GET["action"]) && ($_GET["action"] == "success")) {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Pomyślnie założono konto użytkownika.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
            }
        ?>
        <h1>Rejestracja</h1>

        <div class="col-md-7 col-lg-6">
            <h4 class="mb-3">Rejestracja</h4>
            <form class="needs-validation" novalidate method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label for="firstName" class="form-label">Imię</label>
                        <input type="text" class="form-control" id="firstName" placeholder="" value="" required>
                        <div class="invalid-feedback">
                            Imię jest wymagane.
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="lastName" class="form-label">Nazwisko</label>
                        <input type="text" class="form-control" id="lastName" placeholder="" value="" required>
                        <div class="invalid-feedback">
                            Nazwisko jest wymagane.
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="usernameCreated" class="form-label">Nazwa użytkownika</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">@</span>
                            <input type="text" class="form-control" id="usernameCreated" name="usernameCreated" placeholder="Nazwa użytkownika" required>
                        <div class="invalid-feedback">
                            Nazwa użytkownika jest wymagana.
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label">E-mail <span class="text-body-secondary">(Opcjonalnie)</span></label>
                        <input type="email" class="form-control" id="email" placeholder="twoj@mail.pl">
                        <div class="invalid-feedback">
                            Musisz podać prawidłowy adres e-mail.
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="passwordCreated" class="form-label">Hasło</label>
                        <input type="password" class="form-control" id="passwordCreated" name="passwordCreated" required>
                        <div class="invalid-feedback">
                            Hasła muszą się zgadzać.
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="confirm_password" class="form-label">Powtórz hasło</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <div class="invalid-feedback">
                            Hasła muszą się zgadzać.
                        </div>
                    </div>
                <button class="w-100 btn btn-primary btn-lg" type="submit">Załóż konto</button>
                </div>
            </form>
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