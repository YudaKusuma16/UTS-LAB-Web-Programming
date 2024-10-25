<?php
session_start();  // Start the session to store success message
include 'config.php'; // Koneksi ke database

// Memeriksa apakah form sudah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validasi email format untuk admin
    if (strpos($email, '.admin@gmail.com') !== false && !preg_match("/\.admin@gmail.com$/", $email)) {
        echo "Only admin emails must be in the format 'name.admin@gmail.com'.";
        exit;
    }

    // Hashing password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Query untuk memasukkan data ke database
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        // Set session variable to show alert
        $_SESSION['show_alert'] = true;
        // Redirect ke halaman login setelah sukses
        header("Location: login.php");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskly - Register</title>
    <link rel="stylesheet" href="style.css">
    <style>
        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Poppins", sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url(assets/img.jpg) no-repeat center center fixed;
            background-size: cover;
        }

        .wrapper {
            width: 90%;
            max-width: 420px;
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.8);
            color: #fff;
            border-radius: 10px;
            padding: 30px 40px;
        }

        .wrapper h1 {
            font-size: 36px;
            text-align: center;
        }

        .wrapper .input-box {
            width: 100%;
            height: 50px;
            margin: 30px 0;
            position: relative;
        }

        .input-box input {
            width: 100%;
            height: 100%;
            background: transparent;
            border: none;
            outline: none;
            border: 2px solid rgba(255, 255, 255, 0.8);
            border-radius: 40px;
            font-size: 16px;
            color: #fff;
            padding: 20px 45px 20px 20px;
        }

        .input-box input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .wrapper .remember-forgot {
            display: flex;
            justify-content: space-between;
            font-size: 14.5px;
            margin: -15px 0 15px;
        }

        .remember-forgot label input {
            accent-color: #fff;
            margin-right: 3px;
        }

        .remember-forgot a {
            color: #fff;
            text-decoration: none;
        }
            
        .remember-forgot a:hover {
            text-decoration: underline;
        }

        .wrapper .btn {
            width: 100%;
            height: 45px;
            background: #fff;
            border: none;
            outline: none;
            border-radius: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            cursor: pointer;
            font-size: 16px;
            color: #333;
            font-weight: 600;
        }

        .wrapper .register-link,
        .wrapper .login-link {
            font-size: 14.5px;
            text-align: center;
            margin-top: 20px;
        }

        .register-link p a,
        .login-link p a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link p a:hover,
        .login-link p a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .wrapper {
                padding: 20px;
            }

            .wrapper h1 {
                font-size: 28px;
            }

            .input-box input {
                font-size: 14px;
            }

            .remember-forgot,
            .register-link,
            .login-link {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <form id="registerForm" method="POST" action="register.php">
            <h1>Register</h1>
            <div class="input-box">
                <input type="text" name="username" placeholder="username" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="email" name="email" placeholder="email" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" placeholder="password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <button type="submit" class="btn">register</button>
            <div class="login-link">
                <p>Already have an account? <a href="index.php">login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
