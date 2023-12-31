<?php
    include 'config.php';
    include 'functions.php'; 

    $city = isset($_POST['city']) ? $_POST['city'] : '';
    $databaseType = isset($_POST['databaseType']) ? $_POST['databaseType'] : '';
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    try {
        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Database connection error: " . $e->getMessage());
    }
    
    $quantity = getQuantityFromDatabase($db, $city, $databaseType, $action);

    // Przygotuj odpowiedź w formie JSON
    $response = array(
        $databaseType => $quantity
    );



    // Zwróć odpowiedź jako JSON
    header('Content-Type: application/json');
    echo json_encode($response);
?>