<?php
    if (isset($_SESSION['active']) && $_SESSION['active'] == 0) {
        header("Location: logout.php");
        exit();
    }
    if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
        $userRole = $_SESSION['user_role'];
        $userName = $_SESSION['username'];
        $firstName = $_SESSION['firstName'];
        $lastName = $_SESSION['lastName'];
    } else {
        //header("Location: login.php");
        //exit();
    }
?>


<nav class="navbar navbar-expand-lg bg-body-tertiary p-3 mb-3 border-bottom">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">NumX</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="index.php">Main page</a>
            </li>
            <?php
                if (!isset($_SESSION['user_id'])) {
            ?>
            <li class="nav-item">
                <a class="nav-link" href="login.php">Log in</a>
            </li>
            
            <?php
                } else {
            ?>
            <li class="nav-item">
                <a class="nav-link" href="download.php">Download numbers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="szablon.php">Szablon</a>
            </li>
            <li class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Reports
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="numbertypes.php">Numbers Statistics - database type</a></li>
                    <li><a class="dropdown-item" href="numberpostalcodes.php">Numbers Statistics - postal code</a></li>
                    <li><a class="dropdown-item" href="numbercities.php">Numbers Statistics - city</a></li>
                    <li><a class="dropdown-item" href="numbercitiesdetailed.php">Numbers Statistics - city - details</a></li>                    
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">--> Statystyka pobranych numerów (kto i ile z okresu)</a></li>
                </ul>
            </li>
                
            <?php
                if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == "Admin")) {
            ?>
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Admin Panel
                    </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="set_date.php">Reset Numbers Dates</a></li>
                    <li><a class="dropdown-item" href="checknumbers.php">Number Verification</a></li>
                    <li><a class="dropdown-item" href="history.php">Download history</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="register.php">Create account</a></li>
                    <li><a class="dropdown-item" href="accounts.php">Account Management</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">--> panel dla miast i liczenie okolicy do X km</a></li>
                    <li><a class="dropdown-item" href="#">--> Generowanie SMS</a></li>
                    <li><a class="dropdown-item" href="#">--> Dodawanie numerów</a></li>
                </ul>
            </li>
            <?php
                }
            ?>
            
        </ul>
        <ul class="navbar-nav d-flex px-5">
            <li class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="https://github.com/kosetka.png" alt="mdo" width="32" height="32" class="rounded-circle">
                    <strong><?php echo $firstName. ' ' . $lastName; ?></strong>
                </a>
                <ul class="dropdown-menu">
                    <li><span class="dropdown-item disabled" >Logged as:<br><strong><?php echo $userName;?></strong></span></li>
                    <li><span class="dropdown-item disabled" >Role:<br><strong><?php echo $userRole;?></strong></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php">Log out</a></li>
                </ul>
            </li>
        </ul>
            <?php
                }
            ?>

    </div>
  </div>
</nav>
