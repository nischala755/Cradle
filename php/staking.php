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

if ($action == 'stake') {
    $token = isset($input['token']) ? $input['token'] : $_POST['token'];
    $amount = isset($input['amount']) ? $input['amount'] : $_POST['amount'];

    // Insert staking transaction into the database
    $stmt = $mysqli->prepare("INSERT INTO transactions (user_id, type, token, amount) VALUES (?, 'staking', ?, ?)");
    $stmt->bind_param("iss", $_SESSION['user_id'], $token, $amount);
    
    if ($stmt->execute()) {
        echo json_encode(["message" => "Staking successful"]);
    } else {
        echo json_encode(["message" => "Staking failed"]);
    }

    $stmt->close();
}

// Fetch the user's staked tokens
if ($action == 'get_staking') {
    $stmt = $mysqli->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'staking'");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $staking = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($staking);
}

$mysqli->close();
?>
