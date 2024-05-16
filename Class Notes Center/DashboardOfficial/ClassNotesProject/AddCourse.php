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

// Function to add a course to the user's classes
function addCourseToUser($courseId, $userId, $conn) {
    // Check if the user is already enrolled in the selected course
    $stmt = $conn->prepare("SELECT * FROM UsersCourse WHERE Users_User_ID = :user_id AND Course_Course_Id = :course_id");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':course_id', $courseId);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the user is already enrolled in the course, return false
    if ($result) {
        return false;
    }

    // Prepare statement to insert the course into the user_courses table
    $stmt = $conn->prepare("INSERT INTO UsersCourse (Users_User_ID, Course_Course_Id) VALUES (:user_id, :course_id)");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':course_id', $courseId);
    
    // Execute the statement
    if ($stmt->execute()) {
        // Return true if course added successfully
        return true;
    } else {
        // Return false if course addition failed
        return false;
    }
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["course_id"])) {
    // Get the course ID from the form
    $courseId = $_POST["course_id"];

    // Add the course to the user's classes
    if (addCourseToUser($courseId, $userID, $conn)) {
        // Refresh the page after adding the course
        header("Location: AddCourse.php");
        exit();
    } else {
        // Course addition failed
        echo '<script>alert("Failed to add course to your classes.");</script>';
    }
}

// Retrieve all courses from the Course table
$query = "SELECT * FROM Course";
$stmt = $conn->query($query);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Define an array of background colors
$backgroundColors = array('#FF5733', '#C70039', '#900C3F', '#581845', '#FFC300', '#FF5733', '#DAF7A6', '#FFC300', '#FF5733', '#FF5733');

// Shuffle the array to randomize the colors
shuffle($backgroundColors);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Add any additional CSS styles specific to the Add Course page here */
        .class-card {
            border-radius: 20px; /* Add border radius to make the cards oval-shaped */
            padding: 20px; /* Add padding for spacing */
            margin-bottom: 20px; /* Add margin for spacing between cards */
        }

        .card--header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .card--header .amount {
            flex: 1;
            margin-right: 10px;
        }

        .card--header .amount .title {
            font-weight: bold;
        }

        .card--footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <!-- Sidebar content -->
        <div class="logo"></div>
        <ul class="menu">
            <li class="top">
                <a href="#">
                    <i class="fas fa-football"></i>
                    <span>Ole Notes</span>
                </a>
            </li>
            <li class>
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
            <li class="active">
                <a href="AddCourse.php"> 
                    <i class="fas fa-square-plus"></i>
                    <span>Add Class</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="main--content">
        <div class="header--wrapper">
            <!-- Header content -->
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
            <h3 class="main--title">Add Course</h3>
            <div class="card--wrapper">
                <ul>
                    <?php foreach ($courses as $index => $course): ?>
                        <li>
                            <div class="class-card" style="background-color: <?php echo $backgroundColors[$index % count($backgroundColors)]; ?>;">
                                <div class="card--header">
                                    <div class="amount">
                                        <span class="title"><?php echo htmlspecialchars($course['Course_Section']); ?></span>
                                        <span class="amount--value"></span>
                                    </div>
                                    <i class="fas fa-apple-whole icon"></i>
                                </div>
                                
                                <div class="card--footer">
                                    <span class="title"><?php echo htmlspecialchars($course['Course_Name']); ?></span>
                                    
                                    <!-- Form to add course to user's classes -->
                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                                        <input type="hidden" name="course_id" value="<?php echo $course['Course_ID']; ?>">
                                        <button type="submit" class="add-to-your-classes-btn">Add to Your Classes</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
