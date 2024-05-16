<?php
session_start();

// Include the database connection file
require '/home/altorrad/public_html/connect.php';

// Check if the file parameter is provided
if (isset($_GET['file'])) {
    $fileName = $_GET['file'];
    $filePath = '/DashboardOfficial/' . $directoryName . '/' . $fileName;

    // Check if the file exists
    if (file_exists($filePath)) {
        // Set headers for PDF file
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        // Output the file content
        readfile($filePath);
        exit();
    } else {
        // File not found
        echo 'File not found.';
    }
} else {
    // File parameter not provided
    echo 'Invalid request.';
}
?>
