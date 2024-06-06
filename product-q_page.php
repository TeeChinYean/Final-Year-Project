<?php 
require 'dataconnection.php';
$pImage = ""; // Initialize $pImage variable

// Code for retrieving existing product details and image
if(isset($_GET['id'])) {
    $productID = $_GET['id'];
    $_SESSION['pid'] = $productID;
    $checkExisting = mysqli_prepare($conn, "SELECT * FROM product p JOIN size s ON p.Size_ID = s.Size_ID WHERE Product_ID = ?");
    if (!$checkExisting) {
        die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($checkExisting, "i", $productID);
    // Execute the prepared statement
    mysqli_stmt_execute($checkExisting);
    // Get the result
    $resultExisting = mysqli_stmt_get_result($checkExisting);
    // Check if there are any rows
    if ($resultExisting->num_rows > 0) {
        $row = mysqli_fetch_assoc($resultExisting);
        $pName = $row["Product_Name"];
        $pImage = $row["Product_Image"];
        $pQuantity = $row["Product_quantity_available"];
        $_SESSION['pquantity'] = $pQuantity;
        $_SESSION['product_img'] = $pImage;
    } else {
        echo "<script>alert('Product ID does not exist')</script>";
        header("Location: product-q.php");
        exit();
    }
}

// Code for updating product details
// Code for updating product details
if (isset($_POST["update"])) {
    // Retrieve product details
    $pID = $_SESSION['pid'];
    $pDescription = isset($_POST["pDescription"]) ? trim($_POST["pDescription"]) : '';
    $newQuantity = isset($_POST["pQuantity"]) ? trim($_POST["pQuantity"]) : '';

    // Retrieve the previous quantity from the session
    $previousQuantity = $_SESSION['pquantity'];

    // Calculate quantity change
    $quantityChange = $newQuantity - $previousQuantity;
    $changeType = $quantityChange > 0 ? '+' : '-';

    // Update the product quantity
    $update = mysqli_prepare($conn, "UPDATE product SET Product_quantity_available = ? WHERE Product_ID = ?");
    if (!$update) {
        die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
    }
    mysqli_stmt_bind_param($update, "ii", $newQuantity, $pID);
    if (mysqli_stmt_execute($update)) {
        if (mysqli_stmt_affected_rows($update) > 0) {
            // Insert into product_quantity_change table
            $insertProductQ = mysqli_prepare($conn, "INSERT INTO product_quantity_change (Product_ID, Description, Quantity_Change, Change_Type) VALUES (?, ?, ?, ?)");
            if (!$insertProductQ) {
                die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($insertProductQ, "isis", $pID, $pDescription, $quantityChange, $changeType);
            if (mysqli_stmt_execute($insertProductQ)) {
                echo "<script>alert('Product Updated and Logged Successfully')</script>";
                header("Location: product-q.php");
                exit();
            } else {
                echo "<script>alert('Error executing insert into product_quantity_change: " . mysqli_error($conn) . "')</script>";
            }
        } else {
            echo "<script>alert('No rows were affected. Product Update Failed')</script>";
            header("Location: product-q.php");
            exit();
        }
    } else {
        echo "<script>alert('Error executing update query: " . mysqli_error($conn) . "')</script>";
        header("Location: product-q.php");
        exit();
    }
}

?>

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

            <!-- Edit Product -->
            <div>
                <div class="bg-secondary rounded h-100 p-4" style="margin-left:17px; margin-top:15px; margin-right:17px">
                    <div>
                        <h6 class="mb-4">Edit Product Details</h6>                    
                    </div>
                    <div>
                        <?php if(isset($checkExisting)){ ?>
                        <form action="product-q_page.php" method="post">
                            <div>
                                <h6 class="mb-4">Product <?php echo "$pName"?></h6>
                                <!-- Hidden input field to store Product ID -->
                                <div class="form-floating mb-3">  
                                    <input type="text" class="form-control" required placeholder="Quantity Description" name="pDescription">
                                    <label for="floatingPassword">Description</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" value="<?php echo $pQuantity?>" required placeholder="Product Quantity" name="pQuantity">
                                    <label for="floatingInput">Product Quantity</label>
                                </div>        
                                <img src="<?php echo $pImage?>" alt="Current Image" width="200" height="200">
                            </div>
                            <button type="submit" class="btn btn-primary" name="update">Update</button>                                
                        </form>
                        <?php }?>
                    </div>
                </div>
            </div>
            <!-- Edit Product -->    
        </div>
        <!-- Content End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- Main Script -->
    <?php require "js/Main_js.php"?>
</body>
</html>
