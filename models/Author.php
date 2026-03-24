<?php
class Author {
    private $conn;
    private $table = 'authors';

    public $id;
    public $author;

    // Constructor with DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // Get Authors
    public function read() {
        $query = "SELECT id, author FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Get Single Author
    public function read_single() {
        $query = "SELECT id, author FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
        return $stmt;
    }

    // The create method now returns the new ID directly from the database using RETURNING
    public function create() {
        $query = "INSERT INTO " . $this->table . " (author) 
                VALUES (:author) 
                RETURNING id";
                
        $stmt = $this->conn->prepare($query);

        $this->author = htmlspecialchars(strip_tags($this->author));

        $stmt->bindParam(':author', $this->author);

        // Execute and fetch the returned ID
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $result['id'];
            return true;
        }
        return false;
    }

    // Update Author
    public function update() {
    $query = "UPDATE " . $this->table . " SET author = :author WHERE id = :id";
    $stmt = $this->conn->prepare($query);

    $this->author = htmlspecialchars(strip_tags($this->author));
    $this->id = htmlspecialchars(strip_tags($this->id));

    $stmt->bindParam(':id', $this->id);
    $stmt->bindParam(':author', $this->author);

    return $stmt->execute();
}

    // Delete Author
    public function delete() {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}