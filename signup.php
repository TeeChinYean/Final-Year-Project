<?php

require "dataconnection.php";

if (isset($_POST["signup"])) {
  
    // Proceed with form submission
    $adName = $_POST['adminname'];
    $adEmail = $_POST['adminEmail'];
    $adpassword = $_POST['adminPassword'];
    
    // Hash the password with MD5 (Note: Consider using a stronger hashing algorithm)
    $hashedPassword = md5($adpassword);

    // Check if admin ID or email already exists using prepared statement
    $checkQuery = "SELECT * FROM admin WHERE Admin_Email = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "s", $adEmail);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $_SESSION['notification'] = "Admin Email already exists";
        header("Location: signup.php"); // Redirect back to the signup page
        exit;
    } else {
        // Insert new admin using prepared statement
        $insertQuery = "INSERT INTO admin ( Admin_Username, Admin_Password, Admin_Email) VALUES (?,  ?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "sss", $adName, $hashedPassword, $adEmail);
        
        if (mysqli_stmt_execute($stmt)) {
            echo '<script>
                            alert("Admin account created successfully.");
                            setTimeout(function() {
                                window.location.href = "signin.php";
                            }, 000)
                          </script>';
            exit();
        } else {
            echo '<script>
            alert("Failed to create admin account.");
            setTimeout(function() {
                window.location.href = "signup.php";
            }, 000); 
          </script>';
            exit;
        }
    }
}
?>


<script>
     /*
		function onloadCallback() {
			grecaptcha.render('recaptcha', {
				b,
				//for debug
				callback: successcallback
				});
		};

		//for debug Recaptcha
		function successcallback(token){
			debugger;
		}

		//to prevent user skip the recaptcha (make recaptcha required)
		window.onload = function() {
			var $recaptcha = document.querySelector('#g-recaptcha-response');

			if($recaptcha) {
			$recaptcha.setAttribute("required", "required");
			}

            // Add event listener to the form submit button
            document.getElementById('signup').addEventListener('click', function(event) {//change submitBtn to your submit button id
                if (!$recaptcha || !$recaptcha.value) {
                    event.preventDefault(); // Prevent form submission
                    alert("Please complete the reCAPTCHA before proceeding.");
                }
            });
		};
*/
	</script>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'head.html'?>
    <!--<script src="https://www.google.com/recaptcha/api.js" async defer></script>-->
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


        <!-- Sign Up Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-secondary rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <a href="index.html" class="">
                                <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>TKT Sport Shoes</h3>
                            </a>
                            <h3>Sign Up</h3>
                        </div>

                        <!-- Form Start -->
                        <form method="post" action=""  oninput='adminCPassword.setCustomValidity(adminCPassword.value != adminPassword.value ? "Passwords do not match." : "")'><!--onsubmit="return handleFormSubmission()"-->
                        <!--<div class="g-recaptcha" id="recaptcha" data-sitekey="6Lf06tYpAAAAAMi-KcRe-WwPRP9u9QdR9wb4JMsV"></div>-->

                           
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control"  name="adminname" placeholder="Admin UserName" required>
                                <label for="floatingText">Admin Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" name="adminEmail" placeholder="Email" required>
                                <label for="floatingInput">Email</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="adminPassword" name="adminPassword" placeholder="Password"  oninput="checkPasswordLength()" required>
                                <label for="floatingPassword">Password</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="adminCPassword" name="adminCPassword" placeholder="Confirmed Password" required>
                                <label for="floatingPassword">Confirmed Password</label>
                            </div>
                            <button type="submit" class="btn btn-primary" id="signup" name="signup">Sign Up</button>
                            <button type="button" class="btn btn-primary" onclick="back()">Back</button>
                        </form>
                        <!-- Form End -->
                    </div>
                </div>
            </div>
        </div>
        <!-- Sign Up End -->
    </div>

       <!--Main Script-->
       <?php require "js/Main_js.php"?>
</body>

</html>
