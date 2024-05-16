<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login1.php");
    exit();
}

// Retrieve user ID from session
$userID = $_SESSION['user_id'];

// Include the database connection file
 require '/home/group5/public_html/connect.php';

// Define the deleteFromUserCourses function
function deleteFromUserCourses($courseId, $userId, $conn) {
    // Prepare statement to delete the course from the user_courses table
    $stmt = $conn->prepare("DELETE FROM UsersCourse WHERE Users_User_ID = :user_id AND Course_Course_ID = :course_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':course_id', $courseId);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Return true if course deleted successfully
        return true;
    } else {
        // Return false if course deletion failed
        return false;
    }
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["course_id"])) {
    // Get the course ID from the form
    $courseId = $_POST["course_id"];

    // Delete the course from the user's classes
    if (deleteFromUserCourses($courseId, $userID, $conn)) {
        // Refresh the page after deleting the course
        header("Location: dashboard.php");
        exit();
    } else {
        // Course deletion failed
        echo "Failed to delete course from your classes.";
    }
}

// Define the getClasses function
// Define the getClasses function
function getClasses($userID, $conn){
    // Prepare SQL query to fetch class details for the user from the UsersCourse and Course tables
    $query = 'SELECT c.Course_ID, c.Course_Section, c.Course_Name 
              FROM UsersCourse uc 
              INNER JOIN Course c ON uc.Course_Course_ID = c.Course_ID 
              WHERE uc.Users_User_Id = ?';
    $stmt = $conn->prepare($query);
    $stmt->execute([$userID]);

    // Initialize an array to store class details
    $classes = [];

    // Fetch all class details associated with the user
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $classes[] = $row;
    }

    // If no classes found, display a message or return early
    if (empty($classes)) {
        echo '<p>No classes found.</p>';
        return;
    }

    // Output each class as a card-like element
    foreach ($classes as $class) {
        // Generate a random background color
        $backgroundColors = ['#FF5733', '#C70039', '#900C3F', '#581845', '#FFC300', '#FF5733', '#DAF7A6', '#FFC300', '#FF5733', '#FF5733'];
        $randomColor = $backgroundColors[array_rand($backgroundColors)];

        echo '<div class="student--card new-student-card" style="background-color: ' . $randomColor . ';">';
        echo '<div class="card--header">';
        echo '<div class="amount">';
        echo '<span class="title">' . htmlspecialchars($class['Course_Section']) . '</span>';
        echo '<span class="amount--value"></span>';
        echo '</div>';
        echo '<i class="fas fa-apple-whole icon"></i>';
        echo '</div>';
        echo '<span class="card--detail">Edit Description</span>';
        echo '<div class="card--footer">';
        echo '<span class="title">' . htmlspecialchars($class['Course_Name']) . '</span>';
        // Add a form with a delete button for each class
        echo '<form method="POST" action="">';
        echo '<input type="hidden" name="course_id" value="' . $class['Course_ID'] . '">';
        echo '<button type="submit">Delete</button>';
        echo '</form>';
        echo '</div>';
        echo '</div>';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Ole Notes</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div class="logo"></div>
        <ul class="menu">
            <li class="top">
                <a href="#">
                    <i class="fas fa-football"></i>
                    <span>Ole Notes</span>
                </a>
            </li>
            <li class="active">
                <a href="#">
                    <a href="dashboard.php"> 
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
            </li>
           
          
            
            <li>
                <a href="#">
                <a href="faq.html"> 
                    <i class="fas fa-question-circle"></i>
                    <span>FAQ</span>
                </a>
            </li>
            <li class="logout">
                <a href="#">
                <a href="login1.php"> 
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </li>
            <li class="add--Button">
                <a href="AddCourse.php"> 
                    <i class="fas fa-square-plus"></i>
                    <span>Add Class</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main--content">
        <div class="header--wrapper">
            <div class="header--title">
                <span>Overview</span>
                <h2>Welcome</h2>
            </div>
            <div class="user--info">
                <div class="search--box">
                    <i class="fa-solid fa-search"></i>
                    <input type="text" placeholder="Search">
                </div>
                <img src="2784403.png" alt="">
            </div>
        </div>
        <div class="card--container">
            <h3 class="main--title">Student</h3>
            <div class="card--wrapper">
                <?php
                // Retrieve classes data for the logged-in student
                // Assuming you have the student's ID stored somewhere
                getClasses($userID, $conn);
                ?>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get all course cards
        var courseCards = document.querySelectorAll('.new-student-card');

        // Loop through each course card
        courseCards.forEach(function(card) {
            // Add click event listener to each card
            card.addEventListener("click", function() {
                // Extract course ID, name, and section from the card
                var courseId = card.querySelector('input[name="course_id"]').value;
                var courseName = card.querySelector('.card--footer .title').textContent.trim();
                var courseSection = card.querySelector('.card--header .title').textContent.trim();
                
                // Redirect to the notes page with course ID, name, and section as query parameters
                window.location.href = `notes.php?course_id=${courseId}&courseName=${encodeURIComponent(courseName)}&courseSection=${encodeURIComponent(courseSection)}`;
            });
        });
    });
</script>


</body>
</html>