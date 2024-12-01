<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and bind  
    $stmt = $conn->prepare("SELECT * FROM Users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, now verify password  
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session  
            $_SESSION['user_id'] = $user['user_id'];
            header("Location: home.php"); 
            exit();
        } else {
            echo "Invalid password.";
        }
    } 
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100 ">
            
            <div class="col-md-6 col-lg-4 logcon mt-5">
                <div class="col-12 d-flex justify-content-center">
                    <img src="secondary_lgo.png" class="img-fluid w-50 h-50" alt="secondary logo">
                </div>

                <div class="card mt-5 ">
                    <div class="card-body text-center p-5  ">
                        <form action="login.php" method="post" class="mt-3 p-2 mb-5">
                            <div class="mb-3 text-start">
                                <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Email" required>
                            </div>
                            <div class="mb-3 text-start">
                                <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                            </div>
                            <div class="mb-3 form-check text-start">
                                <input type="checkbox" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                            <button type="submit" class="btn w-100 login-set">Log In</button>
                            <div class="text-center mt-3">
                                <p>Don't have an account? <a href="register.php" class="text-decoration-none">Create Account</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>