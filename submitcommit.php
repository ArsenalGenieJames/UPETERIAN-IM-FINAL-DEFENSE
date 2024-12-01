<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the user ID, post ID, and comment content from the form
    $user_id = $_POST['user_id'];
    $post_id = $_POST['post_id'];
    $content = $_POST['content'];

    // Check if fields are empty
    if (empty($user_id) || empty($post_id) || empty($content)) {
        die("User ID, Post ID, and content cannot be empty.");
    }

    // Prepare and bind the comment insert statement
    $stmt = $conn->prepare("INSERT INTO Comments (post_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $post_id, $user_id, $content);

    // Check for successful insertion
    if ($stmt->execute()) {
        // Get the referrer (where the user came from)
        $referer = $_SERVER['HTTP_REFERER'];

        // Redirect back to the referring page (home.php or profile.php)
        if (strpos($referer, 'home.php') !== false) {
            header("Location: home.php");
        } elseif (strpos($referer, 'profile.php') !== false) {
            header("Location: profile.php");
        } else {
            header("Location: home.php"); // Default fallback
        }
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
