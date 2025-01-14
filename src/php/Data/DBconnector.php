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

use \PDO;

class DBConnector {
    private $pdo;
    public function __construct() {
        $this->pdo = new PDO('sqlite:db.sqlite');
        // $this->pdo = new PDO("mysql:host=servinfo-maria;dbname=DBrandriantsoa","randriantsoa","randriantsoa");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }
    public function query($query) {
        return $this->pdo->query($query);
    }

    // Fonction pour obtenir les produits
    //SELECT
    // public function getProducts(): PDOStatement {
    //     return $this->pdo->query('SELECT * FROM PRODUCTS');
    // }
    public function getUSER($username, $mdp){
        $stmt = $this->pdo->prepare('SELECT * FROM USER WHERE username = :username AND motDePasse = :motDePasse');
        $stmt->execute(['username' => $username, 'motDePasse' => $mdp]);
        if ($stmt->fetch()) {
            UserUserTools::login($username, $mdp);
        }
    }

    public function getROLE($username,$mdp){
        $stmt = $this->pdo->prepare('SELECT nomRole FROM USER NATURAL JOIN USER_ROLES NATURAL JOIN ROLES WHERE username = :username AND motDePasse = :motDePasse');
        $stmt->execute(['username' => $username, 'motDePasse' => $mdp]);
        return $stmt->fetch()['nomRole'];
    }

    public function getQCM(){
        $stmt = $this->pdo->query('SELECT * FROM QCM');
        foreach ($stmt as $row) {
            $qcm = new Quizz($row['uuid'], $row['nomQCM'], $row['nombreQuestion']);
            $stmt2 = $this->pdo->prepare('SELECT * FROM QUESTION WHERE qcmId = :qcmId');
            $stmt2->execute(['qcmId' => $row['id']]);
            foreach ($stmt2 as $row2) {
                $stmt3 = $this->pdo->prepare('SELECT * FROM ANSWER WHERE questionID = :questionID');
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
    // public function insertProduct(Product $product) {
    //     $stmt = $this->pdo->prepare('INSERT INTO PRODUCT (name, quantity, price) VALUES (:name, :quantity, :price)');
    //     $stmt->execute(['name' => $product->getName(), 'quantity' => $product->getQuantity(), 'price' => $product->getPrice()]);
    // }

    public function insertUSER($username, $motDePasse){
        $sql = 'SELECT max(userId,0) as id FROM USER';
        $stmt = $this->pdo->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = $this->pdo->prepare('INSERT INTO USER (userId, username, motDePasse) VALUES (:id, :username, :motDePasse)');
        $stmt->execute(['id' => $id, 'username' => $username, 'motDePasse' => $motDePasse]);
    }

    public function insertROLES($nomRole){
        $sql = 'SELECT max(roleId,0) as id FROM ROLES';
        $stmt = $this->pdo->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = $this->pdo->prepare('INSERT INTO ROLES (roleId, nomRole) VALUES (:id, :nomRole)');
        $stmt->execute(['id' => $id, 'nomRole' => $nomRole]);
    }

    public function insertUSER_ROLES($id_user, $idRole){
        $stmt = $this->pdo->prepare('INSERT INTO USER_ROLES (userId, roleId) VALUES (:id, :idRole)');
        $stmt->execute(['id' => $id_user, 'idRole' => $idRole]);
    }

    public function insertANSWER($questionID, $answer, $correct){
        $sql = 'SELECT max(id,0) as id FROM ANSWER';
        $stmt = $this->pdo->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = $this->pdo->prepare('INSERT INTO ANSWER (id, questionID, answer, correct) VALUES (:id, :questionID, :answer, :correct)');
        $stmt->execute(['id' => $id, 'questionID' => $questionID, 'answer' => $answer, 'correct' => $correct]);
    }

    public function insertQUESTION($qcmid, $uuid, $label, $type,$choix ,$correct){
        $sql = 'SELECT max(id,0) as id FROM QUESTION';
        $stmt = $this->pdo->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = $this->pdo->prepare('INSERT INTO QUESTION (id, qcmId, uuid, label, type) VALUES (:id, :qcmId, :uuid, :label, :type)');
        $stmt->execute(['id' => $id, 'qcmID' => $qcmid, 'uuid' => $uuid ,'label' => $label, 'type' => $type]);
        if ($type == 'radio' || $type == 'checkbox') {
            foreach ($choix as $choix) {
                if ($correct === $choix) {
                    $this->insertANSWER($id, $choix, true);
                } else {
                    $this->insertANSWER($id, $choix, false);
                }
            }
        }
        else {
            $this->insertANSWER($id, $correct, true);
        }
    }

    public function insertQCM($uuid, $nomQCM, $nombreQuestion,$listeQuestion){
        $sql = 'SELECT max(id,0) as id FROM QCM';
        $stmt = $this->pdo->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = $this->pdo->prepare('INSERT INTO QCM (id, uuid, nomQCM, nombreQuestion) VALUES (:id, :uuid, :nomQCM, :nombreQuestion)');
        $stmt->execute(['id' => $id, 'uuid' => $uuid, 'nomQCM' => $nomQCM, 'nombreQuestion' => $nombreQuestion]);
        foreach ($listeQuestion as $question) {
            $this->insertQUESTION($question['uuid'], $id, $question['label'], $question['type'],$question['choices'], $question['correct']);
        }
    }

    public function insertTENTATIVE ($id_user, $qcmID, $score){
        $sql = 'SELECT max(qcmId,0) as id FROM TENTATIVE';
        $stmt = $this->pdo->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = $this->pdo->prepare('INSERT INTO TENTATIVE (qcmId, userId, qcmID, score) VALUES (:id, :id_user, :qcmID, :score)');
        $stmt->execute(['id' => $id, 'id_user' => $id_user, 'qcmID' => $qcmID, 'score' => $score]);
    }

    //UPDATE
    public function updateUSER($id, $username, $motDePasse){
        $stmt = $this->pdo->prepare('UPDATE USER SET username = :username, motDePasse = :motDePasse WHERE id = :id');
        $stmt->execute(['id' => $id, 'username' => $username, 'motDePasse' => $motDePasse]);
    }

    public function updateROLES($id, $nomRole){
        $stmt = $this->pdo->prepare('UPDATE USER_ROLES SET roleId = :nomRole WHERE userId = :id');
        $stmt->execute(['id' => $id, 'nomRole' => $nomRole]);
    }

}