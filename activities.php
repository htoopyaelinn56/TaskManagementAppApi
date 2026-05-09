<?php
include 'db_config.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];


if ($method === 'GET') {

    $user_id = $_GET['user_id'] ?? 0;

    if (!$user_id) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing user_id"
        ]);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT * FROM activities
        WHERE user_id = ?
        ORDER BY date DESC
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    $activities = [];

    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }

    echo json_encode($activities);
} elseif ($method === 'POST') {

    $user_id = $_POST['user_id'];
    $type = $_POST['type'];
    $duration = $_POST['duration_minutes'];
    $val = $_POST['metric_value'] ?? null;
    $unit = $_POST['metric_unit'] ?? null;
    $date = $_POST['date'];
    $calories = $_POST['calories_kcal'];
    $location = $_POST['location'] ?? '';

    $stmt = $conn->prepare("
        INSERT INTO activities
        (
            user_id,
            type,
            duration_minutes,
            metric_value,
            metric_unit,
            date,
            calories_kcal,
            location
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isidssis",
        $user_id,
        $type,
        $duration,
        $val,
        $unit,
        $date,
        $calories,
        $location
    );

    if ($stmt->execute()) {

        echo json_encode([
            "status" => "success",
            "message" => "Activity logged"
        ]);
    } else {

        echo json_encode([
            "status" => "error",
            "message" => $conn->error
        ]);
    }
} elseif ($method === 'DELETE') {

    parse_str(file_get_contents("php://input"), $data);

    $id = $data['id'] ?? 0;

    if (!$id) {
        echo json_encode([
            "status" => "error",
            "message" => "Missing id"
        ]);
        exit;
    }

    $stmt = $conn->prepare("
        DELETE FROM activities
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        echo json_encode([
            "status" => "success",
            "message" => "Activity deleted"
        ]);
    } else {

        echo json_encode([
            "status" => "error",
            "message" => "Delete failed"
        ]);
    }
} else {

    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}
