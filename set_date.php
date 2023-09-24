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
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Numbers Dates - NumX Advanced Phone Number Management</title>

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
        <div class="px-5 mx-auto text-center">
            <h4>Resetting the last contact date</h4>
            <p class="lead">Setting the last access date to January 1, 2023. This action is irreversible!"</p>
            <form method="POST" action="">
                <label for="phoneNumber">Phone number (optional):</label>
                <input type="text" name="phoneNumber" id="phoneNumber"><br>
                <p class="lead">
                <button type="button" class="px-5 mt-5 btn btn-lg btn-danger" onclick="updateLastAccessDate()">Confirm</button>
            </form>
            </p>
        </div>
    </div>

    <!-- Stopka -->
    <footer class="text-center py-3">
        &copy; 2023 NumX - Advanced Phone Number Management
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
