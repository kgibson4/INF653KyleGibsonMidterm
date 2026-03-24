<?php
class Quote {
    private $conn;
    private $table = 'quotes';

    public $id;
    public $quote;
    public $author_id;
    public $category_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($params = []) {

        $query = "
            SELECT 
                q.id,
                q.quote,
                a.author,
                c.category
            FROM quotes q
            JOIN authors a ON q.author_id = a.id
            JOIN categories c ON q.category_id = c.id
        ";

        $conditions = [];

        if(isset($params['id'])) {
            $conditions[] = "q.id = :id";
        }

        if(isset($params['author_id'])) {
            $conditions[] = "q.author_id = :author_id";
        }

        if(isset($params['category_id'])) {
            $conditions[] = "q.category_id = :category_id";
        }

        if(count($conditions) > 0) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->conn->prepare($query);

        if(isset($params['id'])) {
            $stmt->bindParam(':id', $params['id']);
        }

        if(isset($params['author_id'])) {
            $stmt->bindParam(':author_id', $params['author_id']);
        }

        if(isset($params['category_id'])) {
            $stmt->bindParam(':category_id', $params['category_id']);
        }

        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "
            INSERT INTO quotes (quote, author_id, category_id)
            VALUES (:quote, :author_id, :category_id)
            RETURNING id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);

        if($stmt->execute()) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "
            UPDATE " . $this->table . "
            SET quote = :quote,
                author_id = :author_id,
                category_id = :category_id
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->quote = htmlspecialchars(strip_tags($this->quote));
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->author_id = htmlspecialchars(strip_tags($this->author_id));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));

        // Bind data
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM quotes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        return $stmt->execute();
    }
}