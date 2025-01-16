<?php

require_once 'php/autoloader.php';
// require_once "./php/Data/DBconnector.php";
Autoloader::register();

use Components\Form\Checkbox;
use Components\Form\Radiobox;
use Components\Form\TextField;
use Tools\User\UserTools;
use Components\Question\Question;
// use Components\Question\QuestionCheckBox;
// use Components\Question\QuestionRadioBox;
// use Components\Question\QuestionTextField;
use Data\JSONprovider;
use Components\Question\Quizz;
use Components\Form\QuestionForm;
use Data\DBConnector;

session_start();
// echo hash('sha256', 'ADMIN');
// echo '<pre>';
// var_dump($_SESSION['quizzs']);
// echo '</pre>';
// lecture du fichier ou son entreposer toutes les questions et quizzs
JSONprovider::clearSession();
JSONprovider::loadQuestions(JSONprovider::loadJSON('data/json/global/questions.json'), true);
JSONprovider::loadQuizzs(JSONprovider::loadJSON('data/json/global/quizz.json'), true);

// fonction pour charger le formulaire du quizz
function loadQuizz() {
    $html = '<div class="quizz">';
    $html .=  "<h2>Quizz: </h2>";
    $form = new Quizz(null, 'Quizz');
    foreach ($_SESSION['questions'] as $question) {
        $form->addQuestion($question);
    }                                                                                                                                              
    $action = 'reponse.php';
    $html .= $form->render($action, 'GET') . '</div>';
    echo $html;
}

function loadSessionQuizz(string $uuid) {
    $html = '<div class="quizz">';
    $html .=  "<h2>Quizz: </h2>";
    $form = $uuid;
    foreach ($_SESSION['quizzs'] as $quizz) {
        if ($quizz->getUuid() == $uuid) {
            $form = $quizz;
            break;
        }
    }                                                                                                                                      
    $action = 'reponse.php';
    $html .= $form->render($action, 'GET') . '</div>';
    echo $html;
}
function renderImportForm() {
    if (empty($_GET['type'])) {
        return;
    } else {
        $html = '<div class="import">';
        $html .=  "<h2>Import: ". $_GET['type']."</h2>";
        $html .= '<form action="index.php" method="POST" enctype="multipart/form-data">';
        $html .= '<input type="hidden" name="action" value="'.$_GET['type'].'">';
        $html .= '<input type="file" name="file" id="file">';
        $html .= '<button type="submit">Submit</button>';
        $html .= '</form>'; 
        $html .= '</div>';
        echo $html;
        echo "<button id=hide onclick='handleAction()'>back</button>";
    }
    
}
 
// inspiration d'une fonction en ligne pour l'import de fichier
// je me suis limite qu'aux infos necessaire: nom, 
if (!empty($_FILES['file']) && $_FILE['file']['error'] == UPLOAD_ERR_OK) {
    $path = $_FILES['file']['tmp_name'];
    $name = $_FILES['file']['name'];
    $extention = strtolower(end(explode('.', $name)));
    $allowedExtention = ['json']; //extension autorise
    if (in_array($extention, $allowedExtention)) {
        $upDir = './uploaded_files/';
        $dest_path = $upDir . $name;
        echo $dest_path;
        if (move_uploaded_file($path, $dest_path)) {
            if ($_POST['action'] == 'questions') {
                echo 'action questions';
                $data = JSONprovider::loadJSON($dest_path);
                $questions = JSONprovider::loadQuestions($data, true);
                JSONprovider::saveQuestionJSON();
                echo 'Question imported';
            } else if ($_POST['action'] == 'quizzs') {
                echo 'action quizzs';
                $data = JSONprovider::loadJSON($dest_path);
                //$quizzs = JSONprovider::loadQuizzs($data, true);
                foreach ($data as $qcm){
                    insertQCM($qcm['uuid'], $qcm['label'], count($qcm['questions']), $qcm['questions']);
                }
                ///JSONprovider::saveQuizzJSON();
                JSONprovider::saveJSON();
                echo 'Quizz imported';
            }
        } else {
            echo "Erreur d'import durant le deplacement du fichier";
        }
    } else {
        echo 'Externtion autorise: ' . implode(', ', $allowedExtention);
    }
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
    <script>
        function gepathAndQuery() {
            const url = new URL(window.location.href);
            const pathAndQuery = url.pathname + url.search;
            return pathAndQuery.startsWith('/') ? pathAndQuery.substring(1) : pathAndQuery;

        }

        function handleAction() {
            switch (gepathAndQuery()) {
                case 'index.php':
                    window.location.href = "index.php?action=quizz";
                    break;
                case 'index.php?action=quizz':
                    window.location.href = "index.php";
                    break;
                case 'index.php?action=import':
                    window.location.href = "index.php";
                    break;
                default:
                    window.location.href = "index.php";
                    break;
            }
        }
    </script>
    <title>The Quizzer</title>
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
<main>
    <h1>Bienvenu dans le Quizzer</h1>
    <p>Tester vos connaissances</p>
    <?php
        if (UserTools::isLogged()) { 
            $html =  "<center><h3> Bienvenu " . $_SESSION['user']['username'] . "</h3></center>";
            echo $html;
            // echo '=====================================================================================================<br>'; 
            // echo "logged as" . ' ' . $_SESSION['user']['username'] . '<br>';
            // echo "debug user: ";
            // $debug = var_dump($_SESSION['user']);
            // echo '=====================================================================================================<br><br>';
        }
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'quizz' && isset($_GET['uuid']):
                    loadSessionQuizz($_GET['uuid']);
                    echo "<button id=hide onclick='handleAction()'>Hide Quizz</button>";
                    break;
                case 'import' && isset($_GET['type']):
                    renderImportForm();
                    break;
                case 'import':
                    $html = '<div class="import">';
                    $html .= '<button class=import-button onclick="window.location.href=\'index.php?action=import&type=questions\'">Import questions</button>';
                    $html .= '<button class=import-button onclick="window.location.href=\'index.php?action=import&type=quizzs\'">Import quizz</button>';
                    $html .= '</div>';
                    echo $html;
                    echo "<button id=hide onclick='handleAction()'>back</button>";
                    break;
                
                default:
                    break;
            }
        }else {
            $button = "<div class='button-container'>";
            foreach ($_SESSION['quizzs'] as $quizz) {
                $button .= "<button id='startButton' onClick='window.location.href=\"index.php?action=quizz&uuid=" . $quizz->getUuid() . "\"'>Repondre au quizz: " . $quizz->getLabel() . "</button>";
            }
            $button .= "</div>";
            echo $button;
        }
    ?>
</main>
</body>
</html>

