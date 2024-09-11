<?php
// Start session to track user login state
session_start();
header("Content-Type: application/json");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "User not logged in"]);
    exit;
}

// Connect to the MySQL database
$mysqli = new mysqli("localhost", "root", "", "cradle_defi");

// Check for database connection errors
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Parse JSON input data
$input = json_decode(file_get_contents('php://input'), true);
$action = isset($input['action']) ? $input['action'] : $_POST['action'];

// Handle the staking action
if ($action == 'stake') {
    $token = isset($input['token']) ? $input['token'] : $_POST['token'];
    $amount = isset($input['amount']) ? $input['amount'] : $_POST['amount'];

    // Prepare an SQL statement to insert the staking transaction
    $stmt = $mysqli->prepare("INSERT INTO transactions (user_id, type, token, amount) VALUES (?, 'staking', ?, ?)");
    $stmt->bind_param("iss", $_SESSION['user_id'], $token, $amount);
    
    // Execute the query and send response
    if ($stmt->execute()) {
        echo json_encode(["message" => "Staking successful"]);
    } else {
        echo json_encode(["message" => "Staking failed"]);
    }

    // Close the statement
    $stmt->close();
}

// Handle the request to get staking transactions
if ($action == 'get_staking') {
    // Prepare SQL statement to fetch all staking transactions for the logged-in user
    $stmt = $mysqli->prepare("SELECT * FROM transactions WHERE user_id = ? AND type = 'staking'");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();

    // Fetch the result and return it as JSON
    $result = $stmt->get_result();
    $staking = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($staking);

    // Close the statement
    $stmt->close();
}

// Close the database connection
$mysqli->close();
?>
