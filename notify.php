<?php
session_start();
require_once('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Current logged-in user

// Get unread notifications using prepared statements
$stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND read_status = 'unread'");
if ($stmt === false) {
    die("ERROR: Could not prepare query: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$notifications = $result->fetch_all(MYSQLI_ASSOC);

// Mark notifications as read
$update_stmt = $conn->prepare("UPDATE notifications SET read_status = 'read' WHERE user_id = ? AND read_status = 'unread'");
if ($update_stmt === false) {
    die("ERROR: Could not prepare update query: " . $conn->error);
}
$update_stmt->bind_param("i", $user_id);
$update_stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
        }

        li {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        small {
            color: #666;
        }
    </style>
</head>

<body>
    <h1>Your Notifications</h1>
    <?php if (count($notifications) > 0): ?>
        <ul>
            <?php foreach ($notifications as $notification): ?>
                <li>
                    <strong><?php echo htmlspecialchars($notification['message']); ?></strong><br>
                    <small>From User <?php echo htmlspecialchars($notification['from_user_id']); ?> </small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No new notifications.</p>
    <?php endif; ?>
</body>

</html>