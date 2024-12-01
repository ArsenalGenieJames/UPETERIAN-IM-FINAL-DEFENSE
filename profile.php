<?php
session_start();
include 'db.php'; // Ensure this points to your actual DB connection file  

// Check if the user is logged in  
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user's profile information  
$user_id = $_SESSION['user_id'];
$query = "SELECT Name, Email, profile_picture, username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
// Check if preparing the statement was successful  
if ($stmt === false) {
    die("ERROR: Could not prepare query: $query. " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if we fetched a user  
if ($result->num_rows === 0) {
    die("No user found with ID: " . htmlspecialchars($user_id));
}

$user = $result->fetch_assoc();
$stmt->close(); // Close the statement

// Update profile picture if a new one is uploaded  
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $upload_dir = "uploads/profile_pictures/"; // Ensure this directory exists and is writable
    $filename = uniqid("profile_") . '.' . pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
    $target_file = $upload_dir . $filename;
    $upload_ok = 1;

    // Check if the file is an image  
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check === false) {
        echo "<script>alert('File is not an image.');</script>";
        $upload_ok = 0;
    }

    // Check file size (limit to 2MB)  
    if ($_FILES["profile_picture"]["size"] > 2000000) {
        echo "<script>alert('Sorry, your file is too large. Maximum size is 2MB.');</script>";
        $upload_ok = 0;
    }

    // Allow certain file formats  
    $allowed_formats = ["jpg", "png", "jpeg", "gif", "jfif"];
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if (!in_array($file_type, $allowed_formats)) {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');</script>";
        $upload_ok = 0;
    }

    // Check if file upload is valid  
    if ($upload_ok === 1) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Update the database
            $update_query = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $target_file, $user_id);
            if ($update_stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Profile picture updated successfully!", "profile_picture" => $target_file]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to update the profile picture in the database."]);
            }
            $update_stmt->close();
        } else {
            echo json_encode(["success" => false, "message" => "Failed to move uploaded file."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "File upload validation failed."]);
    }
    $conn->close();
    exit();
}



$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./stylecss/tabfooter.css">
    <link rel="stylesheet" href="./stylecss/profile.css">
    <title>Profile</title>
</head>
<style>
    .post-profile {
        border: none;
        width: 40px;
        height: 40px;
    }

    .caption-post {
        resize: none;
        border: none;
    }

    .post-profile {
        position: flex;
        display: inline-block;
        width: fit-content;
    }

    .post-profile input[type="file"] {
        position: absolute;
        top: 60px;
        right: 20px;
        width: 50%;
        height: 50%;
        opacity: 0;
        cursor: pointer;
        border-radius: 80%;
    }

    .post-profile img {
        cursor: pointer;
        width: 40px;
        height: 40px;
        border-radius: 80%;
    }
</style>

<body>
    <div class="profile">

        <form action="profile.php" method="POST" enctype="multipart/form-data" id="profile-form">
            <img id="profile-img" class="profile-img" src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
            <div class="file-input-wrapper">
                <div class="icon-wrapper">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*" required>
            </div>
        </form>

        <h2 class="mt-2 username">@<?php echo htmlspecialchars($user['Name']); ?></h2>

        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="profile-stats">
                        <h3 class="m-b-0 mmday">434K</h3>
                        <small style="color: #640003;">Friends</small>
                    </div>
                </div>
                <div class="col">
                    <div class="profile-stats">
                        <h3 class="m-b-0 mmday">5454</h3>
                        <small style="color: #640003;">Followers</small>
                    </div>
                </div>
                <div class="col">
                    <div class="profile-stats">
                        <a href="#momentsday" class="mmday mt-0">Mo Mentsday</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form method="POST" enctype="multipart/form-data" class="col-12 mt-2">
                    <div class="profile-section mb-2 d-flex align-items-center">
                        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="rounded-circle me-3 post-profile">
                        <p><?php echo htmlspecialchars($user['username']); ?></p>
                        <input type="submit" value="Post" class="btn btn-primary ms-auto post_content">
                    </div>

                    <div class="mb-2">
                        <textarea name="content" placeholder="What's on your mind?" required class="form-control caption-post"></textarea>

                        <div class="post-profile">
                            <img src="./assets/Vector 10.png" alt="Upload Media">
                            <input type="file" name="media" class="form-control" required>
                        </div>

                    </div>
                </form>
            </div>
            <a href="login.php">logout</a>
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
            <script src="./jscode/profile.js"></script>
            <script src="./jscode/jscode.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>

</html>