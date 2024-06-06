<?php
require 'dataconnection.php';

// Set the default timezone to your desired timezone
date_default_timezone_set('Asia/Kuala_Lumpur');


// Check if the user has blocked cookies
if (!isset($_COOKIE['test_cookie'])) {
    setcookie('test_cookie', 'test', time() + 3600, '/'); // Set a test cookie
    echo '<script>
        if (document.cookie.indexOf("test_cookie") === -1) {
            alert("Cookies are blocked. Please enable cookies to use this site.");
        }
    </script>';
}
// Check if the 'login' cookie is set and has a value of 'true'
if (isset($_COOKIE['login']) && $_COOKIE['login'] === 'true') {
    // Set session variables using cookie values
    $_SESSION["login"] = true;
    $_SESSION["username"] = $_COOKIE["username"];
    $_SESSION["adminid"] = $_COOKIE["adminid"];
    header("Location: index.php");
    exit(); // Make sure to exit after header redirection
}

if (isset($_POST["signin"])) {
    $lemail = isset($_POST['Email']) ? $_POST['Email'] : '';
    $lpassword = md5($_POST['Password']); // Hash the password using md5

    // Check if email exists
    $login_query = mysqli_prepare($conn, "SELECT * FROM admin WHERE Admin_Email=?");
    if (!$login_query) {
        die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($login_query, "s", $lemail);
    mysqli_stmt_execute($login_query);
    $result = mysqli_stmt_get_result($login_query);
    $lresult = mysqli_fetch_assoc($result);

    if ($lresult) {
        // Verify the provided password with the hashed password stored in the database
        if ($lpassword === $lresult["Admin_Password"]) {
            // Capture the current timestamp
            $loginTime = time();

            // Convert the timestamp to MySQL DATETIME format
            $loginTimeFormatted = date('Y-m-d H:i:s', $loginTime);

            $record_query = mysqli_prepare($conn, "INSERT INTO time_record (Admin_ID, Login_Time) VALUES (?, ?)");
            if (!$record_query) {
                die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
            }

            mysqli_stmt_bind_param($record_query, "ss", $lresult["Admin_ID"], $loginTimeFormatted);
            mysqli_stmt_execute($record_query);
                            
            // Close the statement
            mysqli_stmt_close($record_query);

            // Set session variables
            $_SESSION["login"] = true;
            $_SESSION["Email"] = $lresult["Admin_Email"];
            $_SESSION["username"] = $lresult["Admin_Username"];
            $_SESSION["adminid"] = $lresult["Admin_ID"];
            
            // Set cookies for login status, username, and time with a one-month expiration
            setcookie("login", "true", time() + (30 * 24 * 60 * 60), "/"); // 30 days
            setcookie("username", $lresult["Admin_Username"], time() + (30 * 24 * 60 * 60), "/"); // 30 days
            setcookie("time", $loginTime, time() + (30 * 24 * 60 * 60), "/"); // 30 days - using captured timestamp
            setcookie("adminid", $lresult["Admin_ID"], time() + (30 * 24 * 60 * 60), "/"); // 30 days
            // Redirect to homepage
            header("Location: index.php");
            exit();
        } else {
            echo '<script>alert("Password does not match");</script>';
        }
    } else {
        echo '<script>alert("Email is not registered");</script>';
    }

    mysqli_stmt_close($login_query);
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
   <?php include 'head.html'?>
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sign In Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-secondary rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="index.html" class="">
                                <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>TKT Sport Shoes</h3>
                            </a>
                            <h3>Sign In</h3>
                        </div>

                        <!-- Form Start -->
                        
                        <form method="post" id="signin"action="">
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="Email" placeholder="Email">
                                <label for="floatingInput">Email address</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control"name="Password" placeholder="Password">
                                <label for="floatingPassword">Password</label>
                            </div>
                            
                            <button type="submit" name="signin" class="btn btn-primary py-3 w-100 mb-4">Sign In</button>
                        </form>
                        <!-- Form End -->

                    </div>
                </div>
            </div>
        </div>
        <!-- Sign In End -->
    </div>
        <!--Main Script-->
        <?php require "js/Main_js.php"?>

</body>

</html>