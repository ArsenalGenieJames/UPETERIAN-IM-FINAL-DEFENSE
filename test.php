<?php
session_start();
include('db.php');

// Ensure session variable for user ID is set
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}
$user_id = $_SESSION['user_id'];

// Function to handle queries more cleanly
function execute_query($conn, $query, $types, $params)
{
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Query preparation failed: " . $conn->error);
    }
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt;
}

// Fetch the user's profile information
$query_user = "SELECT Name, Email, profile_picture, username FROM users WHERE user_id = ?";
$stmt_user = execute_query($conn, $query_user, 'i', [$user_id]);

$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();
$stmt_user->close();

// Handle post deletion
if (isset($_GET['delete_post_id'])) {
    $delete_post_id = intval($_GET['delete_post_id']);

    // Delete comments associated with the post
    $delete_comments_query = "DELETE FROM comments WHERE post_id = ?";
    $delete_comments_stmt = execute_query($conn, $delete_comments_query, 'i', [$delete_post_id]);

    // Delete likes associated with the post
    $delete_likes_query = "DELETE FROM likes WHERE post_id = ?";
    $delete_likes_stmt = execute_query($conn, $delete_likes_query, 'i', [$delete_post_id]);

    // Delete the post itself
    $delete_post_query = "DELETE FROM posts WHERE post_id = ? AND user_id = ?";
    $delete_post_stmt = execute_query($conn, $delete_post_query, 'ii', [$delete_post_id, $user_id]);

    if ($delete_post_stmt->execute()) {
        header("Location: profile.php");
        exit();
    } else {
        echo "Error deleting post: " . $delete_post_stmt->error;
    }
    $delete_post_stmt->close();
}

// Handle comment deletion
if (isset($_GET['delete_comment_id'])) {
    $delete_comment_id = intval($_GET['delete_comment_id']);

    // Delete comment query
    $delete_comment_query = "DELETE FROM comments WHERE comment_id = ? AND user_id = ?";
    $delete_comment_stmt = execute_query($conn, $delete_comment_query, 'ii', [$delete_comment_id, $user_id]);

    if ($delete_comment_stmt->execute()) {
        header("Location: profile.php");
        exit();
    } else {
        echo "Error deleting comment: " . $delete_comment_stmt->error;
    }
    $delete_comment_stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $upload_dir = "uploads/profile_pictures/"; // Ensure this directory exists and is writable
    $target_file = $upload_dir . basename($_FILES["profile_picture"]["name"]);
    $upload_ok = 1;

    // Check if the file is an image  
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check === false) {
        echo "File is not an image.";
        $upload_ok = 0;
    }

    // Check file size (limit to 2MB)  
    if ($_FILES["profile_picture"]["size"] > 2000000) {
        echo "Sorry, your file is too large.";
        $upload_ok = 0;
    }

    // Allow certain file formats  
    $allowed_formats = ["jpg", "png", "jpeg", "gif"];
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (!in_array($file_type, $allowed_formats)) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $upload_ok = 0;
    }

    // Check if file upload is valid  
    if ($upload_ok === 1) {
        // Create the directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update the profile picture in the database  
            $update_query = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_query);
            if ($update_stmt === false) {
                die("ERROR: Could not prepare query: $update_query. " . $conn->error);
            }

            $update_stmt->bind_param("si", $target_file, $user_id);

            if ($update_stmt->execute()) {
                echo "Profile picture updated successfully!";
                header("Refresh: 0"); // Refresh the page to reflect changes  
                exit();
            } else {
                echo "Error updating profile picture.";
            }

            $update_stmt->close(); // Close update statement  
        } else {
            echo "Error uploading your file.";
        }
    }
}

// Handle new comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'], $_POST['post_id'])) {
    $comment = trim($_POST['comment']);
    $post_id = intval($_POST['post_id']);

    if (!empty($comment)) {
        $insert_comment_query = "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)";
        $insert_comment_stmt = execute_query($conn, $insert_comment_query, 'iis', [$post_id, $user_id, $comment]);

        if ($insert_comment_stmt->execute()) {
            header("Location: profile.php");
            exit();
        } else {
            echo "Error adding comment: " . $insert_comment_stmt->error;
        }
        $insert_comment_stmt->close();
    } else {
        echo "Comment cannot be empty.";
    }
}


$post_query = "
    SELECT 
        p.post_id, 
        p.content, 
        p.media_url, 
        p.timestamp, 
        (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.post_id) AS like_count, 
        u.username, 
        u.profile_picture 
    FROM posts p 
    JOIN users u ON p.user_id = u.user_id 
    WHERE p.user_id = ? 
    ORDER BY p.timestamp DESC";

$post_stmt = execute_query($conn, $post_query, 'i', [$user_id]);
$post_result = $post_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="tabfooter.css">
    <style>
        .profile {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            text-align: center;

        }

        img {
            max-width: 150px;
            border-radius: 50%;
        }

        .post {
            margin: 20px 0;
            border: 1px solid #eee;
            padding: 10px;
            border-radius: 5px;
        }

        .delete-btn {
            color: red;
            cursor: pointer;
        }

        .media-content {
            max-width: 100%;
            border-radius: 5px;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
        }

        .file-input-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-input-wrapper i {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }
    </style>
</head>

<body>
    <div class="profile">
        <div class="profile">

            <form action="profile.php" method="POST" enctype="multipart/form-data">

                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" required>
                <button type="submit">Update</button>
            </form>


            <h1><?php echo htmlspecialchars($user['Name']); ?></h1>



        </div>



        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="profile-stats">
                        <h3 class="m-b-0 font-light">434K</h3>
                        <small>Friends</small>
                    </div>
                </div>
                <div class="col">
                    <div class="profile-stats">
                        <h3 class="m-b-0 font-light">5454</h3>
                        <small>Following</small>
                    </div>
                </div>
                <div class="col">
                    <div class="profile-stats">
                        <a href="#momentsday">Mo Mentsday</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="mt-2">
        <?php while ($post = $post_result->fetch_assoc()): ?>
            <div class="post">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <img src="<?php echo htmlspecialchars($post['profile_picture']); ?>" alt="Profile Picture" class="rounded-circle me-3" style="width: 40px; height: 40px;">
                        </div>
                        <div class="col">
                            <h5 class="card-title">
                                <?php echo isset($user['username']) ? htmlspecialchars($user['username']) : 'Unknown'; ?>
                            </h5>

                            <p class="text-muted">
                                <?php
                                // Get the timestamp from the post data  
                                $post_timestamp = strtotime($post['timestamp']);
                                // Format the timestamp according to your desired format  
                                $formatted_date_time = date("F j, Y, g:i A", $post_timestamp);
                                // Display the formatted date and time  
                                echo $formatted_date_time;
                                ?>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <?php if (!empty($post['media_url'])): ?>
                                <img src="<?php echo htmlspecialchars($post['media_url']); ?>" alt="Post Media" class="media-content img-thumbnail mb-2">
                            <?php endif; ?>
                            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fa-solid fa-heart"></i> <?php echo $post['like_count']; ?> Likes</span>
                                <a href="delete_post.php?post_id=<?php echo $post['post_id']; ?>" class="text-danger delete-btn">Delete</a>
                            </div>
                        </div>
                    </div>

                    <div class="comments-section">
                        <h6>Comments:</h6>
                        <?php
                        // Fetch comments for the current post
                        $comments_query = "SELECT c.content, u.username, c.timestamp, c.comment_id FROM comments c JOIN users u ON c.user_id = u.user_id WHERE c.post_id = ?";
                        $comments_stmt = $conn->prepare($comments_query);

                        if ($comments_stmt === false) {
                            die("ERROR: Could not prepare query: $comments_query. " . $conn->error);
                        }

                        $comments_stmt->bind_param("i", $post['post_id']);
                        $comments_stmt->execute();
                        $comments_result = $comments_stmt->get_result();

                        while ($comment = $comments_result->fetch_assoc()): ?>
                            <div class="comment">
                                <strong><?php echo htmlspecialchars($comment['username']); ?>:</strong>
                                <span><?php echo htmlspecialchars($comment['content']); ?></span>
                                <small class="text-muted">
                                    <?php
                                    $comment_timestamp = strtotime($comment['timestamp']);
                                    echo date("F j, Y, g:i A", $comment_timestamp);
                                    ?>
                                </small>
                                <?php if (isset($_SESSION['username']) && $comment['username'] == $_SESSION['username']): ?>

                                    <a href="profile.php?delete_comment_id=<?php echo $comment['comment_id']; ?>" class="text-danger ms-2">Delete</a>
                                <?php endif; ?>
                            </div>
                        <?php endwhile;

                        if ($comments_stmt) {
                            $comments_stmt->close();
                        }
                        ?>
                        <form action="profile.php" method="POST" class="mt-2">
                            <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                            <textarea name="comment" rows="2" class="form-control mb-2" placeholder="Add a comment..." required></textarea>
                            <button type="submit" class="btn btn-primary btn-sm">Comment</button>
                        </form>
                    </div>

                <?php endwhile; ?>

                </div>


                <footer class="bg-light">
                    <div class="container">
                        <div class="row text-center mt-4 footertab">
                            <div class="col">
                                <a href="home.php" class="text-decoration-none">
                                    <i class="fas fa-home fa-2x footericon" data-page="home.php"></i>
                                </a>
                            </div>
                            <div class="col">
                                <a href="search.php" class="text-decoration-none">
                                    <i class="fas fa-search fa-2x footericon" data-page="search.php"></i>
                                </a>
                            </div>
                            <div class="col">
                                <a href="post.php" class="text-decoration-none">
                                    <i class="fas fa-plus fa-2x footericon" data-page="post.php"></i>
                                </a>
                            </div>
                            <div class="col">
                                <a href="notify.php" class="text-decoration-none">
                                    <i class="fas fa-bell fa-2x footericon" data-page="notify.php"></i>
                                </a>
                            </div>
                            <div class="col">
                                <a href="profile.php" class="text-decoration-none">
                                    <i class="fas fa-user fa-2x footericon" data-page="profile.php"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </footer>
</body>
<script src="./jscode/like.js"></script>
<script src="./jscode/jscode.js"></script>
<script src="./jscode/comment.js"></script>

</html>