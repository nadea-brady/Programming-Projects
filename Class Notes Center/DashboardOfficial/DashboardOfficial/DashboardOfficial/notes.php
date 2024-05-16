<?php
session_start(); // Start the session

// Check if the course_id is provided in the URL
if (isset($_GET['course_id'])) {
    // Retrieve the course_id from the URL query parameters
    $courseId = $_GET['course_id'];

    // Set the course_id as a session variable
    $_SESSION['course_id'] = $courseId;
} else {
    // Redirect to dashboard or display an error message if course_id is not provided
    header("Location: dashboard.php");
    exit();
}

// Retrieve course name and section from URL query parameters
$courseName = isset($_GET['courseName']) ? $_GET['courseName'] : 'Course Name';
$courseSection = isset($_GET['courseSection']) ? $_GET['courseSection'] : 'Course Section';

// Include the database connection file
 require '/home/group5/public_html/connect.php';

// Check if the user is logged in and retrieve the user ID
if (isset($_SESSION['user_id'])) {
    $userID = $_SESSION['user_id'];
} else {
    // Redirect to login page if user is not logged in
    header("Location: login1.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['fileInput'])) {
    $file = $_FILES['fileInput'];

    // File details
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmpName = $file['tmp_name'];
    $timestamp = date('Y-m-d'); // Current date

    // Retrieve course ID from session
    $courseID = isset($_SESSION['course_id']) ? $_SESSION['course_id'] : null;

    // Check if course ID is set
    if (!$courseID) {
        echo "Course ID not set.";
        exit();
    }

    $directoryName = $courseName . "_" . str_replace(' ', '_', $courseSection);
    $destinationDir = "/home/group5/public_html/DashboardOfficial/" . $directoryName . "/";

    // Move uploaded file to the server directory
    $filePath = $destinationDir . $fileName; // Complete file path
     // Corrected path
    // Create the destination directory if it doesn't exist
    if (!file_exists($destinationDir)) {
        if (!mkdir($destinationDir, 0777, true)) {
            // Directory creation failed, provide an error message
            echo "Failed to create the destination directory.";
            exit(); // Stop further execution
        }
    }

    // Move uploaded file to the server directory
    if (move_uploaded_file($fileTmpName, $filePath)) {
        // File uploaded successfully

        try {
            // Insert note details into the database
            $query = "INSERT INTO Notes (Note_Size, Note_Date, Note_Approval, Note_Checked, Users_User_Id, Course_Course_ID, Note_Path, Note_Name) 
                      VALUES (?, ?, 0, 0, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$fileSize, $timestamp, $userID, $courseID, $filePath, $fileName]);

            // Redirect to the same page to avoid form resubmission
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        } catch (PDOException $e) {
            // Print error message
            echo "Error inserting note into database: " . $e->getMessage();
        }
    } else {
        // File upload failed, provide error message
        echo "File upload failed.";
    }
}

// Function to fetch and display notes from the database
function getNotes($conn, $courseID){
    // Prepare SQL query to fetch all notes for the current course
    $query = 'SELECT * FROM Notes WHERE Course_Course_ID = ?';
    $stmt = $conn->prepare($query);
    $stmt->execute([$courseID]);

    // Fetch all notes
    $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);


    //$path = str_replace("/home/altorrad/public_html/","/~altorrad" , ($note['Note_Path']));

    // If no notes found, display a message or return early
    if (empty($notes)) {
        echo '<tr><td colspan="3">No notes found.</td></tr>';
        return;
    }
    
    // Output each note within table rows and cells
    foreach ($notes as $note) {
        $path = str_replace("/home/group5/public_html/","/~group5/" , ($note['Note_Path']));
        echo '<tr>';
        echo '<td>' . htmlspecialchars($note['Note_Date']) . '</td>';
        echo '<td>' . htmlspecialchars($note['Note_Name']) . '</td>';
        echo '<td><a href="' . htmlspecialchars($path) . '" target="_blank">View Note</a></td>'; // Adjusted link
        echo '</tr>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Notes</title>
    <link rel="stylesheet" href="notesreal.css">
</head>
<body>
    <header class="course-header">
        <h1 id="courseTitle"><?php echo htmlspecialchars($courseName); ?></h1> <!-- Display the specific course name -->
    </header>
    <main class="content">
        <section class="upload-section">
            <h2>Upload New Note</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="file" id="fileInput" name="fileInput">
                <button type="submit">Upload</button>
            </form>
        </section>
        <section class="notes-section">
            <h2>Available Notes</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date Uploaded</th>
                        <th>Note Name</th>
                        <th>View Notes</th>
                    </tr>
                </thead>
                <tbody id="notesList">
                    <?php getNotes($conn, $_SESSION['course_id']); ?> <!-- Display notes -->
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
