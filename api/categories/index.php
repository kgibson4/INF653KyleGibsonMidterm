<?php
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
                echo json_encode(["message" => "No Quotes Found"]);
            }
        } else {
            $result = $category->read();
            echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));
        }
    break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        if(!isset($data->category)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }
        $category->category = $data->category;
        if($category->create()) {
            echo json_encode(['id' => $category->id, 'category' => $category->category]);
        }
    break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));
        
        if(!isset($data->id) || !isset($data->category)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            break;
        }

        $category->id = $data->id;
        $category->category = $data->category;

        if(!$category->read_single()->fetch()) {
            echo json_encode(["message" => "No Quotes Found"]);
            break;
        }

        if($category->update()) {
            echo json_encode(["id" => $category->id, "category" => $category->category]);
        }
    break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));
        if(!isset($data->id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            break;
        }
        $category->id = $data->id;

        if(!$category->read_single()->fetch()) {
            echo json_encode(["message" => "No Quotes Found"]);
            break;
        }

        if($category->delete()) {
            echo json_encode(["id" => $category->id]);
        }
    break;
}