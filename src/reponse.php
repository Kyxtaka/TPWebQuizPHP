<?php
require_once 'php/autoloader.php';
Autoloader::register();
use Php\Component\Question\Question;
session_start();
if (!isset($_SESSION['questions'])) {
    header('Location: index.php?error=1');
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/navbar.css">
    <title>Réponse</title>
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
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin'):?>
                    <li><a href="?action=import">Import</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Déconnexion</a></li>
            <?php else: ?>
                <li id="loginButton"><a href="login.php">Login</a></li>
                <li id="registerButton"><a href="register.php">Register</a></li>
            <?php endif; ?>
           
        </ul>
    </nav>
</header>
    <div class="answer-container" id="qcm-correction">
    <h2>Voici les réponse correct: </h2>
    <ul>
        <?php
        foreach ($_GET as $key => $value) {
            foreach ($_SESSION['questions'] as $question) {
                if ($question->getUuid() == $key) {
                    echo '<li>' . $question->getLabel() . ' : ' . $question->getAnswer() . '</li>';
                }
            }
        }
        ?>
    </ul>
    </div>
    <div class="answer-container" id="qcm-user">
    <h2>Voici vos réponses: </h2>
    <ul>
        <?php
        foreach ($_GET as $key => $value) {
            foreach ($_SESSION['questions'] as $question) {
                if ($question->getUuid() == $key) {
                    echo '<li>' . $question->getLabel() . ' : ' . $value . '</li>';
                }
            }
        }
        ?>
    </ul>
    <?php
    if (empty($_GET)) {
        echo "Vous ne pouvez pas accéder à cette page si vous n'avez pas soumis le formulaire" . '<br>';
        echo '<pre>' . print_r($_GET) . '</pre>';
        echo '<pre>' . print_r($_SESSION['questions']) . '</pre>';
    }
    ?>
</body>
</html>