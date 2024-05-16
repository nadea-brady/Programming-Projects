<?php
session_start(); // Start the session

// Check if the user is logged in and retrieve the user ID
if (isset($_SESSION['user_id'])) {
    $userID = $_SESSION['user_id'];
} else {
    // Redirect to login page if user is not logged in
    header("Location: login1.php");
    exit();
}

// Include the database connection file
require '/home/group5/public_html/connect.php';

// Function to fetch and display all notes with their corresponding course names and user names
function getAllNotes($conn) {
    // Prepare SQL query to fetch all notes with their corresponding course names and user names
    $query = 'SELECT Notes.*, Course.Course_Name, Users.User_Name 
              FROM Notes 
              INNER JOIN Course ON Notes.Course_Course_ID = Course.Course_ID 
              INNER JOIN Users ON Notes.Users_User_Id = Users.User_Id';
    $stmt = $conn->query($query);

    // Fetch all notes
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no notes found, display a message or return early
    if (empty($notes)) {
        echo '<tr><td colspan="6">No notes found.</td></tr>';
        return;
    }
    
    // Output each note within table rows and cells
    foreach ($notes as $note) {
        $path = str_replace("/home/altorrad/public_html/","/~altorrad/" , ($note['Note_Path']));
        echo '<tr>';
        echo '<td>' . htmlspecialchars($note['Note_Date']) . '</td>';
        echo '<td>' . htmlspecialchars($note['Note_Name']) . '</td>';
        echo '<td>' . htmlspecialchars($note['Course_Name']) . '</td>'; // Display the course name
        echo '<td>' . htmlspecialchars($note['User_Name']) . '</td>'; // Display the user who uploaded the note
        echo '<td><a href="' . htmlspecialchars($path) . '" target="_blank">View Note</a></td>'; // Adjusted link
        echo '<td><form method="POST"><input type="hidden" name="note_id" value="' . $note['Note_ID'] . '"><button type="submit" name="delete_note">Delete</button></form></td>';
        echo '</tr>';
    }
}


// Function to delete a note
function deleteNote($conn, $noteId){
    try {
        // Begin a transaction
        $conn->beginTransaction();

        // Prepare SQL statements to delete the note
        $query = 'DELETE FROM Notes WHERE Note_ID = :note_id';
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':note_id', $noteId);
        $stmt->execute();

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

// Check if the delete note form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_note"])) {
    // Get the note ID from the form
    $noteId = $_POST["note_id"];
    
    // Call the deleteNote function
    if (deleteNote($conn, $noteId)) {
        // Redirect back to the same page after deletion
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        // Show an error message if deletion fails
        echo "Failed to delete note.";
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
            <li>
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
            <li class = "active">
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
                <h3>Notes Management</h3>
                <table id="userTable">
            <table>
                <thead>
                    <tr>
                        <th>Date Uploaded</th>
                        <th>Note Name</th>
                        <th>Course Name</th>
                        <th>User</th>
                        <th>View Notes</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody id="notesList">
                    <?php getAllNotes($conn); ?> <!-- Display all notes -->
                </tbody>
            </table>
        </section>
</div>
</body>
</html>
