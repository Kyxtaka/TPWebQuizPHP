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
    // public function addProduct(Product $product) {
    //     $stmt = $this->pdo->prepare('INSERT INTO PRODUCT (name, quantity, price) VALUES (:name, :quantity, :price)');
    //     $stmt->execute(['name' => $product->getName(), 'quantity' => $product->getQuantity(), 'price' => $product->getPrice()]);
    // }


    //UPDATE

}