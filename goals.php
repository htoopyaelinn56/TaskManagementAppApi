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
        SELECT *
        FROM goals
        WHERE user_id = ?
        ORDER BY created_at DESC
    ");

    $stmt->bind_param("i", $user_id);

    $stmt->execute();

    $result = $stmt->get_result();

    $goals = [];

    while ($row = $result->fetch_assoc()) {
        $goals[] = $row;
    }

    echo json_encode($goals);
} elseif ($method === 'POST') {

    $user_id = $_POST['user_id'] ?? 0;
    $name = $_POST['name'] ?? '';
    $activity_type = $_POST['activity_type'] ?? '';
    $target_value = $_POST['target_value'] ?? null;
    $target_unit = $_POST['target_unit'] ?? null;
    $deadline = $_POST['deadline'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $status = $_POST['status'] ?? 'pending';

    if (!$user_id || !$name || !$activity_type || !$deadline) {

        echo json_encode([
            "status" => "error",
            "message" => "Missing required fields"
        ]);

        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO goals
        (
            user_id,
            name,
            activity_type,
            target_value,
            target_unit,
            deadline,
            notes,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "issdssss",
        $user_id,
        $name,
        $activity_type,
        $target_value,
        $target_unit,
        $deadline,
        $notes,
        $status
    );

    if ($stmt->execute()) {

        echo json_encode([
            "status" => "success",
            "message" => "Goal created"
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
        DELETE FROM goals
        WHERE id = ?
    ");

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        echo json_encode([
            "status" => "success",
            "message" => "Goal deleted"
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
