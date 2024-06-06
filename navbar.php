<?php
    $navbar = ($conn->query("SELECT * FROM admin WHERE Admin_ID = '$_SESSION[adminid]'"));
    $navbar = mysqli_fetch_array($navbar);
    if($navbar == 0){
        header("Location: signin.php");
        exit();
    }
    else{
    $query = "SELECT * FROM admin WHERE Admin_ID = '$_SESSION[adminid]'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($result);
    $image = $row['Admin_Profile_Image'];
    }
?>
<!DOCTYPE html>
<html>
    <body>
    <nav class="navbar navbar-expand bg-secondary navbar-dark sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-user-edit"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <div class="navbar-nav align-items-center ms-auto">
                   
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="<?php echo $image?>" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex"><?php echo $_SESSION['username']?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-secondary border-0 rounded-0 rounded-bottom m-0">
                            <a href="profile.php" class="dropdown-item">My Profile</a>
                            <a href="#" onclick="confirmLogout()" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
                <script>
                    function confirmLogout() {
                        var confirmLogout = confirm("Are you sure you want to log out?");
                        if (confirmLogout) {
                            window.location.href = "logout.php";
                        }
                    }
                </script>
            </nav>
            
    </body>
</html>