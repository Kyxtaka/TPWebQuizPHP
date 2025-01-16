<?php
require_once 'php/autoloader.php';
Autoloader::register();
use Component\Question\Quizz;
use Component\Question\Question;
use Tools\User\UserTools;
use Data\DBconnector;
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
    <div class="answer-container" id="qcm-result">
    <h2>Voici vos réponses: </h2>
    <ul>
        <?php
        foreach ($_GET as $key => $value) {
            foreach ($_SESSION['questions'] as $question) {
                if ( $question->getUuid() == $key) {
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
    </div>
    <div class="answer-container" id="qcm-score">
    <?php
    $score = 0;
    foreach ($_SESSION['quizzs'] as $currentQuizz) {
        if ($currentQuizz->getUuid() == $_GET['quizz']) {
            $quizz = $quizz;
        }
    }
    foreach ($_GET as $key => $value) {
        foreach ($_SESSION['questions'] as $question) {
            if ($question->getUuid() == $key) {
                if ($question->getAnswer() == $value) {
                    $score++;
                }
            }
        }
    }
    echo sprintf('<h2>Votre score est de : %d / %d</h2>', $score, count($_GET)-1);
    if (UserTools::isLogged()) {
        try {
            echo 'logged';
            DBconnector::insertTENTATIVE($_GET['quizz'], UserTools::getUserId(), $score, DBconnector::getTentativeNumber($_GET['quizz'], UserTools::getUserId())+1);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
         echo sprintf('<h2>Toutes vos tentatives : </h2>');
        $allTentatives = DBconnector::getTentatives($_GET['quizz'], UserTools::getUserId());
        foreach ($allTentatives as $tentative) {
            echo sprintf('<p>Tentative n°%d : %d / %d</p>', $tentative['numeroTentative'], $tentative['score'], count($_GET)-1);
        }
    }


    ?>
    </div>
</body>
</html>