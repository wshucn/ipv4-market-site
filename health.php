<?php
header('Content-Type: application/json');

$response = [
    'status' => 'healthy',
    'timestamp' => date('c')
];

echo json_encode($response);