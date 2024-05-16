<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Page</title>
    <link rel="stylesheet" href="login_style.css"> 
</head>
<body>

    <header>
        <h2 class = "logo">Ole Notes</h2>
    </header>

    <div class="bottombar">

    </div>
    
    <div class="login-container">
       <h1>Welcome To Ole Notes!</h1> 
        <h2>Login Form</h2>
        <?php
        session_start(); // Start the session
        require '/home/group5/public_html/connect.php'; // Adjust the path to connect.php as needed

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get form data
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Prepare SQL statement
            $stmt = $conn->prepare("SELECT * FROM Users WHERE User_Email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Check if user exists
            if ($stmt->rowCount() > 0) {
                // User exists, fetch user data
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verify hashed password
                if (password_verify($password, $user['User_Password'])) {
                    // Passwords match, user is authenticated
                    // Store user ID in session
                    $_SESSION['user_id'] = $user['User_Id'];
                    $_SESSION['user_type'] = $user['User_Type'];
                    
                    // Redirect to dashboard after successful login
                    if ($user['User_Type'] == 'Admin') {
                        header("Location: admin_dashboard_mUsers.php");
                    } elseif ($user['User_Type'] == 'Student') {
                        header("Location: dashboard.php");
                    }
                    elseif ($user['User_Type'] == 'Professor') {
                        header("Location: Teacher.php");
                    }
                    exit;
                } else {
                    // Passwords don't match
                    echo "Invalid email or password. Please try again.";
                }
            } else {
                // User does not exist
                echo "Invalid email or password. Please try again.";
            }
        }
        ?>
        <form action="login1.php" method="POST">
            <div class="form-group">
                <span class = "icon">
                    <ion-icon name="mail-outline"></ion-icon>
                </span>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <span class = "icon">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                </span>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Login">
                <button type="button" onclick="location.href='signup.php';" class="register-button">Register An Account</button>

            
            </div>
            <button type="button" onclick="location.href='homepage.html';" class="home-button">Back to Homepage</button>

        </form>
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js">
    </script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script> 
</body>
</html>
