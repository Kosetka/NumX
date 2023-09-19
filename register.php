<?php
session_start();
require_once('config.php');


if (isset($_SESSION['user_id'])) {
    header("Location: download.php");
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

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $db->lastInsertId(); // Ustaw zmienną sesji po udanej rejestracji
            $_SESSION['user_role'] = "Koordynator"; 
            header("Location: download.php"); // Przekieruj na stronę powitalną
            exit();
        } else {
            $error_message = "Błąd przy rejestracji użytkownika.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rejestracja</title>
</head>
<body>
    <?php
        include('menu.php');
    ?>
<h2>Rejestracja</h2>

<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
    <label for="usernameCreated">Nazwa użytkownika:</label>
    <input type="text" id="usernameCreated" name="usernameCreated" required><br><br>

    <label for="passwordCreated">Hasło:</label>
    <input type="password" id="passwordCreated" name="passwordCreated" required><br><br>

    <label for="confirm_password">Potwierdź hasło:</label>
    <input type="password" id="confirm_password" name="confirm_password" required><br><br>

    <input type="submit" value="Zarejestruj">
</form>

<?php
if (isset($error_message)) {
    echo '<p style="color:red;">' . $error_message . '</p>';
}
?>

</body>
</html>
