<?php
    include 'config.php';
    include 'functions.php'; 

    $phoneNumber = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : null;

    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }
    
    // Wywołaj funkcję setLastAccessDateToJanuary1st2023 z odpowiednimi parametrami
    if (setLastAccessDateToJanuary1st2023($db, $phoneNumber)) {
        echo "Data ostatniego dostępu została zaktualizowana.";
    } else {
        echo "Błąd podczas aktualizacji daty ostatniego dostępu.";
    }
?>