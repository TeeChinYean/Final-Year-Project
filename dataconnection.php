<?php
session_start();
$conn= mysqli_connect("localhost","root","","final_year_project(1)") or die;// fill out database name

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>