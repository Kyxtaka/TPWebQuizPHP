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
    <title>Réponse</title>
</head>
<body>
    <h2>Voici les réponse correct: </h2>
    <?php
    if (empty($_GET) || empty($_POST)) {
       echo "Vous ne pouvez pas accéder à cette page si vous n'avez pas soumis le formulaire" . '<br>';
       print_r($_GET);
       print_r($_SESSION['questions']);
    }
    ?>
</body>
</html>