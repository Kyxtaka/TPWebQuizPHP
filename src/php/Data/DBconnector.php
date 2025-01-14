<?php
// Connection en utlisant la connexion PDO avec le moteur en prefix

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

    //INSERT
    // public function insertProduct(Product $product) {
    //     $stmt = $this->pdo->prepare('INSERT INTO PRODUCT (name, quantity, price) VALUES (:name, :quantity, :price)');
    //     $stmt->execute(['name' => $product->getName(), 'quantity' => $product->getQuantity(), 'price' => $product->getPrice()]);
    // }

    public function insertUSER($username, $password){
        $sql = 'SELECT max(id,0) as id FROM USER';
        $stmt = $this->pdo->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = $this->pdo->prepare('INSERT INTO USER (id, username, password) VALUES (:id, :username, :password)');
        $stmt->execute(['id' => $id, 'username' => $username, 'password' => $password]);
    }

    public function insertROLES($nomRole){
        $sql = 'SELECT max(id,0) as id FROM ROLES';
        $stmt = $this->pdo->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = $this->pdo->prepare('INSERT INTO ROLES (id, nomRole) VALUES (:id, :nomRole)');
        $stmt->execute(['id' => $id, 'nomRole' => $nomRole]);
    }

    public function insertUSER_ROLES($id_user, $idRole){
        $stmt = $this->pdo->prepare('INSERT INTO USER_ROLES (id, idRole) VALUES (:id, :idRole)');
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
        $sql = 'SELECT max(id,0) as id FROM TENTATIVE';
        $stmt = $this->pdo->query($sql);
        $id = $stmt->fetch()['id'] + 1;
        $stmt = $this->pdo->prepare('INSERT INTO TENTATIVE (id, id_user, qcmID, score) VALUES (:id, :id_user, :qcmID, :score)');
        $stmt->execute(['id' => $id, 'id_user' => $id_user, 'qcmID' => $qcmID, 'score' => $score]);
    }

    //UPDATE
    public function updateUSER($id, $username, $password){
        $stmt = $this->pdo->prepare('UPDATE USER SET username = :username, password = :password WHERE id = :id');
        $stmt->execute(['id' => $id, 'username' => $username, 'password' => $password]);
    }

    public function updateROLES($id, $nomRole){
        $stmt = $this->pdo->prepare('UPDATE ROLES SET nomRole = :nomRole WHERE id = :id');
        $stmt->execute(['id' => $id, 'nomRole' => $nomRole]);
    }

}