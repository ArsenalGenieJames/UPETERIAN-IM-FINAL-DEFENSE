<?php
session_start();
include('db.php');

// Check if user is logged in  
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch posts with like count and profile picture  
$query = "SELECT Posts.*, Users.username, Users.profile_picture,  
                 (SELECT COUNT(*) FROM Likes WHERE post_id = Posts.post_id) as like_count   
          FROM Posts   
          JOIN Users ON Posts.user_id = Users.user_id   
          ORDER BY timestamp DESC";

$result = $conn->query($query);
if (!$result) {
    die("Database query failed: " . $conn->error);
}


?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./stylecss/tabfooter.css">
</head>
<style>
    .heart {
        font-size: 15px;
        cursor: pointer;
    }

    .no-button-style {
        border: none;
        background: none;
        padding: 0;
    }

    .videosize {
        width: 19%;
        height: auto;
    }
</style>

<body style="background-color: white;">
    <header class="py-1">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-auto">
                    <img src="logo.png" alt="secondary logo" style="height: 60px; width: 85px;">
                </div>
            </div>
        </div>
    </header>

    <div id="newsfeed">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="card mb-3">';
                echo '<div class="card-body">';
                // Display profile picture
                $profilePic = !empty($row['profile_picture']) ? htmlspecialchars($row['profile_picture']) : 'default-pic.jpg';
                echo '<div class="d-flex align-items-center">';
                echo '<img src="' . $profilePic . '" alt="Profile Picture" class="rounded-circle me-3" style="width: 40px; height: 40px;">';
                echo '<h5 class="card-title">' . htmlspecialchars($row['username']) . '</h5>';
                echo '</div>';
                echo '<p class="card-text mt-2">' . htmlspecialchars($row['content']) . '</p>';

                // Check if there's a media URL (image or video)
                if (!empty($row['media_url'])) {
                    $media_url = htmlspecialchars($row['media_url']);
                    $file_extension = pathinfo($media_url, PATHINFO_EXTENSION);

                    if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                        echo '<img src="' . $media_url . '" alt="Post Image" class="img-fluid">';
                    } elseif (in_array($file_extension, ['mp4', 'avi', 'mov'])) {
                        echo '<video controls class="img-fluid">';
                        echo '<source src="' . $media_url . '" type="video/' . $file_extension . '">';
                        echo 'Your browser does not support the video tag.';
                        echo '</video>';
                    } else {
                        echo '<p>Unsupported media type.</p>';
                    }
                }

                echo '<p class="card-text mt-2"><small class="text-muted">' . htmlspecialchars($row['timestamp']) . '</small></p>';

                // Display like button
                echo '<div class="container">
        <div class="row">
            <div class="col">
                <form action="like.php" method="post" class="d-inline">
                    <input type="hidden" name="post_id" value="' . htmlspecialchars($row['post_id']) . '">
                    <button type="submit" class="btn btn-sm btn-link" style="text-align: center;">
                        <i class="fa-regular fa-heart heart" style="color: #640003;"></i>
                    </button>
                    <span class="like-count" style="display: none; font-size: 0.9rem; color: #640003;">
                        ' . htmlspecialchars($row['like_count']) . ' Likes
                    </span>
                </form>
            </div>
            <div class="col">
                <button class="btn btn-sm btn-link show-comments" data-post-id="' . htmlspecialchars($row['post_id']) . '">
                    <i class="fa-regular fa-comment"></i>
                </button>
            </div>
            <div class="col">
                <button class="no-button-style">
                    <img src="./assets/repost.svg" alt="Repost" width="100" height="100">
                </button>
            </div>
            <div class="col">
                <button class="no-button-style">
                    <img src="./assets/send.svg" alt="Send" width="100" height="100">
                </button>
            </div>

        </div>
    </div>';

                // Display comments 
                echo '<div class="comments" id="comments-' . htmlspecialchars($row['post_id']) . '" style="display: none;">';
                echo '<h6>Comments:</h6>';

                $comments_query = "SELECT Comments.*, Users.profile_picture, Users.username 
                   FROM Comments
                   JOIN Users ON Comments.user_id = Users.user_id
                   WHERE post_id = ?";
                $stmt_comments = $conn->prepare($comments_query);
                $stmt_comments->bind_param("i", $row['post_id']);
                $stmt_comments->execute();
                $comments_result = $stmt_comments->get_result();

                if ($comments_result->num_rows > 0) {
                    while ($comment = $comments_result->fetch_assoc()) {
                        // Get the commenter's profile picture

                        $profilePic = !empty($comment['profile_picture']) ? htmlspecialchars($comment['profile_picture']) : 'default-pic.jpg';

                        // Output the comment with the correct profile picture
                        echo '<div>';
                        echo '<img src="' . $profilePic . '" alt="Profile Picture" style="width: 30px; height: 30px;"> ';
                        echo '<strong>' . htmlspecialchars($comment['username']) . ':</strong> ' . htmlspecialchars($comment['content']);
                        echo '</div>';
                    }
                } else {
                    echo '<div>No comments yet.</div>';
                }
                // Comment form
                echo '<form action="submitcommit.php" method="post">';
                echo '<input type="hidden" name="post_id" value="' . htmlspecialchars($row['post_id']) . '">';
                echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($_SESSION['user_id']) . '">';
                echo '<div><input type="text" name="content" placeholder="Add a comment..." required class="form-control my-2">';
                echo '<button type="submit" class="btn btn-primary btn-sm">Submit</button></div>';
                echo '</form>';

                echo '</div>'; // Close comments div
                echo '</div>'; // Close card-body
                echo '</div>'; // Close card
            }
        } else {
            echo '<p>No posts yet!</p>';
        }
        ?>
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


    <script src="./jscode/like.js"></script>
    <script src="./jscode/jscode.js"></script>
    <script src="./jscode/comment.js"></script>

</body>

</html>