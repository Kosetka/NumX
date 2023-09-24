<?php
    session_start();
    require_once('./functions/config.php');
    require_once('./functions/functions.php');
    
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NumX - Advanced Phone Number Management</title>

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
        <h1>NumX - Advanced Phone Number Management</h1>
        <p><strong>NumX</strong> is a versatile phone number management tool that allows efficient collection, browsing, and analysis of phone number-related information. This application is particularly useful for companies and organizations that need an effective way to monitor and manage their phone number databases.</p>

        <h2>Main Features of NumX:</h2>
        <ol>
            <li><strong>Phone Number Database:</strong> NumX enables you to create and manage phone number databases. You can easily add new numbers, edit existing entries, and remove unnecessary information.</li>
            <li><strong>Database Types Management:</strong> The application allows you to categorize numbers based on different database types such as MySQL, PostgreSQL, SQLite, and MongoDB. You can choose which database types are active and monitor their quantity.</li>
            <li><strong>Data Export and Import:</strong> NumX allows you to export and import data in CSV format, making it easy to move information between different applications and platforms.</li>
            <li><strong>Monitoring and Reporting:</strong> The application lets you track activities related to numbers, such as blocked numbers and availability. You can generate reports and analyze data to make more informed decisions.</li>
            <li><strong>Authentication System:</strong> NumX provides a user authentication system, meaning that access to data can be controlled by user permissions.</li>
            <li><strong>Responsive User Interface:</strong> The application is accessible through a web browser, and its interface is responsive, which means it can be used on various devices, including smartphones and tablets.</li>
        </ol>

        <p>The "NumX" application was created to simplify contact data and phone number management and provide a tool for effectively monitoring and analyzing this information. Regardless of your type of business, "NumX" can be a valuable tool for efficient contact number management.</p>
    </div>

    <!-- Stopka -->
    <footer class="text-center py-3">
        &copy; 2023 NumX - Advanced Phone Number Management
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
