<?php
require 'dataconnection.php';
date_default_timezone_set('Asia/Kuala_Lumpur');
// Check if session variables are set
if (isset($_SESSION["adminid"]) && isset($_COOKIE["time"])) {
    $adminid = $_SESSION["adminid"];
    $loginTime = date('Y-m-d H:i:s', $_COOKIE["time"]);

    $update_query = mysqli_prepare($conn, "UPDATE time_record SET Logout_Time = CURRENT_TIMESTAMP() WHERE Admin_ID = ? AND Login_Time = ?");
    if (!$update_query) {
        die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
    }   

    mysqli_stmt_bind_param($update_query, "ss", $adminid, $loginTime);
    mysqli_stmt_execute($update_query);
    mysqli_stmt_close($update_query);

    // Unset and destroy the session
    session_unset();
    session_destroy();

    // Remove cookies
    setcookie("login", "", time() - 3600, "/"); // Expire cookie
    setcookie("username", "", time() - 3600, "/"); // Expire cookie
    setcookie("time", "", time() - 3600, "/"); // Expire cookie
    setcookie("adminid", "", time() - 3600, "/"); // Expire cookie
    // Redirect to sign-in page
    header("Location: signin.php");
    exit();
} else {
    // Handle the case where session variables or cookies are not set
    echo "Session variables or cookies not set.";
    header("Location: signin.php");
    exit();
}
?>
