<?php
// Connection en utlisant la connexion PDO avec le moteur en prefix

namespace Data;
use Components\Question\Question;
use Components\Question\QuestionCheckBox;
use Components\Question\QuestionRadioBox;
use Components\Question\QuestionTextField;
use Components\Question\Quizz;
use Components\Tools\User\UserTools;
use Tools\User\UserTools as UserUserTools;
use \PDOException;
use \PDO;


// Implementation d'un DB connector avec la pattern Singleton pour la connexion à la base de données
// Permet d'instancier une seule fois la connexion à la base de données
class DBconnector {
    public static $instance = null;

    private function __construct() {
        try{
            self::$instance = new PDO("sqlite:./data/db/database.db");
            // self::getInstance() = new PDO("mysql:host=servinfo-maria;dbname=DBrandriantsoa","randriantsoa","randriantsoa");
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            echo 'Connexion échouée : ' . $e->getMessage();
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            new DBconnector();
        }
        return self::$instance;
    }

    public static function checkDB($username, $hashedPassword){
        $stmt = self::getInstance()->prepare('SELECT * FROM USERS NATURAL JOIN USER_ROLES NATURAL JOIN ROLES WHERE username = :username AND motDePasse = :motDePasse ');
        $stmt->execute(['username' => $username, 'motDePasse' => $hashedPassword]);
        return $stmt->fetch();
    }

    public static function getUSER($username){
        $stmt = self::getInstance()->prepare('SELECT * FROM NATURAL JOIN USER_ROLES NATURAL JOIN ROLES WHERE username = :username');
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    public static function insertUSER($username, $motDePasse){
        $sql = 'SELECT max(userId,0) as id FROM USER';
        $stmt = self::getInstance()->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = self::getInstance()->prepare('INSERT INTO USER (userId, username, motDePasse) VALUES (:id, :username, :motDePasse)');
        $stmt->execute(['id' => $id, 'username' => $username, 'motDePasse' => $motDePasse]);
    }
    
    public static function insertROLES($nomRole){
        $sql = 'SELECT max(roleId,0) as id FROM ROLES';
        $stmt = self::getInstance()->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = self::getInstance()->prepare('INSERT INTO ROLES (roleId, nomRole) VALUES (:id, :nomRole)');
        $stmt->execute(['id' => $id, 'nomRole' => $nomRole]);
    }
    
    public static function insertUSER_ROLES($id_user, $idRole){
        $stmt = self::getInstance()->prepare('INSERT INTO USER_ROLES (userId, roleId) VALUES (:id, :idRole)');
        $stmt->execute(['id' => $id_user, 'idRole' => $idRole]);
    }

    public static function registerUser($username, $hashedPassword, $role){
        try {
            self::insertUSER($username, $hashedPassword);
            $user = self::getUSER($username);
            $id_user = $user['userId'];
            $id_role = self::getROLE($role);
            self::insertUSER_ROLES($id_user, $id_role);
            return true;
        } catch (PDOException $e) {
            return false;
        }
        
    } 

    public static function getAllRoles() {
        $stmt = self::getInstance()->query('SELECT * FROM ROLES');
        $res =  $stmt->fetchAll();
        return $res;
    }

    public static function getTentativeNumber($id_user, $qcmUID){
        $stmt = self::getInstance()->prepare('SELECT count(*) as number FROM QCMTENTATIVE WHERE userId = :id_user AND qcmUID = :qcmUID');
        $stmt->execute(['id_user' => $id_user, 'qcmUID' => $qcmUID]);
        return $stmt->fetch()['number'];
    }

    public static function insertTENTATIVE ($qcmUID, $id_user, $score, $tentativeNumber){
        $stmt = self::getInstance()->prepare('INSERT INTO QCMTENTATIVE (qcmUID, userId, score, num) VALUES (:qcmUID, :id_user, :score, :tentativeNumber)');
        $stmt->execute(['qcmUID' => $qcmUID, 'id_user' => $id_user, 'score' => $score, 'tentativeNumber' => $tentativeNumber]);
    }
    

    //UPDATE
    public static function updateUSER($id, $username, $motDePasse){
        $stmt = self::getInstance()->prepare('UPDATE USER SET username = :username, motDePasse = :motDePasse WHERE id = :id');
        $stmt->execute(['id' => $id, 'username' => $username, 'motDePasse' => $motDePasse]);
    }
    
    public static function updateROLES($id, $nomRole){
        $stmt = self::getInstance()->prepare('UPDATE USER_ROLES SET roleId = :nomRole WHERE userId = :id');
        $stmt->execute(['id' => $id, 'nomRole' => $nomRole]);
    }

    public static function getTentatives($qcmUID, $userId){
        $stmt = self::getInstance()->prepare('SELECT * FROM QCMTENTATIVE WHERE qcmUID = :qcmUID AND userId = :userId');
        $stmt->execute(['qcmUID' => $qcmUID, 'userId' => $userId]);
        return $stmt->fetchAll();
    }
    







    // Partie abandonnee par soucie de temps

    // public static function getQCM(){
    //     $stmt = self::getInstance()->query('SELECT * FROM QCM');
    //     foreach ($stmt as $row) {
    //         $qcm = new Quizz($row['uuid'], $row['nomQCM'], $row['nombreQuestion']);
    //         $stmt2 = self::getInstance()->prepare('SELECT * FROM QUESTION WHERE qcmUID = :qcmUID');
    //         $stmt2->execute(['qcmUID' => $row['id']]);
    //         foreach ($stmt2 as $row2) {
    //             $stmt3 = self::getInstance()->prepare('SELECT * FROM ANSWER WHERE questionID = :questionID');
    //             $stmt3->execute(['questionID' => $row2['id']]);
    //             $choices = [];
    //             foreach ($stmt3 as $row3) {
    //                 $choices[] = $row3['answer'];
    //                 if ($row3['correct'] == 1) {
    //                     $correct = $row3['answer'];
    //                 }
    //             }
    //             switch ($row2['type']) {
    //                 case 'radio':
    //                     $question = new QuestionRadioBox($row2['label'],$row2['radio'],$correct,$row2['uuid']);
    //                     foreach($choices as $choice){
    //                         $question->addChoice($choice);
    //                     }
    //                     $qcm->addQuestion($question);
    //                     break;
    //                 case 'checkbox':
    //                     $question = new QuestionCheckBox($row2['label'],$row2['checkbox'],$correct,$row2['uuid']);
    //                     foreach($choices as $choice){
    //                         $question->addChoice($choice);
    //                     }
    //                     $qcm->addQuestion($question);
    //                     break;
    //                 case 'text':
    //                     $question = new QuestionTextField($row2['label'],$row2['text'],$correct,$row2['uuid']);
    //                     $qcm->addQuestion($question);
    //                     break;
    //             }      
    //         }
    //     }
    //     JSONprovider::saveQuizzToSession($qcm);
    // }
    
    //INSERT
    // public static function insertProduct(Product $product) {
    //     $stmt = self::getInstance()->prepare('INSERT INTO PRODUCT (name, quantity, price) VALUES (:name, :quantity, :price)');
    //     $stmt->execute(['name' => $product->getName(), 'quantity' => $product->getQuantity(), 'price' => $product->getPrice()]);
    // }
    
    // public static function insertANSWER($questionID, $answer, $correct){
    //     $sql = 'SELECT max(id,0) as id FROM ANSWER';
    //     $stmt = self::getInstance()->query($sql);
    //     $id = $stmt->fetch()['id'] + 1;
    //     $stmt = self::getInstance()->prepare('INSERT INTO ANSWER (id, questionID, answer, correct) VALUES (:id, :questionID, :answer, :correct)');
    //     $stmt->execute(['id' => $id, 'questionID' => $questionID, 'answer' => $answer, 'correct' => $correct]);
    // }
    
    // public static function insertQUESTION($qcmUID, $uuid, $label, $type,$choix ,$correct){
    //     $sql = 'SELECT max(id,0) as id FROM QUESTION';
    //     $stmt = self::getInstance()->query($sql);
    //     $id = $stmt->fetch()['id'] + 1;
    //     $stmt = self::getInstance()->prepare('INSERT INTO QUESTION (id, qcmUID, uuid, label, type) VALUES (:id, :qcmUID, :uuid, :label, :type)');
    //     $stmt->execute(['id' => $id, 'qcmUID' => $qcmUID, 'uuid' => $uuid ,'label' => $label, 'type' => $type]);
    //     if ($type == 'radio' || $type == 'checkbox') {
    //         foreach ($choix as $choix) {
    //             if ($correct === $choix) {
    //                 insertANSWER($id, $choix, true);
    //             } else {
    //                 insertANSWER($id, $choix, false);
    //             }
    //         }
    //     }
    //     else {
    //         insertANSWER($id, $correct, true);
    //     }
    // }
    
    // public static function insertQCM($uuid, $nomQCM, $nombreQuestion,$listeQuestion){
    //     $sql = 'SELECT max(id,0) as id FROM QCM';
    //     $stmt = self::getInstance()->query($sql);
    //     $id = $stmt->fetch()['id'] + 1;
    //     $stmt = self::getInstance()->prepare('INSERT INTO QCM (id, uuid, nomQCM, nombreQuestion) VALUES (:id, :uuid, :nomQCM, :nombreQuestion)');
    //     $stmt->execute(['id' => $id, 'uuid' => $uuid, 'nomQCM' => $nomQCM, 'nombreQuestion' => $nombreQuestion]);
    //     foreach ($listeQuestion as $question) {
    //         insertQUESTION($question['uuid'], $id, $question['label'], $question['type'],$question['choices'], $question['correct']);
    //     }
    // }

}
?>
