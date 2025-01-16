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
            // self::getInstance() = new PDO("mynextId:host=servinfo-maria;dbname=DBrandriantsoa","randriantsoa","randriantsoa");
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            echo 'Connexion échouée : ' . $e->getMessage();
        }
    }

    /**
     * Récupère tous les rôles de la base de données.
     * @return array Un tableau contenant tous les rôles.
     */
    public static function getAllRoles() {
        $stmt = self::getInstance()->query('SELECT * FROM ROLES');
        $res = $stmt->fetchAll();
        return $res;
    }

    /**
     * Récupère le nombre de tentatives pour un utilisateur et un QCM spécifique.
     * @param int $id_user L'ID de l'utilisateur.
     * @param string $qcmUID L'UID du QCM.
     * @return int Le nombre de tentatives.
     */
    public static function getTentativeNumber($id_user, $qcmUID) {
        $stmt = self::getInstance()->prepare('SELECT count(*) as number FROM QCMTENTATIVE WHERE userId = :id_user AND qcmUID = :qcmUID GROUP BY userId');
        $stmt->execute(['id_user' => $id_user, 'qcmUID' => $qcmUID]);
        return $stmt->fetch()['number'];
    }

    /**
     * Insère une nouvelle tentative pour un utilisateur et un QCM spécifique.
     * @param string $qcmUID L'UID du QCM.
     * @param int $id_user L'ID de l'utilisateur.
     * @param float $score Le score de la tentative.
     * @param int $tentativeNumber Le numéro de la tentative.
     */
    public static function insertTENTATIVE($qcmUID, $id_user, $score, $tentativeNumber) {
        $stmt = self::getInstance()->prepare('INSERT INTO QCMTENTATIVE (qcmUID, userId, score, num) VALUES (:qcmUID, :id_user, :score, :tentativeNumber)');
        $stmt->execute(['qcmUID' => $qcmUID, 'id_user' => $id_user, 'score' => $score, 'tentativeNumber' => $tentativeNumber]);
    }

    /**
     * Met à jour les informations d'un utilisateur dans la base de données.
     * @param int $id L'ID de l'utilisateur.
     * @param string $username Le nom d'utilisateur.
     * @param string $motDePasse Le mot de passe.
     */
    public static function updateUSER($id, $username, $motDePasse) {
        $stmt = self::getInstance()->prepare('UPDATE USER SET username = :username, motDePasse = :motDePasse WHERE id = :id');
        $stmt->execute(['id' => $id, 'username' => $username, 'motDePasse' => $motDePasse]);
    }

    /**
     * Met à jour les informations d'un rôle dans la base de données.
     * @param int $id L'ID de l'utilisateur.
     * @param string $nomRole Le nom du rôle.
     */
    public static function updateROLES($id, $nomRole) {
        $stmt = self::getInstance()->prepare('UPDATE USER_ROLES SET roleId = :nomRole WHERE userId = :id');
        $stmt->execute(['id' => $id, 'nomRole' => $nomRole]);
    }

    /**
     * Récupère toutes les tentatives pour un utilisateur et un QCM spécifique.
     * @param string $qcmUID L'UID du QCM.
     * @param int $userId L'ID de l'utilisateur.
     * @return array Un tableau contenant toutes les tentatives.
     */
    public static function getTentatives($qcmUID, $userId) {
        $stmt = self::getInstance()->prepare('SELECT * FROM QCMTENTATIVE WHERE qcmUID = :qcmUID AND userId = :userId');
        $stmt->execute(['qcmUID' => $qcmUID, 'userId' => $userId]);
        return $stmt->fetchAll();
    }
    

    // Partie abandonnee par soucie de temps


     
    // public static function insertROLES($nomRole){
    //     $nextId = 'SELECT max(roleId,0) as id FROM ROLES';
    //     $stmt = self::getInstance()->query($nextId);
    //     $id = $stmt->fetch()['id'] + 1;
    //     $stmt = self::getInstance()->prepare('INSERT INTO ROLES (roleId, nomRole) VALUES (:id, :nomRole)');
    //     $stmt->execute(['id' => $id, 'nomRole' => $nomRole]);
    // }

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
    //     $nextId = 'SELECT max(id,0) as id FROM ANSWER';
    //     $stmt = self::getInstance()->query($nextId);
    //     $id = $stmt->fetch()['id'] + 1;
    //     $stmt = self::getInstance()->prepare('INSERT INTO ANSWER (id, questionID, answer, correct) VALUES (:id, :questionID, :answer, :correct)');
    //     $stmt->execute(['id' => $id, 'questionID' => $questionID, 'answer' => $answer, 'correct' => $correct]);
    // }
    
    // public static function insertQUESTION($qcmUID, $uuid, $label, $type,$choix ,$correct){
    //     $nextId = 'SELECT max(id,0) as id FROM QUESTION';
    //     $stmt = self::getInstance()->query($nextId);
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
    //     $nextId = 'SELECT max(id,0) as id FROM QCM';
    //     $stmt = self::getInstance()->query($nextId);
    //     $id = $stmt->fetch()['id'] + 1;
    //     $stmt = self::getInstance()->prepare('INSERT INTO QCM (id, uuid, nomQCM, nombreQuestion) VALUES (:id, :uuid, :nomQCM, :nombreQuestion)');
    //     $stmt->execute(['id' => $id, 'uuid' => $uuid, 'nomQCM' => $nomQCM, 'nombreQuestion' => $nombreQuestion]);
    //     foreach ($listeQuestion as $question) {
    //         insertQUESTION($question['uuid'], $id, $question['label'], $question['type'],$question['choices'], $question['correct']);
    //     }
    // }

}
?>
