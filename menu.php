<?php
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_id'])) {
        $userRole = $_SESSION['user_role'];
        echo "Twoja rola użytkownika to: $userRole";
    } else {
        echo "Nie jesteś zalogowany lub brak informacji o roli użytkownika.";
    }
?>

<ul>
    <li><a href="login.php">Logowanie</a></li>
    <li><a href="register.php">Rejestracja</a></li>
    <li><a href="download.php">Pobieranie</a></li>
    <li><a href="logout.php">Wyloguj</a></li>
</ul>