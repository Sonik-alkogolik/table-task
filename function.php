<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");


require_once 'connectDB.php'; 
$conn = connectDB();
$tableName = 'products';
$input = file_get_contents('php://input');
$data = json_decode($input, true); 
$action = $data['action'] ?? '';

if (!$conn) {
    die(json_encode(['error' => 'Database connection failed']));
}

function readData($conn, $tableName) {
    try {
        
        $result = $conn->query("SELECT * FROM `$tableName`");
        
        if (!$result) {
            throw new Exception($conn->error);
        }
        $data = $result->fetch_all(MYSQLI_ASSOC);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
        
    } catch (Exception $e) {
        die(json_encode(['error' => $e->getMessage()]));
    }
}



function createData($conn,$tableName,$data) {

    if ($data === null) {
        return ['error' => 'Invalid JSON data'];
    } 

     //var_dump($data);

    if (empty($data['name'])) {
        return ['error' => 'Поле "name" обязательно'];
    }
    $name = $data['name'];
    $provider = $data['payment_method'];
    $product_fields = $data['full_data']['product_fileds'] ?? [];
    $product_fields_json = json_encode($product_fields, JSON_UNESCAPED_UNICODE);
    $product_fields_json = $conn->real_escape_string($product_fields_json);
    $sql = "INSERT INTO `$tableName` (`user`,`providers`,`product_fileds`) VALUES ('$name','$provider','$product_fields_json')";
    if (!$conn->query($sql)) {
        return ['error' => $conn->error];
    }
    $conn->close();
    return ['success' => true];
}

function edit($conn,$tableName,$data) {
    $id = $data['id'];
    $sql = "SELECT * FROM `$tableName` WHERE `id` = '$id'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return ['success' => true, 'data' => $row];
}

function deleteData($conn, $tableName, $data) {
    if (empty($data['id'])) {
        return ['error' => 'Требуется ID записи'];
    }
    $stmt = $conn->prepare("DELETE FROM `$tableName` WHERE id = ?");
    $stmt->bind_param('i', $data['id']);
    if ($stmt->execute()) {
        return ['success' => true];
    }
    return ['error' => $stmt->error];
}

function updateData($conn, $tableName, $data) {
    var_dump($data);
    $id = $data['id'];
    $name = $data['name'];
    $provider = $data['providers'];
    $product_fileds = $data['full_data'];
    $product_fileds_json = json_encode($product_fileds, JSON_UNESCAPED_UNICODE);
   


    $sql = "UPDATE `$tableName` SET 
    `user` = '$name', 
    `providers` = '$provider', 
    `product_fileds` = '$product_fileds_json' 
    WHERE `id` = '$id'";

    if (!$conn->query($sql)) {
        return ['error' => $conn->error];
    }

    $conn->close();
    
    return ['success' => true];
}

switch ($action) {
    case 'readData':
        echo readData($conn, $tableName);
        break;
    case 'create':
        $result = createData($conn,$tableName,$data);
        echo json_encode($result);
        break;
    case 'edit':
        $result = edit($conn,$tableName,$data);
        echo json_encode($result);
        break;
    case 'delete':
        echo json_encode(deleteData($conn, $tableName, $data));
        break;
    case 'update':
        echo json_encode(updateData($conn, $tableName, $data));
        break;
    default:
        echo json_encode(["error" => "Unknown action"]);
}
