<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Quote.php';
include_once '../../models/Author.php';
include_once '../../models/Category.php';

$database = new Database();
$db = $database->connect();
$quote = new Quote($db);
$method = $_SERVER['REQUEST_METHOD'];

switch($method) {

    case 'GET':
        $params = [];
        if(isset($_GET['id'])) $params['id'] = $_GET['id'];
        if(isset($_GET['author_id'])) $params['author_id'] = $_GET['author_id'];
        if(isset($_GET['category_id'])) $params['category_id'] = $_GET['category_id'];
        if(isset($_GET['random'])) $params['random'] = $_GET['random'];

        $result = $quote->read($params);
        $num = $result->rowCount();

        if($num > 0) {
            if(isset($_GET['id']) || (isset($_GET['random']) && $_GET['random'] === 'true')) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                echo json_encode($row);
            } else {
                
                $quotes_arr = $result->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($quotes_arr);
            }
        } else {
            echo json_encode(["message" => "No Quotes Found"]);
        }
    break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"));
        
        // 1. Check for required parameters
        if(!isset($data->quote) || !isset($data->author_id) || !isset($data->category_id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }

        // 2. Validate author_id exists
        $author = new Author($db);
        $author->id = $data->author_id;
        if(!$author->read_single()->fetch()) {
            echo json_encode(["message" => "author_id Not Found"]);
            return;
        }

        // 3. Validate category_id exists
        $cat = new Category($db);
        $cat->id = $data->category_id;
        if(!$cat->read_single()->fetch()) {
            echo json_encode(["message" => "category_id Not Found"]);
            return;
        }

        // 4. Create the Quote
        $quote->quote = $data->quote;
        $quote->author_id = $data->author_id;
        $quote->category_id = $data->category_id;

        if($quote->create()) {
            // SUCCESS: Return a single JSON object
            echo json_encode([
                "id" => $quote->id,
                "quote" => $quote->quote,
                "author_id" => $quote->author_id,
                "category_id" => $quote->category_id
            ]);
        } else {
            echo json_encode(["message" => "Quote could not be created."]);
        }
    break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"));

        // 1. Check for missing parameters (must have all 4)
        if(!isset($data->id) || !isset($data->quote) || !isset($data->author_id) || !isset($data->category_id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }

        $quote->id = $data->id;
        $quote->quote = $data->quote;
        $quote->author_id = $data->author_id;
        $quote->category_id = $data->category_id;

        // 2. REQUIREMENT: Check if the Quote ID exists first
        if(!$quote->read(['id' => $quote->id])->fetch()) {
            echo json_encode(["message" => "No Quotes Found"]);
            return;
        }

        // 3. REQUIREMENT: Check if author_id exists
        $author = new Author($db);
        $author->id = $data->author_id;
        if(!$author->read_single()->fetch()) {
            echo json_encode(["message" => "author_id Not Found"]);
            return;
        }

        // 4. REQUIREMENT: Check if category_id exists
        $cat = new Category($db);
        $cat->id = $data->category_id;
        if(!$cat->read_single()->fetch()) {
            echo json_encode(["message" => "category_id Not Found"]);
            return;
        }

        // 5. Perform the update and return the SINGLE OBJECT
        if($quote->update()) {
            echo json_encode([
                "id" => $quote->id,
                "quote" => $quote->quote,
                "author_id" => $quote->author_id,
                "category_id" => $quote->category_id
            ]);
        } else {
            echo json_encode(["message" => "Quote could not be updated."]);
        }
    break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"));

        if(!isset($data->id)) {
            echo json_encode(["message" => "Missing Required Parameters"]);
            return;
        }

        $quote->id = $data->id;

        // Requirement: Check existence before deleting
        if(!$quote->read(['id' => $quote->id])->fetch()) {
            echo json_encode(["message" => "No Quotes Found"]);
            return;
        }

        if($quote->delete()) {
            echo json_encode(["id" => $quote->id]);
        }
    break;
}