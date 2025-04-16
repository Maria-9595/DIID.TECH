<?php

require_once __DIR__ . '/../src/Calculated/Calculated.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$length = filter_input(INPUT_POST, 'length', FILTER_VALIDATE_INT);
$width = filter_input(INPUT_POST, 'width', FILTER_VALIDATE_INT);

if ($length === false || $width === false || $length <= 0 || $width <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$cost = \Calculated\Calculated::calculateCost($length, $width);

echo json_encode(['cost' => $cost]);