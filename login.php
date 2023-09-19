<?php
session_start();
require_once('/functions/config.php');

if (isset($_SESSION['user_id'])) {
    header("Location: download.php");
    exit();
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

    $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $row = $stmt->fetch($db::FETCH_ASSOC);

    if ($row && password_verify($password, $row["password"])) {
        $_SESSION['user_id'] = $row["id"];
        $_SESSION['user_role'] = $row["role"];
        header("Location: download.php");
        exit();
    } else {
        $error_message = "Błędna nazwa użytkownika lub hasło";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logowanie</title>
</head>
<body>
    <?php
        include('menu.php');
    ?>
<h2>Logowanie</h2>

<form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
    <label for="username">Nazwa użytkownika:</label>
    <input type="text" id="username" name="username" required><br><br>

    <label for="password">Hasło:</label>
    <input type="password" id="password" name="password" ><br><br>

    <input type="submit" value="Zaloguj">
</form>

<?php
if(isset($error_message)){
    echo '<p style="color:red;">' . $error_message . '</p>';
}
?>

</body>
</html>
