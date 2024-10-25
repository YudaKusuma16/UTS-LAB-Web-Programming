<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskly - Forgot Password</title>
    <link rel="stylesheet" href="style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <form id="forgotPasswordForm">
            <h1>Forgot Password</h1>
            <div class="input-box">
                <input type="email" id="resetEmail" placeholder="enter your email" required>
                <i class='bx bxs-envelope'></i>
            </div>
            <button type="submit" class="btn">reset password</button>
            <div class="login-link">
                <p>Remember your password? <a href="index.html">login</a></p>
            </div>
        </form>
    </div>
    <script src="script.js"></script>
</body>
</html>
