<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login1.php");
    exit();
}

// Include the database connection file
 require '/home/group5/public_html/connect.php';

// Define the getUsers function
function getUsers($conn){
    // Prepare SQL query to fetch all users
    $query = 'SELECT * FROM Users';
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Fetch all users
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no users found, display a message or return early
    if (empty($users)) {
        echo '<tr><td colspan="5">No users found.</td></tr>';
        return;
    }

    // Output each user within table rows and cells
    foreach ($users as $user) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($user['User_Name']) . '</td>';
        echo '<td>' . htmlspecialchars($user['User_Email']) . '</td>';
        echo '<td>' . htmlspecialchars($user['User_Password']) . '</td>';
        echo '<td>' . htmlspecialchars($user['User_Type']) . '</td>';
        
        echo '<td>';
        echo '<form method="POST">';
        echo '<input type="hidden" name="user_id" value="' . htmlspecialchars($user['User_Id']) . '">';
        echo '<button type="submit" name="delete_user">Delete</button>';
        echo '</form>';
        echo '</td>';
        echo '</tr>';
    }
}

// Define the deleteUser function
function deleteUser($conn, $userId){
    try {
        // Begin a transaction
        $conn->beginTransaction();

        // Prepare SQL statements to delete user and related entries
        $query = 'DELETE FROM UsersCourse WHERE Users_User_Id = :user_id';
        $stmt1 = $conn->prepare($query);
        $stmt1->bindParam(':user_id', $userId);
        $stmt1->execute();

        $query2 = 'DELETE FROM Users WHERE User_Id = :user_id';
        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':user_id', $userId);
        $stmt2->execute();

        // Commit the transaction
        $conn->commit();

        return true; // Deletion successful
    } catch (PDOException $e) {
        // Roll back the transaction if an error occurs
        $conn->rollback();
        echo "Error: " . $e->getMessage(); // Output the error message
        return false; // Deletion failed
    }
}


// Check if the delete form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_user"])) {
    // Get the user ID from the form
    $userId = $_POST["user_id"];
    
    // Call the deleteUser function
    if (deleteUser($conn, $userId)) {
        // Redirect to refresh the page after deletion
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        // Show an error message if deletion fails
        echo "Failed to delete user.";
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Ole Notes</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo"></div>
        <ul class="menu">
            <li class="active">
                <a href="#">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-user"></i>
                    <a href="admin_dashboard_mUsers.php">
                    <span>Manage Users</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-book"></i>
                    <a href="admin_dashboard_mCourses.php">
                    <span>Manage Courses</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-file-alt"></i>
                    <a href="admin_dashboard_mNotes.php">
                    <span>Manage Notes</span>
                </a>
            </li>

            <li class="logout">
                <a href="#">
                <a href="login1.php"> 
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </li>
            
        </ul>
    </div>
    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Admin Area</span>
                <h2>Welcome, Admin</h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="Search">
                </div>
            </div>
        </div>
        <div class="card--container">
            <section class="user-management">
                <h3>User Management</h3>
                <table id="userTable">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>User Email</th>
                            <th>User Password</th>
                            <th>Account Type</th>
                            <th>Delete User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Include the PHP code for fetching and displaying users
                        getUsers($conn);
                        ?>
                    </tbody>
                </table>
            </section>
        </div>
