<?php
session_start();
include('db.php');

// Ensure session variable for user ID is set  
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

// Check if 'post_id' is set in the URL  
if (isset($_GET['post_id'])) {
    $post_id = intval($_GET['post_id']);

    // Delete comments associated with the post  
    $delete_comments_query = "DELETE FROM comments WHERE post_id = ?";
    $delete_comments_stmt = $conn->prepare($delete_comments_query);
    $delete_comments_stmt->bind_param("i", $post_id);
    $delete_comments_stmt->execute();
    $delete_comments_stmt->close();

    // Delete likes associated with the post  
    $delete_likes_query = "DELETE FROM likes WHERE post_id = ?";
    $delete_likes_stmt = $conn->prepare($delete_likes_query);
    $delete_likes_stmt->bind_param("i", $post_id);
    $delete_likes_stmt->execute();
    $delete_likes_stmt->close();

    // Delete the post itself  
    $delete_post_query = "DELETE FROM posts WHERE post_id = ? AND user_id = ?";
    $delete_post_stmt = $conn->prepare($delete_post_query);
    $delete_post_stmt->bind_param("ii", $post_id, $_SESSION['user_id']);

    if ($delete_post_stmt->execute()) {
        // Redirect back to the referring page  
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'profile.php'; // fallback to profile.php  
        header("Location: $referer");
        exit();
    } else {
        echo "Error deleting post: " . $delete_post_stmt->error;
    }
    $delete_post_stmt->close();
} else {
    echo "No post ID specified.";
}
