<?php
session_start();
header("Content-Type: application/json");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "User not logged in"]);
    exit;
}

// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "cradle_defi");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = isset($input['action']) ? $input['action'] : $_POST['action'];

if ($action == 'lend') {
    $token = isset($input['token']) ? $input['token'] : $_POST['token'];
    $amount = isset($input['amount']) ? $input['amount'] : $_POST['amount'];

    // Insert lending transaction into the database
    $stmt = $mysqli->prepare("INSERT INTO transactions (user_id, type, token, amount) VALUES (?, 'lending', ?, ?)");
    $stmt->bind_param("iss", $_SESSION['user_id'], $token, $amount);
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Lending successful"]);
    } else {
        echo json_encode(["message" => "Lending failed"]);
    }

    $stmt->close();
}

// Fetch the user's lending transactions
if ($action == 'get_lending') {
    $stmt = $mysqli->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'lending'");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $lending = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($lending);
}

$mysqli->close();
?>
