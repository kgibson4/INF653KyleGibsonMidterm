<?php
header('Content-Type: application/json');

// 1. Check if we can reach the Database class
include_once 'config/Database.php';

$database = new Database();
$db = $database->connect();

if ($db) {
    echo json_encode([
        "status" => "Success",
        "message" => "Docker is running, .htaccess is routing, and I am connected to Render Postgres!"
    ]);
} else {
    echo json_encode([
        "status" => "Error",
        "message" => "Server is up, but I cannot reach the Render Database. Check your .env file."
    ]);
}
exit();