<?php
session_start();
require_once('db.php');

$sender_id = $_POST['sender_id']; 
$receiver_id = $_SESSION['user_id']; 


$sql = "UPDATE friendships SET status = 'accepted' WHERE user_id = $sender_id AND friend_id = $receiver_id";
mysqli_query($conn, $sql);


$message = "User $receiver_id accepted your friendship request.";
$sql = "INSERT INTO notifications (user_id, from_user_id, type, message) VALUES ($sender_id, $receiver_id, 'follow', '$message')";
mysqli_query($conn, $sql);

$message2 = "You are now friends with User $sender_id.";
$sql = "INSERT INTO notifications (user_id, from_user_id, type, message) VALUES ($receiver_id, $sender_id, 'follow', '$message2')";
mysqli_query($conn, $sql);

header("Location: profile.php?user=$receiver_id");
