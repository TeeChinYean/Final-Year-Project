<?php 
    require 'dataconnection.php';

if (isset($_GET['id'])) {
    $Orderid = $_GET['id'];
    $_SESSION['Oid'] = $Orderid;

    $checkExisting = mysqli_prepare($conn, "
        SELECT b.Invoice_ID, p.Product_Name, b.Invoice_Status, r.Quantity, b.Total_Amount, c.Customer_Username, b.Invoice_Date
        From `bill_master` b
        JOIN bill_master_transaction r ON b.Invoice_ID = r.Invoice_ID
        JOIN product p ON r.Product_ID = p.Product_ID
        JOIN customer c ON b.Customer_ID = c.Customer_ID
        WHERE b.Invoice_ID = ?
    ");

    if (!$checkExisting) {
        die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($checkExisting, "i", $Orderid);
    
    // Execute the prepared statement
    mysqli_stmt_execute($checkExisting);
    
    // Get the result
    $resultExisting = mysqli_stmt_get_result($checkExisting);

    // Check if there are any rows
    if ($resultExisting->num_rows > 0) {
        $row = mysqli_fetch_assoc($resultExisting);
        $Orderid = $row['Invoice_ID'];
        $Productname = $row['Product_Name'];
        $OrderStatus = $row['Invoice_Status'];
        $Quantity = $row['Quantity'];
        $date = $row['Invoice_Date'];
        $Total = $row['Total_Amount'];
        $CustomerName = $row['Customer_Username'];
        $OrderDate = $row['Invoice_Date'];
        
    } else {
        echo "<script>alert('Order ID does not exist')</script>";
        echo "<script>alert('$_GET[id]')</script>";
        echo "<script>window.location = 'index.php'</script>";
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


                <!-- Sales Report -->
                <div >
                    <div class="bg-secondary rounded h-100 p-4" style="margin-left:17px; margin-top:15px; margin-right:17px">
                        <div>
                            <h5 class="mb-4">Sales Report <?php echo $_SESSION['Oid']?></h5>                    
                        </div>
                        <div>
                            <?php if(isset($checkExisting)){ ?>
                                <div class="form-floating mb-3">  
                                    <a class="form-control" style="background-color:black;color:white"><?php echo $Orderid; ?></a>
                                    <label for="floatingOrderID" style="color:white">Order ID</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <a class="form-control" style="background-color:black;color:white"><?php echo $Productname; ?></a>
                                    <label for="floatingProductName" style="color:white">Product Name</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <a class="form-control" style="background-color:black;color:white"><?php echo $OrderStatus; ?></a>
                                    <label for="floatingOrderStatus" style="color:white">Order Status</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <a class="form-control" style="background-color:black;color:white"><?php echo $Quantity; ?></a>
                                    <label for="floatingQuantity" style="color:white">Quantity</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <a class="form-control" style="background-color:black;color:white"><?php echo $Total; ?></a>
                                    <label for="floatingTotalPrice" style="color:white">Total Price</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <a class="form-control" style="background-color:black;color:white"><?php echo $CustomerName; ?></a>
                                    <label for="floatingCustomerName" style="color:white">Customer Username</label>
                                </div>
                                <div class="form-floating mb-3">
                                    <a class="form-control" style="background-color:black;color:white"><?php echo $date; ?></a>
                                    <label for="floatingOrderDate" style="color:white">Order Date</label>
                                </div>
                            <?php } ?>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="back()">Back</button>
                    </div>
                </div>
            <!-- Sales Report -->    
        </div>
        <!-- Content End -->

        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!--Main Script-->
    <?php require "js/Main_js.php"?>
    <!--clean resubmit-->

</body>

</html>
