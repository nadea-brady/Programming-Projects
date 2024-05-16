<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup Page</title>
    <link rel="stylesheet" href="signup_style.css"> 
</head>
<body>
    <div class="signup-container">
        <h1>Register An Account</h1>

        <?php
        
        require '/home/group5/public_html/connect.php';
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Get form data
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $accountType = $_POST['accountType'];
            
           
            if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($accountType)) {
                echo "All fields are required.";
            } elseif ($password != $confirm_password) {
                echo "Passwords do not match.";
            }
            elseif (!preg_match('/[A-Z]/', $password)) {
                echo "Password must contain at least one capital letter.";
            } else {
                // Check if email or username is already in use
                // You need to implement this based on your database structure and query
        
                // Example: Check if email already exists
                $sql = "SELECT * FROM Users WHERE User_Email = :email";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                if ($stmt->rowCount() > 0) {
                    echo "Email is already in use.";
                }
                else{$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                    $sql = "INSERT INTO Users (User_Name, User_Email, User_Password, User_Type) VALUES (:name, :email, :password, :accountType)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':name', $name);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $password); // Store hashed password
                    $stmt->bindParam(':accountType', $accountType);
                    $stmt->execute();
                    
                    // Redirect to another page after successful signup
                    header("Location: login1.php");
                    exit;}
        }
    }
        
        
        ?>
        <form action="signup.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Signup">
            </div>
            <div class="form-group">
                <label for="accountType">I am:</label>
                <select name="accountType" id="accountType">
                    <option value="student">Student</option>
                    <option value="Professor">Professor</option>
                </select>
            </div>
            <div class="form-group">
                
                <button type="button" onclick="location.href='login1.php';">Already Have An Account</button>
                
              
            </div>
            <div class="form-group">
                <button type="button" onclick="location.href='homepage.html';" class="home-button">Back to Homepage</button>
            </div>

        </form>
    </div>
</body>
</html>
