<?php
// Mengambil file konfigurasi database
include 'config.php';

// Memulai sesi
session_start();



// Memeriksa apakah form sudah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Query untuk mendapatkan data pengguna dari database
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    // Memeriksa apakah pengguna ditemukan
    if ($result->num_rows > 0) {
        // Mengambil data pengguna
        $user = $result->fetch_assoc();

        // Memverifikasi password
        if (password_verify($password, $user['password'])) {
            // Password cocok, login berhasil
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['loggedin'] = true;

            // Redirect ke halaman dashboard atau halaman lain setelah login
            header("Location: index.php");
            exit;
        } else {
            // Password tidak cocok
            $error = "Invalid password.";
        }
    } else {
        // Pengguna tidak ditemukan
        $error = "No user found with that username.";
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskly</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
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

    .alert-purple {
        background-color: #6f42c1; /* Purple background */
        color: white; /* White text color */
        padding: 15px 20px;
        border-radius: 5px;
        border: 1px solid #563d7c; /* Darker purple border */
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
        font-size: 16px;
        font-weight: bold;
        display: flex;
        align-items: center;
    }

    .alert-purple .alert-icon {
        margin-right: 10px; /* Space between icon and text */
        font-size: 20px;
    }
    .alert-purple .close-btn {
        margin-left: auto; /* Push close button to the right */
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
    }
    
    
    
    </style>
</head>
<body>
    <div class="wrapper">
        <form method="POST" action="login.php">
            <h1>Taskly</h1>
            
            <!-- Menampilkan pesan error jika login gagal -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>
            <div class="input-box">
                <input type="text" name="username" id="username" placeholder="username" required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" name="password" id="password" placeholder="password" required>
                <i class='bx bxs-lock-alt'></i>
            </div>
            <?php
                // Display success message if it exists
                if (isset($_SESSION['show_alert']) && $_SESSION['show_alert'] === true) {
                    echo "<div class='alert alert-purple' role='alert'>
                            <span class='alert-icon'>🔔</span> Registration successful! Please log in.
                            <button class='close-btn' onclick='this.parentElement.style.display=\"none\";'>&times;</button>
                        </div>";
                    // Remove the alert trigger after displaying it
                    unset($_SESSION['show_alert']);
                }
            ?>
            <button type="submit" class="btn">login</button>
            <div class="register-link">
                <p>Don't have an account? <a href="register.php">register</a></p>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="script.js"></script>
</body>
</html>

