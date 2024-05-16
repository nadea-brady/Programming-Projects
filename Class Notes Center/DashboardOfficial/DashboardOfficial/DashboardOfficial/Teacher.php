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
    $stmt = $conn->prepare("DELETE FROM UsersCourse WHERE Users_User_ID = :user_id AND Course_Course_ID = :course_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':course_id', $courseId);
    
    // Execute the statement
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["course_id"])) {
    $courseId = $_POST["course_id"];
    if (deleteFromUserCourses($courseId, $userID, $conn)) {
        header("Location: Teacher.php");
        exit();
    } else {
        echo "Failed to delete course from your classes.";
    }
}

// Define the getClasses function
function getClasses($userID, $conn) {
    $query = 'SELECT c.Course_ID, c.Course_Section, c.Course_Name 
              FROM UsersCourse uc 
              INNER JOIN Course c ON uc.Course_Course_ID = c.Course_ID 
              WHERE uc.Users_User_Id = ?';
    $stmt = $conn->prepare($query);
    $stmt->execute([$userID]);

    $classes = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $classes[] = $row;
    }

    if (empty($classes)) {
        echo '<p>No classes found.</p>';
        return;
    }

    foreach ($classes as $class) {
        $backgroundColors = ['#FF5733', '#C70039', '#900C3F', '#581845', '#FFC300', '#FF5733', '#DAF7A6', '#FFC300', '#FF5733', '#FF5733'];
        $randomColor = $backgroundColors[array_rand($backgroundColors)];
        echo '<div class="student--card new-student-card" data-course-id="' . $class['Course_ID'] . '" data-course-name="' . htmlspecialchars($class['Course_Name']) . '" data-course-section="' . htmlspecialchars($class['Course_Section']) . '" style="background-color: ' . $randomColor . ';">';
        echo '<div class="card--header"><span class="title">' . htmlspecialchars($class['Course_Section']) . '</span></div>';
        echo '<div class="card--footer"><span class="title">' . htmlspecialchars($class['Course_Name']) . '</span><button type="submit">Delete</button></div>';
        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Ole Notes</title>
    <link rel="stylesheet" href="Teacher.css">
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
                <a href="Teacher.php">  
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="faq.html"> 
                    <i class="fas fa-question-circle"></i>
                    <span>FAQ</span>
                </a>
            </li>
            <li class="logout">
                <a href="login1.php"> 
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Log Out</span>
                </a>
            </li>
            <li class="add--Button">
                <a href="AddCourseProf.php"> 
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
                <?php getClasses($userID, $conn); ?>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var courseCards = document.querySelectorAll('.new-student-card');
        courseCards.forEach(function(card) {
            card.addEventListener("click", function() {
                var courseId = this.dataset.courseId;
                var courseName = this.dataset.courseName;
                var courseSection = this.dataset.courseSection;
                window.location.href = `professor_notes.php?course_id=${courseId}&courseName=${encodeURIComponent(courseName)}&courseSection=${encodeURIComponent(courseSection)}`;
            });
        });
    });
    </script>
</body>
</html>

