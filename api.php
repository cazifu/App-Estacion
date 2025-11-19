<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Use local data for demonstration
$localData = file_get_contents('data.json');
if ($localData !== false) {
    echo $localData;
} else {
    http_response_code(500);
    echo json_encode(['error' => 'No se pudieron cargar los datos']);
}