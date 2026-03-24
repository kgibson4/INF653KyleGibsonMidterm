<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();
$author = new Author($db);
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $author->id = $_GET['id'];
            $result = $author->read_single();
            $row = $result->fetch(PDO::FETCH_ASSOC);

            if($row) {
                echo json_encode(['id' => $row['id'], 'author' => $row['author']]);
            } else {
                // Requirement: Specific error for GET
                echo json_encode(["message" => "author_id Not Found"]);
            }
        } else {
            $result = $author->read();
            echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        }
    break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));

        // Requirement: Check for missing parameters
        if(!isset($data->author) || empty(trim($data->author))) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }

        $author->author = $data->author;

        if($author->create()) {
            // SUCCESS: Return a single JSON object { id: X, author: "Name" }
            echo json_encode([
                "id" => $author->id,
                "author" => $author->author
            ]);
        } else {
            echo json_encode(["message" => "Author could not be created."]);
        }
    break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        // 1. Check for missing parameters
        if(!isset($data->id) || !isset($data->author)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }

        $author->id = $data->id;
        $author->author = $data->author;

        // 2. REQUIREMENT: Check if author exists first
        // If not found, return "No Authors Found"
        if(!$author->read_single()->fetch()) {
            echo json_encode(["message" => "No Authors Found"]);
            return;
        }

        // 3. Perform update and return the SINGLE OBJECT
        if($author->update()) {
            echo json_encode([
                "id" => $author->id,
                "author" => $author->author
            ]);
        } else {
            echo json_encode(["message" => "Author could not be updated."]);
        }
    break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        // 1. Check for missing ID parameter
        if(!isset($data->id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }

        $author->id = $data->id;

        // 2. REQUIREMENT: Check if author exists first
        // If not found, return "No Authors Found"
        if(!$author->read_single()->fetch()) {
            echo json_encode(["message" => "No Authors Found"]);
            return;
        }

        // 3. Perform delete and return the SINGLE OBJECT {"id": X}
        if($author->delete()) {
            echo json_encode([
                "id" => $author->id
            ]);
        } else {
            echo json_encode(["message" => "Author could not be deleted."]);
        }
    break;
}