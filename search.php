<?php
session_start();
include('db.php');

// Check if the user is logged in  
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id']; // Use the logged-in user's ID  

// Handle AJAX requests  
if (isset($_POST['action'])) {
    $friend_id = intval($_POST['friend_id']); // Sanitize input  

    if ($_POST['action'] === 'add_friend') {
        $check_sql = "SELECT * FROM friendships WHERE user_id = ? AND friend_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ii", $current_user_id, $friend_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            $insert_sql = "INSERT INTO friendships (user_id, friend_id, status) VALUES (?, ?, 'pending')";
            $stmt2 = $conn->prepare($insert_sql);
            $stmt2->bind_param("ii", $current_user_id, $friend_id);
            if ($stmt2->execute()) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            echo "exists";
        }
    } elseif ($_POST['action'] === 'cancel_friend') {
        $delete_sql = "DELETE FROM friendships WHERE user_id = ? AND friend_id = ? AND status = 'pending'";
        $stmt3 = $conn->prepare($delete_sql);
        $stmt3->bind_param("ii", $current_user_id, $friend_id);
        if ($stmt3->execute()) {
            echo "canceled";
        } else {
            echo "error";
        }
    }
    exit;
}

// Search for users  
$search_results = null; // Initialize  
if (isset($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";
    $sql = "SELECT user_id, Name, username, profile_picture FROM users WHERE Name LIKE ? OR username LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $search_results = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Users</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="./stylecss/tabfooter.css">
    <style>
        body {
            background: white;
        }

        .user-card {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .user-card img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-card a {
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            flex-grow: 1;
        }

        .user-card button {
            margin-left: auto;
            padding: 5px 10px;
            border-color: black;
            background-color: #ffff;
            color: black;
            border-radius: 10px;
            cursor: pointer;
            border: 2%;
        }

        .user-card button.cancel {
            background-color: #dc3545;
        }

        .serach_placeholder::placeholder {
            text-align: center;
        }
    </style>
</head>

<body>
    <form method="GET" class="mt-2">
        <div class="input-group">
            <input type="text" name="search" placeholder="Search" class="form-control border-0 serach_placeholder" />
        </div>
    </form>

    <div>
        <?php if ($search_results && $search_results->num_rows > 0): ?>
            <?php while ($row = $search_results->fetch_assoc()): ?>
                <div class="user-card">
                    <a href="profile.php?user_id=<?php echo $row['user_id']; ?>">
                        <img src="<?php echo $row['profile_picture'] ?: 'default.jpg'; ?>" alt="Profile Picture">
                        <div>
                            <strong><?php echo htmlspecialchars($row['Name']); ?></strong>
                        </div>
                    </a>
                    <button
                        class="add-friend-btn"
                        data-friend-id="<?php echo $row['user_id']; ?>"
                        data-action="add_friend">
                        Follow
                    </button>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="d-flex justify-content-center align-items-center">No users found.</p>
        <?php endif; ?>
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

    <script src="./jscode/jscode.js"></script>
    <script>
        document.querySelectorAll('.add-friend-btn').forEach(button => {
            button.addEventListener('click', function() {
                const friendId = this.getAttribute('data-friend-id');
                const action = this.getAttribute('data-action');
                const btn = this;

                fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            action: action,
                            friend_id: friendId,
                        }),
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (data === 'success') {
                            btn.textContent = 'Unfollow';
                            btn.setAttribute('data-action', 'cancel_friend');
                            btn.classList.add('cancel');
                        } else if (data === 'canceled') {
                            btn.textContent = 'Follow';
                            btn.setAttribute('data-action', 'add_friend');
                            btn.classList.remove('cancel');
                        } else if (data === 'exists') {
                            alert('Friend request already exists!');
                        } else {
                            alert('Error occurred!');
                        }
                    })
                    .catch(err => console.error('Error:', err));
            });
        });
    </script>
</body>

</html>