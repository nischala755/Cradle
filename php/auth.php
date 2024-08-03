<?php
header("Content-Type: application/json");
$method = $_SERVER['REQUEST_METHOD'];
$mysqli = new mysqli("localhost", "root", "", "defi_app");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = isset($input['action']) ? $input['action'] : $_POST['action'];
$username = isset($input['username']) ? $input['username'] : $_POST['username'];
$password = isset($input['password']) ? $input['password'] : $_POST['password'];

if ($method == 'POST') {
    if ($action == 'signup') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);
        if ($stmt->execute()) {
            echo json_encode(["message" => "Signup successful"]);
        } else {
            echo json_encode(["message" => "Signup failed"]);
        }
        $stmt->close();
    } elseif ($action == 'login') {
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                echo json_encode(["message" => "Login successful"]);
            } else {
                echo json_encode(["message" => "Invalid credentials"]);
            }
        } else {
            echo json_encode(["message" => "Invalid credentials"]);
        }
        $stmt->close();
    }
}

$mysqli->close();
?>
