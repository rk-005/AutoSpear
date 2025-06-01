<?php
// --- PHP Configuration for Error Reporting (for debugging) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // Display all errors to help debug

// --- Session Start (MUST be the very first thing before any HTML output) ---
session_start();

// --- Initialize message variable ---
$message = ""; 

// --- Process Login Form Submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include db_connect.php.
    // Ensure db_connect.php does NOT call session_start() itself.
    // Ensure db_connect.php also has no characters before <?php on line 1.
    include 'db_connect.php'; 

    $username = $_POST['username'];
    $password = $_POST['password'];

    // --- VULNERABILITY: SQL Injection ---
    // Directly concatenating user input into the SQL query
    $sql = "SELECT id, username FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Login successful
        $user = $result->fetch_assoc();
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $user['username'];
        
        // --- FIX: Immediate Redirection ---
        // The header() function MUST be called before any HTML or other output.
        // It's the absolute first thing PHP does if successful.
        header("Location: dashboard.php"); 
        exit(); // Crucial: Stop script execution immediately after sending the header
    } else {
        // Login failed
        $message = "<p class='message'>Invalid username or password.</p>";
    }

    // Close the database connection ONLY after all operations for this request
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Login</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; text-align: center; }
        h2 { color: #333; margin-bottom: 20px; }
        input[type="text"], input[type="password"] { width: calc(100% - 20px); padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 44px; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        .message { margin-top: 15px; color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login to Vulnerable App</h2>
        <form method="POST" action="">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>

        <?php
        // Display messages only after the form, if any.
        echo $message; 
        ?>
    </div>
</body>
</html>