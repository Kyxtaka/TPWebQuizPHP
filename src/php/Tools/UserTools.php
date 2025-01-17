<?php
namespace Tools;
use Data\DBconnector; // Importation de la classe DBconnector
session_start();

/** 
 * Librairie de gestion des utilisateurs 
 * Auteur : Nathan Randriantsoa
 * Derniere MAJ : 17/01/2025
 * Utilisation : TP / TD / SAE / Projets personnels 
 * Doit etre utilisee avec la classe DBconnector
 */
class UserTools {

    /**
     * Fonction permettant de creer une session utilisateur
     * @param array $atrs Les attributs de l'utilisateur
     */
    public static function createSession(array $atrrs) {$_SESSION['user'] = $atrrs;}

    /**
     * Fonction permettant de verifier si un utilisateur est present dans la base de donnees
     * @param string $username Le nom d'utilisateur
     */
    private static function checkDB($username, $password) {
        $result = DBconnector::checkDB($username, $password);
        return $result;
    }

    /**
     * Fonction permettant de verifier si un utilisateur est present dans la base de donnees
     * @param string $username Le nom d'utilisateur
     * @param string $password Le mot de passe
     * @return boolean true si l'utilisateur est present dans la base de donnees, false sinon
     */
    public static function login($username, $password) {
        $hash = hash('sha256', $password);
        if ($result = self::checkDB($username, $hash)) {
            $attrs = array(
                'userId' => $result['userId'], 
                'username' => $username, 
                'token' => self::generateToken(), 
                'role' => $result['nomRole']
            );
            self::createSession($attrs);
            return true;
        }
        return false;
    }

    /**
     * Fonction permettant d'enregistrer un utilisateur dans la base de donnees
     * @param string $username Le nom d'utilisateur
     * @param string $password Le mot de passe
     * @param string $confirmationPassword La confirmation du mot de passe
     * @param string $role Le role de l'utilisateur
     * @return boolean true si l'utilisateur a ete enregistre, false sinon
     */
    public static function register($username, $password, $confirmationPassword, $role = "USER") {
        if ($password !== $confirmationPassword) { return false;}
        $hash = hash('sha256', $password);
        $case = DBconnector::registerUser($username, $hash, $role);
        return $case;
    }
 
    /**
     * Fonction permettant de generer un token (32 octets) de 64 caracteres aleatoires
     * @return string Le token genere
     */
    public static function generateToken() {
        $token = bin2hex(random_bytes(32));
        setcookie('token', $token, time() + 10);
        return $token;
    }

    /**
     * Fonction permettant de verifier la validite d'un token
     * @param string $token Le token a verifier
     * @param boolean $removeIfInvalid true si le token doit etre supprime s'il est invalide, false sinon
     * @return boolean true si le token est valide, false sinon
     */
    public static function checkTokenValidity($token, $removeIfInvalid = false) {
        $validity = true;
        if (empty($_COOKIE['token'])) {
            $validity = false;
            if ($removeIfInvalid) $remove = true;
        }else if ($token !== $_COOKIE['token']) {
            $validity = false;
            if ($removeIfInvalid) $remove = true;
        }
        if ($remove && $removeIfInvalid) {
            unset($_COOKIE['token']);
            self::logout();
        }
        return $validity;
    }

    /**
     * Fonction permettant de deconnecter un utilisateur
     */
    public static function logout() { unset($_SESSION['user']);}

    /**
     * Fonction permettant de verifier si un utilisateur est connecte
    *  @param boolean $checkTokenValidity true si le token doit etre verifie, false sinon
     * @return boolean true si l'utilisateur est connecte, false sinon
     */
    public static function isLogged($checkTokenValidity = false) {
        if ($checkTokenValidity) { return isset($_SESSION['user']) && self::checkTokenValidity($_SESSION['user']['token']);}
        else {return isset($_SESSION['user']);}
    }

    /**
     * Fonction permettant de rediriger un utilisateur vers une page de connexion
     * @param string $query La page de redirection
     */
    public static function requireLogin($query = 'index.php') {
        if (!self::isLogged()) {
            header('Location: ' . $query);
            exit();
        }
    }

    /**
     * Fonction permettant de recuperer le token de l'utilisateur
     * @param string $query La page de redirection
     */
    public static function getUserToken() {return $_SESSION['user']['token'];}

    /**
     * Fonction permettant de recuperer le role de l'utilisateur
     * @param string $query La page de redirection
     */
    public static function getUserRole() {return $_SESSION['user']['role'];}

    /**
     * Fonction permettant de recuperer l'ID de l'utilisateur
     * @param string $query La page de redirection
     */
    public static function getUserId() {return $_SESSION['user']['userId'];}
}
?>