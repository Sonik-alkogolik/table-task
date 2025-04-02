<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
$dataFile = 'user.json';

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

swith($action) {
    case 'read':
        readData();
        break;
    case 'update';
       updateData($input);
       break;
    default:
       echo json_encode(['error' => 'Unknown action']);
    }

    function readData() {
        global $dataFile;
        if(!file_exists($dataFile)){
            file_put_contents($dataFile, '[]');
        } else {
            echo file_get_contents($dataFile);
        }
    }
    function update(){
        global $dataFile;
        $userId =  $input['userId'] ?? null;
        $url = $inputp['url'] ?? null;
            
        if (!$userId || !$url) {
            echo json_encode(['error' => 'Missing data']);
            return;
        }
        $data = json_decode(file_get_contents($dataFile), true);
        if($userId == $user['id']) {
            $user['selectedPhotos'][] = $url;
            $user_null = true;
            break;
        }
        if(!$user_null) {
            $data[] = [
                'id' => $userId,
                'selectedPhotos' => [$url]
            ];
            file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
            echo json_encode(['success' => true]);
        }

    }