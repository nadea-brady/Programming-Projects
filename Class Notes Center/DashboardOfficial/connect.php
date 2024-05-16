<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Referrer-Policy: strict-origin-when-cross-origin");

   $servername = "localhost"; // Change this to your database server
   $username = "group5"; // Change this to your database username
   $password = "o1eMiss2024"; // Change this to your database password
   $dbname = "group5"; // Change this to your database name
   
   try {
       $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
       // Set the PDO error mode to exception
       $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //    echo "Connected successfully"; 
   } catch(PDOException $e) {
       echo "Connection failed: " . $e->getMessage();
   }

?>


