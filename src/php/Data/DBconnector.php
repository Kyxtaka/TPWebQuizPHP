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
            self::$instance = new PDO("sqlite:./data/db/database.sqlite");
            // self::getInstance() = new PDO("mysql:host=servinfo-maria;dbname=DBrandriantsoa","randriantsoa","randriantsoa");
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $e) {
            echo 'Connexion échouée : ' . $e->getMessage();
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DBconnector();
        }
        return self::$instance;
    }

    // public static function query($query) {
    //     return self::getInstance()->query($query);
    // }

    // Fonction pour obtenir les produits
    //SELECT
    // public static function getProducts(): PDOStatement {
    //     return self::getInstance()->query('SELECT * FROM PRODUCTS');
    // }
    public static function getUSER($username, $mdp){
        $stmt = self::getInstance()->prepare('SELECT * FROM USER WHERE username = :username AND motDePasse = :motDePasse');
        $stmt->execute(['username' => $username, 'motDePasse' => $mdp]);
        if ($stmt->fetch()) {
            UserUserTools::login($username, $mdp);
        }
    }

    public static function getROLE($username,$mdp){
        
        $stmt = self::getInstance()->prepare('SELECT nomRole FROM USER NATURAL JOIN USER_ROLES NATURAL JOIN ROLES WHERE username = :username AND motDePasse = :motDePasse');
        $stmt->execute(['username' => $username, 'motDePasse' => $mdp]);
        return $stmt->fetch()['nomRole'];
    }

    public static function getAllRoles() {
        $stmt = self::getInstance()->query('SELECT * FROM ROLES');
        $res =  $stmt->fetchAll();
        return $res;
    }
    
    public static function getQCM(){
        
        $stmt = self::getInstance()->query('SELECT * FROM QCM');
        foreach ($stmt as $row) {
            $qcm = new Quizz($row['uuid'], $row['nomQCM'], $row['nombreQuestion']);
            $stmt2 = self::getInstance()->prepare('SELECT * FROM QUESTION WHERE qcmId = :qcmId');
            $stmt2->execute(['qcmId' => $row['id']]);
            foreach ($stmt2 as $row2) {
                $stmt3 = self::getInstance()->prepare('SELECT * FROM ANSWER WHERE questionID = :questionID');
                $stmt3->execute(['questionID' => $row2['id']]);
                $choices = [];
                foreach ($stmt3 as $row3) {
                    $choices[] = $row3['answer'];
                    if ($row3['correct'] == 1) {
                        $correct = $row3['answer'];
                    }
                }
                switch ($row2['type']) {
                    case 'radio':
                        $question = new QuestionRadioBox($row2['label'],$row2['radio'],$correct,$row2['uuid']);
                        foreach($choices as $choice){
                            $question->addChoice($choice);
                        }
                        $qcm->addQuestion($question);
                        break;
                    case 'checkbox':
                        $question = new QuestionCheckBox($row2['label'],$row2['checkbox'],$correct,$row2['uuid']);
                        foreach($choices as $choice){
                            $question->addChoice($choice);
                        }
                        $qcm->addQuestion($question);
                        break;
                    case 'text':
                        $question = new QuestionTextField($row2['label'],$row2['text'],$correct,$row2['uuid']);
                        $qcm->addQuestion($question);
                        break;
                }
                
            }
        }
        JSONprovider::saveQuizzToSession($qcm);
    
    }
    
    //INSERT
    // public static function insertProduct(Product $product) {
    //     $stmt = self::getInstance()->prepare('INSERT INTO PRODUCT (name, quantity, price) VALUES (:name, :quantity, :price)');
    //     $stmt->execute(['name' => $product->getName(), 'quantity' => $product->getQuantity(), 'price' => $product->getPrice()]);
    // }
    
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
    
    public static function insertANSWER($questionID, $answer, $correct){
        
        $sql = 'SELECT max(id,0) as id FROM ANSWER';
        $stmt = self::getInstance()->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = self::getInstance()->prepare('INSERT INTO ANSWER (id, questionID, answer, correct) VALUES (:id, :questionID, :answer, :correct)');
        $stmt->execute(['id' => $id, 'questionID' => $questionID, 'answer' => $answer, 'correct' => $correct]);
    }
    
    public static function insertQUESTION($qcmid, $uuid, $label, $type,$choix ,$correct){
        
        $sql = 'SELECT max(id,0) as id FROM QUESTION';
        $stmt = self::getInstance()->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = self::getInstance()->prepare('INSERT INTO QUESTION (id, qcmId, uuid, label, type) VALUES (:id, :qcmId, :uuid, :label, :type)');
        $stmt->execute(['id' => $id, 'qcmID' => $qcmid, 'uuid' => $uuid ,'label' => $label, 'type' => $type]);
        if ($type == 'radio' || $type == 'checkbox') {
            foreach ($choix as $choix) {
                if ($correct === $choix) {
                    DBconnector::insertANSWER($id, $choix, true);
                } else {
                    DBconnector::insertANSWER($id, $choix, false);
                }
            }
        }
        else {
            DBconnector::insertANSWER($id, $correct, true);
        }
    }
    
    public static function insertQCM($uuid, $nomQCM, $nombreQuestion,$listeQuestion){
        
        $sql = 'SELECT max(id,0) as id FROM QCM';
        $stmt = self::getInstance()->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = self::getInstance()->prepare('INSERT INTO QCM (id, uuid, nomQCM, nombreQuestion) VALUES (:id, :uuid, :nomQCM, :nombreQuestion)');
        $stmt->execute(['id' => $id, 'uuid' => $uuid, 'nomQCM' => $nomQCM, 'nombreQuestion' => $nombreQuestion]);
        foreach ($listeQuestion as $question) {
            DBconnector::insertQUESTION($question['uuid'], $id, $question['label'], $question['type'],$question['choices'], $question['correct']);
        }
    }
    
    public static function insertTENTATIVE ($id_user, $qcmID, $score){
        
        $sql = 'SELECT max(qcmId,0) as id FROM TENTATIVE';
        $stmt = self::getInstance()->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = self::getInstance()->prepare('INSERT INTO TENTATIVE (qcmId, userId, qcmID, score) VALUES (:id, :id_user, :qcmID, :score)');
        $stmt->execute(['id' => $id, 'id_user' => $id_user, 'qcmID' => $qcmID, 'score' => $score]);
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

}
?>
