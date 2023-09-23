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
                <a class="nav-link active" aria-current="page" href="index.php">Strona Główna</a>
            </li>
            <?php
                if (!isset($_SESSION['user_id'])) {
            ?>
            <li class="nav-item">
                <a class="nav-link" href="login.php">Logowanie</a>
            </li>
            
            <?php
                } else {
            ?>
            <li class="nav-item">
                <a class="nav-link" href="download.php">Pobieranie</a>
            </li>
            <li class="nav-item">
                <a class="nav-link disabled" href="szablon.php">Szablon</a>
            </li>
            <li class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Raporty
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#">Raport 1</a></li>
                    <li><a class="dropdown-item" href="#">--> Statystyka pobranych numerów (kto i ile z okresu)</a></li>
                    <li><a class="dropdown-item" href="#">--> Statystyka ilości numerów i rodzajów baz</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Raport 3</a></li>
                </ul>
            </li>
                
            <?php
                if (isset($_SESSION['user_role']) && ($_SESSION['user_role'] == "Admin")) {
            ?>
                <li class="dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Panel administratora
                    </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="set_date.php">Resetowanie dat numerów</a></li>
                    <li><a class="dropdown-item" href="#">--> Sprawdzanie kodów pocztowych</a></li>
                    <li><a class="dropdown-item" href="#">--> Generowanie SMS</a></li>
                    <li><a class="dropdown-item" href="#">--> Dodawanie numerów</a></li>
                    <li><a class="dropdown-item" href="history.php">Historia pobrań</a></li>
                    <li><a class="dropdown-item" href="#">--> panel dla miast i liczenie okolicy do X km</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="register.php">Załóż konto</a></li>
                    <li><a class="dropdown-item" href="accounts.php">Lista użytkowników</a></li>
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
                    <li><span class="dropdown-item disabled" >Zalogowany jako:<br><strong><?php echo $userName;?></strong></span></li>
                    <li><span class="dropdown-item disabled" >Uprawnienia:<br><strong><?php echo $userRole;?></strong></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="logout.php">Wyloguj</a></li>
                </ul>
            </li>
        </ul>
            <?php
                }
            ?>

    </div>
  </div>
</nav>
