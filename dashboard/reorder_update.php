<?php
ob_start();
require_once 'includes/config.php'; // Must not produce HTML output

// Clear output buffers to prevent accidental HTML from being sent
while (ob_get_level()) {
    ob_end_clean();
}
header('Content-Type: application/json');

// Read the raw JSON input
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (isset($data['order'])) {
    $order = $data['order'];
    $errors = [];
    foreach ($order as $item) {
        $id = intval($item['id']);
        $order_val = intval($item['order']);
        $sql_update = "UPDATE headline SET sort_order = $order_val WHERE h_id = $id";
        if (!mysqli_query($conn, $sql_update)) {
            $errors[] = "Error updating headline id $id: " . mysqli_error($conn);
        }
    }
    if (empty($errors)) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => implode("\n", $errors)]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No order data provided."]);
}
exit;
