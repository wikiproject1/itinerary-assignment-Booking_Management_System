<?php
session_start();
require_once "config/database.php";

if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: dashboard.php");
    exit;
}

$username = $password = "";
$username_err = $password_err = $login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty($username_err) && empty($password_err)){
        $sql = "SELECT id, username, password FROM admin WHERE username = ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = $username;
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            session_start();
                            
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            header("location: dashboard.php");
                        } else{
                            $login_err = "Invalid username or password.";
                        }
                    }
                } else{
                    $login_err = "Invalid username or password.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Management System - Login</title>
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><circle cx='50' cy='50' r='50' fill='%231a2a6c'/><text x='50' y='50' font-size='45' font-weight='bold' font-family='Arial' fill='white' text-anchor='middle' dominant-baseline='central'>TS</text></svg>" type="image/svg+xml">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1a2a6c 0%, #2a4858 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 40px 20px;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }
        .login-title {
            text-align: center;
            color: #1a2a6c;
            margin-bottom: 2rem;
            font-weight: 600;
        }
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
        }
        .form-control:focus {
            border-color: #1a2a6c;
            box-shadow: 0 0 0 0.2rem rgba(26, 42, 108, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #1a2a6c 0%, #2a4858 100%);
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 42, 108, 0.4);
        }
        .footer {
            text-align: center;
            color: white;
            padding: 1rem;
            width: 100%;
            margin-top: 2rem;
        }
        .footer a {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 0 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }
        .footer a:hover {
            color: #a8c0ff;
            transform: translateY(-2px);
        }
        .footer i {
            font-size: 1.2rem;
        }
        .developer-info {
            background: rgba(0, 0, 0, 0.3);
            padding: 15px 30px;
            border-radius: 25px;
            backdrop-filter: blur(10px);
            display: inline-block;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .developer-info a {
            margin: 0 12px;
            padding: 8px 15px;
            border-radius: 20px;
            transition: all 0.3s ease;
            white-space: nowrap;
            font-size: 0.85rem;
        }
        .developer-info a:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            transform: translateY(-2px);
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-right: none;
        }
        .input-group .form-control {
            border-left: none;
        }
        .input-group-text i {
            color: #1a2a6c;
        }
        @media (max-height: 700px) {
            body {
                padding: 20px;
            }
            .login-container {
                margin: 0 auto;
                padding: 1.5rem;
            }
            .footer {
                margin-top: 1rem;
            }
            .developer-info {
                padding: 10px 20px;
            }
            .developer-info a {
                padding: 5px 10px;
                margin: 0 8px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 class="login-title">Travel Management System</h2>
        <?php 
        if(!empty($login_err)){
            echo '<div class="alert alert-danger">' . $login_err . '</div>';
        }        
        ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="mb-4">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                </div>
                <div class="invalid-feedback"><?php echo $username_err; ?></div>
            </div>    
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                </div>
                <div class="invalid-feedback"><?php echo $password_err; ?></div>
            </div>
            <div class="mb-4">
                <button type="submit" class="btn btn-login">Login</button>
            </div>
        </form>
    </div>

    <footer class="footer">
        <div class="developer-info">
            <a href="http://chipmunk-tech.com" target="_blank">
                <i class="bi bi-globe2"></i>
                chipmunk-tech.com
            </a>
            <a href="mailto:alvinchipmunk196@gmail.com">
                <i class="bi bi-envelope-fill"></i>
                alvinchipmunk196@gmail.com
            </a>
            <a href="https://github.com/chipmunk-tech" target="_blank">
                <i class="bi bi-github"></i>
                GitHub
            </a>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 