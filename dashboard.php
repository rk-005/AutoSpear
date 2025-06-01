<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// --- NEW PHP LOGIC FOR HANDLING COMMENT SUBMISSION ---
// This part should be placed here, after session check, before any HTML.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_text'])) {
    include 'db_connect.php'; // Include DB connection for this operation

    $comment_text = $_POST['comment_text'];
    $username = $_SESSION['username']; // Get username from session

    // --- VULNERABILITY: XSS (Saving unescaped user input directly to DB) ---
    // For demonstration, we are intentionally NOT escaping HTML characters here.
    // However, we use a prepared statement to prevent SQL Injection when inserting the comment.
    $stmt = $conn->prepare("INSERT INTO comments (username, comment_text) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ss", $username, $comment_text); // 'ss' for two strings
        if ($stmt->execute()) {
            // Comment posted successfully, redirect back to dashboard to see it updated
            // Use header("Location: ..."); to refresh the page.
            header("Location: dashboard.php");
            exit();
        } else {
            // Error handling for database insertion
            echo "Error posting comment: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Error handling for prepared statement creation
        echo "Error preparing comment statement: " . $conn->error;
    }
    $conn->close(); // Close connection after use
}
// --- END NEW PHP LOGIC ---
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vulnerable Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; background-color: #333; color: white; padding: 10px 20px; border-radius: 8px; margin-bottom: 20px; }
        .header a { color: white; text-decoration: none; padding: 5px 10px; background-color: #007bff; border-radius: 4px; }
        .header a:hover { background-color: #0056b3; }
        .container { background-color: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        h2 { color: #333; margin-bottom: 20px; }
        input[type="text"] { width: calc(100% - 20px); padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #28a745; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .comment-box { border: 1px solid #eee; padding: 10px; margin-bottom: 10px; border-radius: 4px; } /* Added class for comment styling */
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2>Product Search (SQL Injection Demo)</h2>
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search products by name..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Search</button>
        </form>

        <?php
        // Note: db_connect.php is already included at the top for comment submission.
        // If the GET request for search happens before the POST, you might need to re-include or ensure $conn is globally accessible.
        // For simplicity, let's assume it's included again here if the POST block is skipped.
        // This is safe because include 'db_connect.php' will only include it once if already included.
        // If using `require_once` at the very top, you wouldn't need to re-include.
        
        // This 'include' is needed here because the previous $conn might have been closed by the POST logic.
        // Or, better, refactor to keep $conn open until the end of the script or use a function.
        // For simplicity and demonstration, we'll re-include it here.
        include 'db_connect.php'; 

        if (isset($_GET['search'])) {
            $search_term = $_GET['search'];

            // --- VULNERABILITY: SQL Injection ---
            // Directly concatenating user input into the SQL query
            $sql = "SELECT id, name, description, price FROM products WHERE name LIKE '%$search_term%'";
            $result = $conn->query($sql);

            if ($result) {
                if ($result->num_rows > 0) {
                    echo "<table>";
                    echo "<tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th></tr>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>"; 
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>No products found.</p>";
                }
            } else {
                echo "<p>Error in query: " . $conn->error . "</p>"; // Display error for demo
            }
        }
        // Closing connection for this block. Re-opened below for comments.
        // This is inefficient but demonstrates modularity. A better approach is one connection for the whole script.
        $conn->close(); 
        ?>
    </div>

    <div class="container">
        <h2>Leave a Comment (XSS Demo)</h2>
        <p>This is where you can see Cross-Site Scripting in action!</p>
        <form method="POST" action="dashboard.php"> <textarea name="comment_text" rows="4" cols="50" placeholder="Enter your comment here..."></textarea><br>
            <button type="submit">Post Comment</button>
        </form>
    </div>

    <div class="container" style="margin-top: 20px;">
        <h2>All Comments</h2>
        <?php
        // Re-include db_connect.php to get a new connection for fetching comments
        include 'db_connect.php'; 

        $sql_comments = "SELECT username, comment_text, timestamp FROM comments ORDER BY timestamp DESC";
        $result_comments = $conn->query($sql_comments);

        if ($result_comments && $result_comments->num_rows > 0) {
            while ($row_comment = $result_comments->fetch_assoc()) {
                echo "<div class='comment-box'>";
                echo "<strong>" . htmlspecialchars($row_comment['username']) . ":</strong> ";
                // --- VULNERABILITY: XSS ---
                // Directly outputting user-controlled comment_text without escaping
                echo $row_comment['comment_text']; // THIS IS THE XSS VULNERABILITY
                echo "<br><small>" . $row_comment['timestamp'] . "</small>";
                echo "</div>";
            }
        } else {
            echo "<p>No comments yet.</p>";
        }
        $conn->close(); // Close connection after displaying comments
        ?>
    </div>
</body>
</html>