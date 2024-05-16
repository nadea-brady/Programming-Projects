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

// Define the getCourses function
function getCourses($conn){
    // Prepare SQL query to fetch all courses
    $query = 'SELECT * FROM Course';
    $stmt = $conn->prepare($query);
    $stmt->execute();

    // Fetch all courses
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If no courses found, display a message or return early
    if (empty($courses)) {
        echo '<tr><td colspan="5">No courses found.</td></tr>';
        return;
    }

    // Output each course within table rows and cells
    foreach ($courses as $course) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($course['Course_Name']) . '</td>';
        echo '<td>' . htmlspecialchars($course['Course_Section']) . '</td>';
        echo '<td>';
        echo '<form method="POST">';
        echo '<input type="hidden" name="course_id" value="' . htmlspecialchars($course['Course_ID']) . '">';
        echo '<button type="submit" name="delete_course">Delete</button>';
        echo '</form>';
        echo '</td>';
        // Add more columns here as needed
        echo '</tr>';
    }
}

function deleteCourse($conn, $courseId){
    try {
        // Begin a transaction
        $conn->beginTransaction();

        // Prepare SQL statements to delete course and related entries
        $query1 = 'DELETE FROM UsersCourse WHERE Course_Course_Id = :course_id'; // Updated parameter name here
        $stmt1 = $conn->prepare($query1);
        $stmt1->bindParam(':course_id', $courseId);
        $stmt1->execute();

        $query2 = 'DELETE FROM Course WHERE Course_ID = :course_id';
        $stmt2 = $conn->prepare($query2);
        $stmt2->bindParam(':course_id', $courseId);
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_course"])) {
    // Get the user ID from the form
    $courseId = $_POST["course_id"];
    
    // Call the deleteUser function
    if (deleteCourse($conn, $courseId)) {
        // Redirect to refresh the page after deletion
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } else {
        // Show an error message if deletion fails
        echo "Failed to delete course.";
    }
}

// Define the createClassDirectory function
function createClassDirectory($courseName, $courseSection) {
    // Define the base path
    $basePath = "/home/altorrad/public_html/DashboardOfficial/";

    // Define the directory name format: classid_classname_classterm
    $directoryName = $courseName . "_" . str_replace(' ', '_', $courseSection);

    // Define the path to the directory
    $directory = $basePath . $directoryName;

    // Check if the directory already exists
    if (!is_dir($directory)) {
        // Create the directory (folder)
        if (mkdir($directory, 0777, true)) {
            return true; // Directory created successfully
        } else {
            return false; // Error creating directory
        }
    } else {
        return true; // Directory already exists
    }
}

// Check if the add form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_course"])) {
    // Get form data
    $courseName = $_POST["course_name"];
    $courseSection = $_POST["course_section"];

    // Call the addCourse function
    if (addCourse($conn, $courseName, $courseSection)) {
        // Redirect to refresh the page after addition
        if(createClassDirectory($courseName, $courseSection)){
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        }
        else{
            echo "Failed to create directory.";
        }
        
    } 
}

// Define the addCourse function
function addCourse($conn, $courseName, $courseSection){
    // Prepare SQL statement to insert new course
    $query = 'INSERT INTO Course (Course_Name, Course_Section) VALUES (:course_name, :course_section)';
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_name', $courseName);
    $stmt->bindParam(':course_section', $courseSection);

    // Execute the statement
    $success = $stmt->execute();
    
    // If the course was added successfully, create the corresponding directory
    if ($success) {
        // Get the ID of the newly inserted course
        $courseId = $conn->lastInsertId();
        // Create the directory for the course
        $directoryCreated = createClassDirectory($courseName, $courseSection);
        
    }}


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
            <li class = "active">
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
        <section class="course-management">
    <h3>Course Management</h3>
    <div class="add-course-form">
        <h4>Add New Course</h4>
        <form method="POST">
            <div class="form-group">
                <label for="course_name">Course Name:</label>
                <input type="text" id="course_name" name="course_name" required>
            </div>
            <div class="form-group">
                <label for="course_section">Course Section:</label>
                <input type="text" id="course_section" name="course_section" required>
            </div>
            <button type="submit" name="add_course">Add Course</button>
        </form>
    </div>
    <table id="courseTable">
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Course Section</th>
                <th>Delete Course</th>
                <!-- Add more headers/columns as needed -->
            </tr>
        </thead>
        <tbody>
            <?php
            // Include the PHP code for fetching and displaying courses
            getCourses($conn);
            ?>
        </tbody>
    </table>
</section>
