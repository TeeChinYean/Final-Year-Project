<?php 
require 'dataconnection.php';

if(isset($_POST["add"]))
{
    // Retrieve form data
    $pName = isset($_POST["Product_Name"]) ? trim($_POST["Product_Name"]) : '';
    $pCategory = isset($_POST["Product_Category"]) ? trim($_POST["Product_Category"]) : '';
    $pDescription = isset($_POST["Product_Description"]) ? trim($_POST["Product_Description"]) : '';
    $pQuantity = isset($_POST["Product_Quantity"]) ? trim($_POST["Product_Quantity"]) : '';
    $pPrice = isset($_POST["Product_Price"]) ? trim($_POST["Product_Price"]) : '';
    $pCost = isset($_POST["Product_Cost"]) ? trim($_POST["Product_Cost"]) : '';
    $pSize = isset($_POST["Product_size"]) ? trim($_POST["Product_size"]) : '';

    if (isset($_FILES["pImage"]) && $_FILES["pImage"]["error"] == UPLOAD_ERR_OK) {
        $filename = $_FILES["pImage"]["name"];
        $tempname = $_FILES["pImage"]["tmp_name"];
        $folder = $filename;
    }
    // Check for existing product
    $checkExisting = mysqli_prepare($conn, "SELECT * FROM product WHERE Product_Name = ?");
    if (!$checkExisting) {
        die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($checkExisting, "s", $pName);
    mysqli_stmt_execute($checkExisting);
    $resultExisting = mysqli_stmt_get_result($checkExisting);

    if($resultExisting->num_rows > 0){
        echo "<script>alert('Product Name already exists')</script>";
    }
    else{
        // Insert product into database
        $add = mysqli_prepare($conn, "INSERT INTO product ( Size_ID , Product_Name,Product_Description, Product_quantity_available, Product_Price, Product_Cost, PC_ID, Product_Image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        if (!$add) {
            die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($add, "ssssssss", $pSize,$pName, $pDescription, $pQuantity,$pPrice, $pCost,$pCategory, $filename);


        mysqli_stmt_execute($add);

        if (mysqli_stmt_affected_rows($add) > 0) {
            echo "<script>alert('Product add Success')</script>";
        } else {
            echo "<script>alert('Error during add. MySQL Error: " . mysqli_error($conn) . "')</script>";
        }
    }

    // Insert record time
    $record_time_query = mysqli_prepare($conn, "INSERT INTO record_time (Product_ID, record_time) VALUES (?, NOW())");
    mysqli_stmt_bind_param($record_time_query, "s", $pID);
    mysqli_stmt_execute($record_time_query);

    // Handle file upload
    
}
?>

<style>
    label{
        color:white;
    }
</style>
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


            <!-- Add Product -->
                        
                        <div class="bg-secondary rounded h-100 p-4" style="margin-left:17px; margin-top:15px;margin-right:17px">
                            <h3 class="mb-4">New Product Details</h3>
                            <form action="add_product.php" method="post" enctype="multipart/form-data">
                                <div class="row mb-3">
                                    <label for="inputEmail3" class="col-sm-2 col-form">Product Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Product Name" name="Product_Name" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputPassword3" class="col-sm-2 col-form-label">Product Category</label>
                                    <div class="col-sm-10">
                                        <?php
                                            $sql = "SELECT * FROM product_category";
                                            $result = mysqli_query($conn, $sql);

                                            echo "<select class='form-select' name='Product_Category' required>";
                                            echo "<option selected disabled>Select Category</option>";
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                echo "<option value='" . $row['PC_ID'] . "'>" . $row['Category'] . "</option>";
                                            }
                                            echo "</select>";

                                        ?>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputPassword3" class="col-sm-2 col-form-label">Product Description</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Product Description" name="Product_Description" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputPassword3" class="col-sm-2 col-form-label">Product Quantity Available</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Product Quantity Available" name="Product_Quantity" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputPassword3" class="col-sm-2 col-form-label">Product Price</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Product Price" name="Product_Price" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputPassword3" class="col-sm-2 col-form-label">Product Cost</label>
                                      <div class="col-sm-10">
                                        <input type="text" class="form-control" placeholder="Product Cost" name="Product_Cost" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="inputPassword3" class="col-sm-2 col-form-label">Product Size</label>
                                    <div class="col-sm-10">
                                    <?php
                                            $sql = "SELECT * FROM size";
                                            $result = mysqli_query($conn, $sql);

                                            echo "<select class='form-select' name='Product_size' required>";
                                            echo "<option selected disabled>Select Size</option>";
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                if ($row['gender'] == 0)
                                                    $gender = "Male";
                                                else if ($row['gender'] == 1)
                                                    $gender = "Female";
                                                else if ($row['gender'] == 2)
                                                    $gender = "Kids";
                                                echo "<option value='" . $row['Size_ID'] . "'>" . $gender . " (Size " . $row["Size_ID"] . ")" . "</option>";
                                            }
                                            echo "</select>";
                                            

                                        ?>
                                        
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="pImage" class="col-sm-2 col-form-label">Product Image</label>
                                    <div class="col-sm-10" >
                                        <input type="file" class="form-control" accept=".jpeg,.jpg,.png" name="pImage">
                                    </div>
                                    
                                </div>
                                <button type="submit" class="btn btn-primary" name="add">Add</button>
                                <button type="button" class="btn btn-primary" onclick="back()">Back</button>
                            </form>
                        <hr>
                    <div style="height: 370px;overflow-y:auto;scrollbar-color:white gray; scrollbar-width:thin;">
                    <h3>Lastest Product</h3>
                            <?php include 'Product_List.php';?>
                        </div>
            <!-- Add Product -->

        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!--Main Script-->
    <?php require "js/Main_js.php"?>
    <!--clean resubmit-->
    <script>
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
    </script>
</body>

</html>