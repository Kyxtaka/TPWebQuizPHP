<?php
namespace Tools\User;
use Data\DBconnector;
session_start();
// classe donnant des outils pour la gestion des utilisateurs
//connexion / deconnexion / verification de connexion
class UserTools {

    private static function checkDB($username, $password) {
        $result = DBconnector::checkDB($username, $password);
        return $result;
    }

    public static function login($username, $password) {
        $hash = hash('sha256', $password);
        if ($result = self::checkDB($username, $hash)) {
            $_SESSION['user'] = array('userId' => $result['userId'] , 'username' => $username, 'token' => self::generateToken(), 'role' => $result['nomRole']);
            return true;
        }
        return false;
    }

    public static function register($username, $password, $confirmationPassword, $role = "USER") {
        if ($password !== $confirmationPassword) {
            return false;
        }
        $hash = hash('sha256', $password);
        $case = DBconnector::registerUser($username, $hash, $role);
        return $case;
    }
 
    public static function generateToken() {
        $token = bin2hex(random_bytes(32));
        setcookie('token', $token, time() + 10);
        return $token;
    }

    public static function checkTokenValidity($token) {
        $validity = true;
        if (empty($_COOKIE['token'])) {
            $validity = false;
        }else if ($token !== $_COOKIE['token']) {
            $validity = false;
        }
        return $validity;
    }

    public static function logout() {
        unset($_SESSION['user']);
    }

    public static function isLogged() {
        return isset($_SESSION['user']);
    }

    public static function requireLogin($query = 'index.php') {
        if (!self::isLogged()) {
            header('Location: ' . $query);
            exit();
        }
    }

    public static function getUserToken() {
        return $_SESSION['user']['token'];
    }

    public static function getUserRole() {
        return $_SESSION['user']['role'];
    }

    public static function getUserId() {
        return $_SESSION['user']['userId'];
    }
}
?>