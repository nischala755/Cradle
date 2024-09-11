<?php
// Set response type to JSON
header("Content-Type: application/json");

// Get the request method (POST in this case)
$method = $_SERVER['REQUEST_METHOD'];

// Connect to the MySQL database
$mysqli = new mysqli("localhost", "root", "", "defi_app");

// Check if the database connection was successful
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Decode JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Determine the action (signup or login) and retrieve username/password from either JSON input or POST
$action = isset($input['action']) ? $input['action'] : $_POST['action'];
$username = isset($input['username']) ? $input['username'] : $_POST['username'];
$password = isset($input['password']) ? $input['password'] : $_POST['password'];

// Handle POST requests
if ($method == 'POST') {
    // Signup action
    if ($action == 'signup') {
        // Hash the password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL query to insert user details into the database
        $stmt = $mysqli->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);

        // Execute the query and check for success
        if ($stmt->execute()) {
            echo json_encode(["message" => "Signup successful"]);
        } else {
            echo json_encode(["message" => "Signup failed"]);
        }
        $stmt->close();

    // Login action
    } elseif ($action == 'login') {
        // Prepare SQL query to select user by username
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();

        // Get the query result
        $result = $stmt->get_result();

        // Check if a user with the given username exists
        if ($result->num_rows > 0) {
            // Fetch the user data
            $user = $result->fetch_assoc();

            // Verify the provided password with the stored hash
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

// Close the database connection
$mysqli->close();
?>
