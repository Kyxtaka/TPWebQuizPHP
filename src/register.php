<?php
session_start();
require_once 'php/autoloader.php';
Autoloader::register();
use Data\DBconnector;
use Tools\User\UserTools;
if (!empty($_POST['username']) && !empty($_POST['pwd']) && !empty($_POST['confirm-pwd'])) {
    if ($_POST['pwd'] !== $_POST['confirm-pwd']) {
        header('Location: register.php?error=3');
    }
    $case = UserTools::register($_POST['username'], $_POST['pwd'], $_POST['confirm-pwd'], "USER");
    var_dump($case);
    if ($case) $status = UserTools::login($_POST['username'], $_POST['pwd']);
    else $status = false;
    var_dump($status);
    if ($status) {
        header('Location: index.php');
    } else {
        header('Location: register.php?error=1');
    }
} else if (!empty($_POST['username']) || !empty($_POST['pwd']) || !empty($_POST['confirm-pwd'])) {
    header('Location: register.php?error=2');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/index.css">
    <script src="js/W3IncludeHTML.js"></script>
    <title>Register</title>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="index.php">A propos</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="index.php">Quiz</a></li>
                    <li><a href="index.php">Résultats</a></li>
                    <li><a href="index.php">Déconnexion</a></li>
                <?php else: ?>
                    <li id="loginButton"><a href="login.php">Login</a></li>
                    <li id="registerButton"><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Register</h1>
        <form action="" method = "POST">
            <label for="username">Username</label>
            <input type="text" name="username" id="username">
            <label for="pwd">Password</label>
            <input type="password" name="pwd" id="pwd">
            <label for="confirm-pwd">Confirm Password</label>
            <input type="password" name="confirm-pwd" id="confirm-pwd">
            <input type="submit" value="Register">
            <?php
            if (!(empty($_GET["error"]))) {
                $message = '<p id="error">%s</p>';
                switch ($_GET["error"]) {
                    case 1:
                        echo sprintf($message, htmlspecialchars("Erreur lors de l'inscription. Username déjà utilisé"));
                        break;
                    case 2:
                        echo sprintf($message, htmlspecialchars("Veuillez remplir tous les champs"));
                        break;
                    case 3:
                        echo sprintf($message, htmlspecialchars("Les mots de passe ne correspondent pas"));
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