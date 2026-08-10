<?php
header("Content-Type: application/json");

// Define where to store the password hash securely on the server
$hash_file = __DIR__ . "/password_hash.txt";

// Read the incoming JSON payload
$data = json_decode(file_get_contents("php://input"), true);

// Action 1: Get the current password hash
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($hash_file)) {
        echo json_encode(["hash" => trim(file_get_content($hash_file))]);
    } else {
        // Fallback default hash for "1234"
        echo json_encode(["hash" => "03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4"]);
    }
    exit;
}

// Action 2: Save a new password hash
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($data['hash'])) {
        // Sanitize and save the hash to disk
        $new_hash = preg_replace('/[^a-f0-9]/i', '', $data['hash']);
        if (strlen($new_hash) === 64) {
            file_put_contents($hash_file, $new_hash, LOCK_EX);
            echo json_encode(["status" => "success", "message" => "Password updated."]);
            exit;
        }
    }
    
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Invalid hash format."]);
    exit;
}
?>