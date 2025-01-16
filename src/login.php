<?php
session_start();
require_once 'php/autoloader.php';
Autoloader::register();

use Tools\User\UserTools;
if (!empty($_POST['login']) && !empty($_POST['password'])) {
    $login = UserTools::login($_POST['login'], $_POST['password']);
    if ($login == true) {
        header('Location: index.php');
    } else {
        header('Location: login.php?error=1');
    }
} else if (!empty($_POST['login']) || !empty($_POST['password'])) {
    header('Location: login.php?error=2');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/pages/login.css">
    <script src="js/W3IncludeHTML.js"></script>
    <title>Login</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="index.php">Déconnexion</a></li>
                <?php else: ?>
                    <li id="loginButton"><a href="login.php">Login</a></li>
                    <li id="registerButton"><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Login</h1>
        <form action="" method="POST">
        <label for="login">Login</label>
        <input type="text" name="login" id="login">
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
        <input id="submit" type="submit" value="Login">
        <?php
        if (!(empty($_GET["error"]))) {
            $message = '<p id="error">%s</p>';
            switch ($_GET["error"]) {
                case 1:
                    echo sprintf($message, htmlspecialchars("Login ou mot de passe incorrect"));
                    break;
                case 2:
                    echo sprintf($message, htmlspecialchars("Veuillez remplir tous les champs"));
                    break;
                default:
                    break;
            }
        }   
        ?>
    </form>
    </main>
</body>
</html>