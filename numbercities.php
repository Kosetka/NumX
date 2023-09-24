<?php
    session_start();
    require_once('./functions/config.php');
    require_once('./functions/functions.php');
    
    if (!isset($_SESSION['user_role'])) {
        header("Location: index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Numbers Statistics - city - NumX Advanced Phone Number Management</title>

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
            echo "<h3>Numbers Statistics - city</h3>";
            echo "<table class='table table-bordered'>";
            echo "<tr>";
            echo "<th>City</th>";
            echo "<th>All numbers</th>";
            echo "<th>Available numbers</th>";
            echo "<th>Blacklisted numbers</th>";
            echo "<th>Temporary blocked numbers</th>";
            echo "</tr>";
            try {
                $db3 = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                $db3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
            

            $query = "SELECT DISTINCT city FROM numbers ORDER BY city ASC";
            $stmt = $db3->query($query);

            $numArr = [0, 0, 0, 0];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $city = $row['city'];
                echo "<tr>";
                echo "<td>".$city."</td>";
                try {
                    $db2 = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                    $db2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    die("Database connection error: " . $e2->getMessage());
                }
                $numArr[0] += getQuantityFromDatabase($db2, $city, 'all', 1);
                $numArr[1] += getQuantityFromDatabase($db2, $city, 'all', 3);
                $numArr[2] += getQuantityFromDatabase($db2, $city, 'all', 2);
                $numArr[3] += getQuantityFromDatabase($db2, $city, 'all', 4);
                echo "<td>".getQuantityFromDatabase($db2, $city, 'all', 1)."</td>";
                echo "<td>".getQuantityFromDatabase($db2, $city, 'all', 3)."</td>";
                echo "<td>".getQuantityFromDatabase($db2, $city, 'all', 2)."</td>";
                echo "<td>".getQuantityFromDatabase($db2, $city, 'all', 4)."</td>";
                $db2 = null;
                echo "</tr>";
            }
            echo "<tr>";
            echo "<th>Total</th>";
            echo "<th>".$numArr[0]."</th>";
            echo "<th>".$numArr[1]."</th>";
            echo "<th>".$numArr[2]."</th>";
            echo "<th>".$numArr[3]."</th>";
            echo "</tr>";
            $db3 = null;
            echo "</table>";
        ?>
    </div>

    <!-- Stopka -->
    <footer class="text-center py-3">
        &copy; 2023 NumX - Advanced Phone Number Management
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
