<?php
require_once 'php/autoloader.php';
Autoloader::register();
use Tools\UserTools;
$disconnect = UserTools::logout();
session_destroy();
session_start();
$status = UserTools::isLogged();
if ($status == false) {
    header('Location: index.php');
}else {
    echo "Erreur de déconnexion";
}
?>