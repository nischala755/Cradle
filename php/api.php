<?php
session_start();
header("Content-Type: application/json");

$mysqli = new mysqli("localhost", "root", "", "cradle_defi");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$action = $_POST['action'];
$user_id = $_SESSION['user_id'];

if ($action == 'swap') {
    // Handle token swap logic
    $fromToken = $_POST['fromToken'];
    $toToken = $_POST['toToken'];
    $amount = $_POST['amount'];

    // Simulated swap logic (real implementation would involve blockchain interaction)
    $stmt = $mysqli->prepare("UPDATE user_balances SET balance = balance - ? WHERE user_id = ? AND token = ?");
    $stmt->bind_param("dis", $amount, $user_id, $fromToken);
    $stmt->execute();

    $stmt = $mysqli->prepare("UPDATE user_balances SET balance = balance + ? WHERE user_id = ? AND token = ?");
    $stmt->bind_param("dis", $amount, $user_id, $toToken);
    $stmt->execute();

    // Log the transaction
    $stmt = $mysqli->prepare("INSERT INTO transactions (user_id, type, token, amount) VALUES (?, 'swap', ?, ?)");
    $stmt->bind_param("isd", $user_id, $fromToken, $amount);
    $stmt->execute();

    echo json_encode(["success" => true]);
}

if ($action == 'mine') {
    $reward = 0.01; // Example reward value
    $stmt = $mysqli->prepare("UPDATE user_balances SET balance = balance + ? WHERE user_id = ? AND token = 'ETH'");
    $stmt->bind_param("di", $reward, $user_id);
    $stmt->execute();

    // Log mining activity
    $stmt = $mysqli->prepare("INSERT INTO transactions (user_id, type, token, amount) VALUES (?, 'mine', 'ETH', ?)");
    $stmt->bind_param("id", $user_id, $reward);
    $stmt->execute();

    echo json_encode(["success" => true, "reward" => $reward]);
}

if ($action == 'get_balance') {
    $token = $_POST['token'];

    $stmt = $mysqli->prepare("SELECT balance FROM user_balances WHERE user_id = ? AND token = ?");
    $stmt->bind_param("is", $user_id, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $balance = $result->fetch_assoc()['balance'];

    echo json_encode(["success" => true, "balance" => $balance]);
}

if ($action == 'get_transaction_history') {
    $stmt = $mysqli->prepare("SELECT * FROM transactions WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $transactions = $result->fetch_all(MYSQLI_ASSOC);

    echo json_encode(["success" => true, "transactions" => $transactions]);
}

$mysqli->close();
?>
