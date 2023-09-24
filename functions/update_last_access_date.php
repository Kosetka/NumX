<?php
    include 'config.php';
    include 'functions.php'; 

    $phoneNumber = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : null;

    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection error: " . $e->getMessage());
    }
    
    // Wywołaj funkcję setLastAccessDateToJanuary1st2023 z odpowiednimi parametrami
    if (setLastAccessDateToJanuary1st2023($db, $phoneNumber)) {
        echo "The last access date has been updated.";
    } else {
        echo "Error while updating the last access date.";
    }
?>