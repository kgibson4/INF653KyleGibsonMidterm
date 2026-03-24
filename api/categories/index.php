<?php
// Set headers to allow Cross-Origin Resource Sharing (CORS) and define JSON output
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Category.php';

$database = new Database();
$db = $database->connect();
$category = new Category($db);
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $category->id = $_GET['id'];
            $result = $category->read_single();
            $row = $result->fetch(PDO::FETCH_ASSOC);

            if($row) {
                echo json_encode(['id' => $row['id'], 'category' => $row['category']]);
            } else {
                echo json_encode(["message" => "category_id Not Found"]);
            }
        } else {
            $result = $category->read();
            // Return a simple indexed array of all category objects
            echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        }
    break;

    case 'POST':
        // Retrieve and decode raw JSON body from the input stream
        $data = json_decode(file_get_contents("php://input"));

        if(!isset($data->category) || empty(trim($data->category))) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }

        $category->category = $data->category;

        if($category->create()) {
            echo json_encode([
                "id" => $category->id,
                "category" => $category->category
            ]);
        } else {
            echo json_encode(["message" => "Category could not be created."]);
        }
    break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        if(!isset($data->id) || !isset($data->category)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }

        $category->id = $data->id;
        $category->category = $data->category;

        // Perform existence check to satisfy specific error message requirements
        if(!$category->read_single()->fetch()) {
            echo json_encode(["message" => "No Quotes Found"]);
            return;
        }

        if($category->update()) {
            echo json_encode([
                "id" => $category->id,
                "category" => $category->category
            ]);
        } else {
            echo json_encode(["message" => "Category could not be updated."]);
        }
    break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if(!isset($data->id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            break;
        }
        $category->id = $data->id;

        // Ensure record exists before deletion attempt
        if(!$category->read_single()->fetch()) {
            echo json_encode(["message" => "No Quotes Found"]);
            break;
        }

        if($category->delete()) {
            echo json_encode(["id" => $category->id]);
        }
    break;
}