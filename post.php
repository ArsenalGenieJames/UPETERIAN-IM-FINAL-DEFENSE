<?php
session_start();
include('db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details, including profile picture and username
$query = "SELECT profile_picture, username FROM users WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found!");
}

$user = $result->fetch_assoc();

// Handle new post submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];

    // Handle media upload (image/video)
    $media_url = null;
    $post_type = 'text';  // Default to text

    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        // Validate file type and move the uploaded file
        $media_type = $_FILES['media']['type'];

        // Determine post type (image or video)
        if (strpos($media_type, 'image') !== false) {
            $post_type = 'image';
        } elseif (strpos($media_type, 'video') !== false) {
            $post_type = 'video';
        }

        // Generate a unique file name to avoid overwriting
        $target_dir = "uploads/posts/";
        $media_url = $target_dir . basename($_FILES['media']['name']);

        // Move the uploaded file to the target directory
        if (!move_uploaded_file($_FILES['media']['tmp_name'], $media_url)) {
            die("Error uploading the file.");
        }
    }

    // Prepare the SQL query for insertion
    $stmt = $conn->prepare("INSERT INTO posts (user_id, content, post_type, media_url) VALUES (?, ?, ?, ?)");

    if ($stmt === false) {
        die("Error preparing SQL query: " . $conn->error);
    }

    // Bind parameters and execute the statement
    $stmt->bind_param("isss", $user_id, $content, $post_type, $media_url);

    if ($stmt->execute()) {
        header("Location: home.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <title>Post Your Thoughts</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            padding: 20px;
            background-color: white;
            font-family: "Montserrat", serif;
            font-optical-sizing: auto;
            font-weight: <weight>;
            font-style: normal;
        }

        .profile-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
            resize: none;
            border: none;
        }

        .post_content {
            width: 100px;
            height: 40px;
            background-color: #640003;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-left: 150px;
        }

        .post_content:hover {
            color: black;
            background-color: #ffff;
        }

        .exitpost img {
            width: 25px;
        }

        .post_content {
            background-color: #640003;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row m-1 mb-4">
            <div class="col-12 mb-4">
                <a href="home.php" class="exitpost">
                    <img src="./assets/Close_LG.svg" alt="close" class="img-fluid" style="width: 25px;">
                </a>
                <label class="ms-2">Update</label>
            </div>
            
            <form method="POST" enctype="multipart/form-data" class="col-12">
                <div class="profile-section mb-4 d-flex align-items-center">
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="rounded-circle me-3" style="width: 50px;">
                    <b><?php echo htmlspecialchars($user['username']); ?></b>
                    <input type="submit" value="Post" class="btn btn-primary ms-auto post_content">
                </div>

                <div class="mb-3">
                    <textarea name="content" placeholder="What's on your mind?" required class="form-control"></textarea>
                </div>

                <div class="mb-3">
                    <input type="file" name="media" class="form-control">
                </div>

            </form>
        </div>
    </div>

    <script src="./jscode/jscode.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>

</html>