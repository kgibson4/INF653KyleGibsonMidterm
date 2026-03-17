<?php
    class Database {
        private $host;
        private $port;
        private $db_name;
        private $username;
        private $password;
        private $conn;

        public function __construct() {
            $renderEnv = getenv('RENDER');

            // Only use Internal if RENDER is explicitly set to 'true'
            if ($renderEnv !== false && strtolower($renderEnv) === 'true') {
                $this->host = getenv('DB_HOST_INTERNAL');
            } else {
                // This will now correctly fire on your local machine
                $this->host = getenv('DB_HOST_EXTERNAL');
            }

            $this->port = getenv('DB_PORT') ?: '5432';
            $this->db_name = getenv('DB_NAME');
            $this->username = getenv('DB_USER');
            $this->password = getenv('DB_PASS');
        }

        public function connect() {
            $this->conn = null;
            
            // Build the DSN for PostgreSQL
            $dsn = "pgsql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;

            try {
                $this->conn = new PDO($dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // Recommended for REST APIs to return data as associative arrays
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch(PDOException $e) {
                // Requirements specify JSON responses for the API 
                echo json_encode(["message" => "Connection Error: " . $e->getMessage()]);
                exit;
            }

            return $this->conn;
        }
    }