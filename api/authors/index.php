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
        if(!isset($data->author)) {
            // Requirement: Missing Parameter check
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $author->author = $data->author;
        if($author->create()) {
            echo json_encode(['id' => $author->id, 'author' => $author->author]);
        }
    break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        // Requirement: Must contain id and author
        if(!isset($data->id) || !isset($data->author)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            break;
        }

        $author->id = $data->id;
        $author->author = $data->author;

        // Requirement: Check if author exists. If not, return "author_id Not Found"
        if(!$author->read_single()->fetch()) {
            echo json_encode(["message" => "author_id Not Found"]);
            break;
        }

        if($author->update()) {
            echo json_encode(["id" => $author->id, "author" => $author->author]);
        }
    break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if(!isset($data->id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            break;
        }
        $author->id = $data->id;

        // Requirement: Check if author exists. If not, return "author_id Not Found"
        if(!$author->read_single()->fetch()) {
            echo json_encode(["message" => "author_id Not Found"]);
            break;
        }

        if($author->delete()) {
            echo json_encode(["id" => $author->id]);
        }
    break;
}