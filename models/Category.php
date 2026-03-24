<?php
class Category {
    private $conn;
    private $table = 'categories';

    public $id;
    public $category;

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // Get Categories
    public function read() {
        $query = "SELECT id, category FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get Single Category
    public function read_single() {
        $query = "SELECT id, category FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt;
    }

    // The create method now returns the new ID directly from the database using RETURNING
    public function create() {
        $query = "INSERT INTO " . $this->table . " (category) 
                VALUES (:category) 
                RETURNING id";
                
        $stmt = $this->conn->prepare($query);

        $this->category = htmlspecialchars(strip_tags($this->category));

        $stmt->bindParam(':category', $this->category);

        // Execute and fetch the returned ID
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $result['id'];
            return true;
        }
        return false;
    }

    // Update Category
    public function update() {
        $query = "UPDATE " . $this->table . " SET category = :category WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':category', $this->category);

        return $stmt->execute();
    }

    // Delete Category
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }
}