<?php
ob_start();
require_once 'includes/config.php';

while (ob_get_level()) {
    ob_end_clean();
}
header('Content-Type: application/json');

$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Check JSON parsing
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(["status" => "error", "message" => "Invalid JSON: " . json_last_error_msg()]);
    exit;
}

if (isset($data['order']) && is_array($data['order'])) {
    $errors = [];

    foreach ($data['order'] as $item) {
        if (!isset($item['id'], $item['order'])) {
            $errors[] = "Invalid data provided.";
            continue;
        }

        $id = (int)$item['id'];
        $order_val = (int)$item['order'];

        $sql_update = "UPDATE headline SET sort_order = $order_val WHERE h_id = $id";

        if (!mysqli_query($conn, $sql_update)) {
            $errors[] = "Error updating ID $id: " . mysqli_error($conn);
        }
    }

    if (empty($errors)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => implode("; ", $errors)]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Order data missing or incorrect format."]);
}

exit;
