<?php
// Set header to JSON so the mobile app knows how to parse it
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$response = [];

if ($method === 'GET') {
    // Access params via $_GET (e.g., hello.php?name=Gemini)
    $name = $_GET['name'] ?? 'World';
    $response = [
        "message" => "Hello $name! This was a GET request.",
        "status" => "success"
    ];
} elseif ($method === 'POST') {
    // Access params via $_POST
    $name = $_POST['name'] ?? 'World';
    $response = [
        "message" => "Hello $name! This was a POST request.",
        "status" => "success"
    ];
} else {
    $response = ["error" => "Method not allowed"];
}

echo json_encode($response);
