<?php
require 'dataconnection.php';

$adminID = $_SESSION['adminid'];
$checkExisting = mysqli_prepare($conn, "SELECT * FROM admin WHERE Admin_ID = ?");
if (!$checkExisting) {
    die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
}

mysqli_stmt_bind_param($checkExisting, "s", $adminID);
mysqli_stmt_execute($checkExisting);
$resultExisting = mysqli_stmt_get_result($checkExisting);

if($resultExisting->num_rows > 0){
    $row = mysqli_fetch_assoc($resultExisting);
    $Name = $row["Admin_Username"];
    $password = $row["Admin_Password"]; // assuming this is hashed
    $pImage = $row["Admin_Profile_Image"];
}

if(isset($_POST["update"])) {
    $Name = isset($_POST["Name"]) ? trim($_POST["Name"]) : '';
    $Opassword = isset($_POST["Opassword"]) ? trim($_POST["Opassword"]) : '';
    $Npassword = isset($_POST["Npassword"]) ? trim($_POST["Npassword"]) : '';
    
    $Opassword = md5($Opassword);
    if ($Opassword == $password) {
        $hashedNpassword = md5($Npassword);

        if(isset($_FILES["pImage"]) && $_FILES["pImage"]["error"] == UPLOAD_ERR_OK) {
            $filename = $_FILES["pImage"]["name"];
            $tempname = $_FILES["pImage"]["tmp_name"];
            $folder = "img/" . $filename;

            if (move_uploaded_file($tempname, $folder)) {
                $filename = $folder;
            } else {
                echo "<script>alert('File upload failed. Please try again.')</script>";
                $filename = $pImage;
            }
        } else {
            $filename = $pImage;
        }

        $update = mysqli_prepare($conn, "UPDATE admin SET Admin_Username = ?, Admin_Password = ?, Admin_Profile_Image = ? WHERE Admin_ID = ?");
        if (!$update) {
            die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($update, "ssss", $Name, $hashedNpassword, $filename, $adminID);
        mysqli_stmt_execute($update);

        if(mysqli_stmt_affected_rows($update) > 0) {
            echo "<script>alert('Profile Updated Successfully')</script>";
        } else {
            echo "<script>alert('Profile Update Failed')</script>";
        }
    } else {
        echo "<script>alert('Old Password is incorrect. Please try again.')</script>";
    }
}
?>

<script>
function previewImage(event) {
    var posterPreview = document.getElementById('image');
    var file = event.target.files[0];
    var reader = new FileReader();
    reader.onload = function() {
        posterPreview.src = reader.result;
        posterPreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
}
</script>

<!DOCTYPE html>
<html lang="en">
   <?php include 'head.html'?>
<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Sidebar Start -->
        <?php include 'sidebar.php' ?>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <?php include 'navbar.php' ?>
            <!-- Navbar End -->

            <div>
                <div class="bg-secondary rounded h-100 p-4" style="margin-left:17px; margin-top:15px;margin-right:17px">
                    <div>
                        <h6 class="mb-4">Admin Profile</h6>
                    </div>
                    <div style="display:flex;justify-content:center;border:10px;border-color:aliceblue;">
                        <div>
                            <?php
                                if(!isset($_POST['editprofile'])){
                            ?>
                                <div style="border:3px solid gray;padding:10px">
                                    <div style="border:1px solid white;">   
                                        <?php if(!empty($pImage)): ?>
                                            <div class="mb-3">
                                                <img src="<?php echo $pImage; ?>" id="image" alt="Current Image" width="200" height="200">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div style="margin-top:10px;">
                                        <div class="form-floating mb-3">
                                            <a class="form-control" style="background-color:black;color:white"><?php echo "$Name";?></a>
                                            <label for="floatingPassword" style="color:white">Admin Name</label>
                                        </div>
                                    </div>
                                    <form action="profile.php" method="post">
                                        <button type="submit" class="btn btn-primary" name="editprofile">Edit</button>
                                        <button type="button" class="btn btn-primary" onclick="back()">Back</button>
                                    </form>
                                </div>
                            <?php } ?>
                            <?php if(isset($_POST['editprofile'])){ ?>
                                <form action="profile.php" method="post" enctype="multipart/form-data">
    <div style="border:3px solid gray;padding:10px">
        <div style="border:1px solid white;">
            <?php if(!empty($pImage)): ?>
                <div class="mb-3" style="display:flex;justify-content:center;">
                    <img src="<?php echo $pImage; ?>" id="image" alt="Current Image" width="200" height="200">
                </div>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="pImage" class="form-label">Admin Image</label>
            <input type="file" class="form-control" id="pImage" accept="image/jpeg,image/jpg,image/png" name="pImage" onchange="previewImage(event)">
        </div>
        <div>
            <div>
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" value="<?php echo $Name;?>" required placeholder="Product Name" name="Name">
                    <label for="floatingPassword">Admin Name</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" required placeholder="Enter Old Password" name="Opassword">
                    <label for="floatingInput">Old Password</label>
                </div>     
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" required placeholder="Enter New Password" name="Npassword">
                    <label for="floatingInput">New Password</label>
                </div>                                          
            </div>
        </div>
    </div>
    <div class="mb-3" style="margin-top:10px;">
        <button type="submit" class="btn btn-primary" name="update">Update</button>
        <button type="button" class="btn btn-primary" onclick="back()">Back</button>
    </div>
</form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!--Main Script-->
    <?php require "js/Main_js.php"?>
    <script>
function previewImage(event) {
    var posterPreview = document.getElementById('image');
    var file = event.target.files[0];
    var reader = new FileReader();
    reader.onload = function() {
        posterPreview.src = reader.result;
        posterPreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
}
</script>
</body>
</html>
