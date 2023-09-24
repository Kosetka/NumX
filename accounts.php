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
    <title>Account Management - NumX - Advanced Phone Number Management</title>

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
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $firstNameCreated2 = $_POST["firstName"];
            $lastNameCreated2 = $_POST["lastName"];
            $id = $_POST["id"];

            if (isset($_POST["email"])) {
                $emailCreated = $_POST["email"];
            } else {
                $emailCreated = NULL;
            }

            if (isset($_POST["passwordCreated"])) {
                $passwordCreated = $_POST["passwordCreated"];
            } else {
                $passwordCreated = NULL;
            }

            if (isset($_POST["active"])) {
                if ($_POST["active"] == "on") {
                    $activeCreated = 1;
                } else {
                    $activeCreated = 0;
                }
            }

            $roleCreated = $_POST["role"];
    
            // Zabezpiecz dane przed SQL Injection
            $passwordCreated = htmlspecialchars($passwordCreated);
            $firstNameCreated2 = htmlspecialchars($firstNameCreated2);
            $lastNameCreated2 = htmlspecialchars($lastNameCreated2);
            $emailCreated = htmlspecialchars($emailCreated);
    
    
            try {
                $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }

            // Zahaszuj hasło przed zapisaniem do bazy danych
            $hashed_password = password_hash($passwordCreated, PASSWORD_BCRYPT);

            if ($passwordCreated == "") {
                $stmt = $db->prepare("UPDATE users SET role = :roleCreated, firstname = :firstNameCreated2, lastname = :lastNameCreated2, email = :emailCreated, active = :activeCreated WHERE id = :id");
            } else {
                $stmt = $db->prepare("UPDATE users SET password = :passwordCreated, role = :roleCreated, firstname = :firstNameCreated2, lastname = :lastNameCreated2, email = :emailCreated, active = :activeCreated WHERE id = :id");
                $stmt->bindParam(':passwordCreated', $hashed_password);
            }

            // nie działa hasło

            // Wstaw użytkownika do bazy danych
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':firstNameCreated2', $firstNameCreated2);
            $stmt->bindParam(':roleCreated', $roleCreated);
            $stmt->bindParam(':lastNameCreated2', $lastNameCreated2);
            $stmt->bindParam(':emailCreated', $emailCreated);
            $stmt->bindParam(':activeCreated', $activeCreated);

            try {
                $stmt->execute();
                $messegeStatus = 1;
            } catch (PDOException $e){
                $messegeStatus = 0;
            }
            $db = null;
            
        }



        if(isset($_GET["edit"])) {
            try {
                $db2 = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                $db2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
            $id = htmlspecialchars($_GET["edit"]);
            $stmt = $db2->prepare("SELECT id, username, password, role, firstName, lastName, email, active FROM users WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $stmt2 = $db2->prepare("SELECT username FROM users WHERE id = :id");
            $stmt2->bindParam(':id', $id);
            $stmt2->execute();
            $userExists = True;

            $rowUser = $stmt2->fetch(PDO::FETCH_ASSOC);
            if (!$rowUser) {
                $userExists = False;
            }


            while ($rowUser = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $userGetFirstName = $rowUser['firstName'];
                $userGetLastName = $rowUser['lastName'];
                $userGetName = $rowUser['username'];
                $userEmail = "";
                $userRole = $rowUser['role'];
                $active = $rowUser['active'];
                if (isset($rowUser['email'])) {
                    $userEmail = $rowUser['email'];
                }
                
            }
            $db2 = null;

            if ($userExists) {
    ?>

        <div class="col-md-7 col-lg-6 mx-auto text-center">
            <h4 class="mb-3">Edit User Account</h4>
            <form class="needs-validation" novalidate method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="id" name="id" value="<?php echo $id; ?>" hidden>
                        <label for="firstName" class="form-label">Firstname</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" placeholder="" value="<?php echo $userGetFirstName; ?>" required>
                        <div class="invalid-feedback">
                            Firstname is required.
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <label for="lastName" class="form-label">Lastname</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" placeholder="" value="<?php echo $userGetLastName; ?>" required>
                        <div class="invalid-feedback">
                            Lastname is required.
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="usernameCreated" class="form-label">Username</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">Login</span>
                            <input type="text" class="form-control" id="usernameCreated" name="usernameCreated" value = "<?php echo $userGetName; ?>" placeholder="" required disabled>
                            <div class="invalid-feedback">
                                Username is required.
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="email" class="form-label">E-mail <span class="text-body-secondary">(Optional)</span></label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">E-mail</span>
                            <input type="email" class="form-control" id="email" name="email" value = "<?php echo $userEmail; ?>" placeholder="">
                            <div class="invalid-feedback">
                                You must provide a valid email address."
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="passwordCreated" class="form-label">Password</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text">Password</span>
                            <input type="password" class="form-control" id="passwordCreated" name="passwordCreated">
                            <div class="invalid-feedback">
                                Passwords must match.
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="passwordCreated" class="form-label">User Role</label>
                        <select class="form-select form-select-sm" aria-label="Small select example" id="role" name="role">
                            <option value="Admin" <?php echo ($userRole == 'Admin' ? 'selected': ''); ?>>Admin</option>
                            <option value="Koordynator" <?php echo ($userRole == 'Koordynator' ? 'selected': ''); ?>>Coordinator</option>
                        </select>
                    </div>
                    <div class="col-12 ">
                        <label for="flexSwitchCheckChecked" class="form-label">Account Activated: </label>
                        <input class="form-check-input" type="checkbox" role="switch" name="active" id="flexSwitchCheckChecked" <?php echo ($active == 1 ? 'checked': ''); ?>>
                    </div>
                <button class="w-100 btn btn-primary btn-lg" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
        <?php
                } else {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        This account does not exist!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
            }
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if ($messegeStatus == 1) {
                    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Account Edit Successful.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                } else if ($messegeStatus == 0) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error While Editing an Account.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
            }

        ?>


        <div class="my-3 p-3 bg-body rounded shadow-sm">
            <h6 class="border-bottom pb-2 mb-0">List of Accounts</h6>
            <?php
                try {
                    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    die('Database connection error: ' . $e->getMessage());
                }

                $query = "SELECT * FROM users";
                $stmt = $db->query($query);
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $active = $row['active'];
                    echo '
                    <div class="d-flex text-body-secondary pt-3">
                        <svg class="bd-placeholder-img flex-shrink-0 me-2 rounded" width="32" height="32" role="img" aria-label="Active" preserveAspectRatio="xMidYMid slice" focusable="false"><rect width="100%" height="100%" fill="'.($row["active"] === 1 ? "green":"red").'"/></svg>
                        <div class="pb-3 mb-0 small lh-sm border-bottom w-100">
                            <div class="d-flex justify-content-between">
                                <strong class="text-gray-dark">'.$row["firstName"].' '.$row["lastName"].'</strong>
                                <a href="accounts.php?edit='.$row["id"].'">Edit</a>
                            </div>
                            <span class="d-block">'.$row["role"].'</span>
                        </div>
                    </div>
                    ';

                }
                $db = null;

            ?>
            <small class="d-block text-end mt-3">
                <a href="register.php">Create New Account</a>
            </small>
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
