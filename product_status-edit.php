<?php 
require 'dataconnection.php';

if(isset($_POST['cancel'])) {
    header("Location: product_status.php");
    exit();
}
if(isset($_POST['edit2']))
{
    foreach ($_POST['Status'] as $key => $value) {
        $update = mysqli_prepare($conn, "UPDATE `product` SET Product_Status = ? WHERE Product_ID = ?");
        
        if (!$update) {
            die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($update, "ss", $value, $key);
        mysqli_stmt_execute($update);
        
        if(mysqli_stmt_affected_rows($update) > 0) {
            echo "<script>alert('Product Updated Successfully')</script>";
        } else {
            echo "<script>alert('Product Update Failed')</script>";
        }
    }
    header("Location: product_status.php");
    exit();

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.html' ?>
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

        <!-- Sidebar Start -->
        <?php include 'sidebar.php' ?>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <?php include 'navbar.php' ?>
            <!-- Navbar End -->

            
            <div class="container-fluid pt-4 px-4">
                <div class="vh-100 bg-secondary rounded mx-0" style="padding:10px 10px 10px 10px;overflow-y:auto;scrollbar-color:white gray; scrollbar-width:thin;">
                    <div class="flex-container">
                            <h3>Product Status Change</h3>
                    </div>
                    
                    <hr>
                    <div class="table-responsive">
                        <form action='product_status-edit.php' method='post'>
                            <table class="table text-start align-middle table-bordered table-hover mb-0">
                                <thead>
                                    <tr class="text-white">
                                        <th scope="col">#</th>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Cost</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql ="SELECT p.Product_ID, p.Product_Name, c.Category, p.Product_Description, p.Product_quantity_available, p.Product_Price, p.Product_Cost, p.Product_Status 
                                    FROM product p
                                    JOIN record_time r ON p.Product_ID = r.Product_ID
                                    JOIN product_category c ON p.PC_ID = c.PC_ID
                                    ORDER BY p.Product_ID DESC";

                                    $result = mysqli_query($conn, $sql);
                                    if(!$result) {
                                        die("Error executing the query: " . mysqli_error($conn));
                                    }
                                    if(mysqli_num_rows($result) > 0){
                                        $num = 0;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $num++; 
                                            $selectedStatus = isset($_POST['Status'][$row['Product_ID']]) ? $_POST['Status'][$row['Product_ID']] : $row['Product_Status'];
                                            echo "<tr>";        
                                            echo "<td>" . ($num) . "</td>";
                                            echo "<td>{$row['Product_ID']}</td>";
                                            echo "<td>{$row['Product_Name']}</td>";
                                            echo "<td>{$row['Category']}</td>";
                                            echo "<td>{$row['Product_Description']}</td>";
                                            echo "<td>{$row['Product_quantity_available']}</td>";
                                            echo "<td>{$row['Product_Price']}</td>";
                                            echo "<td>{$row['Product_Cost']}</td>";
                                            echo "<td>
                                                <div class='form-floating mb-3'>
                                                    <select class='form-select' id='floatingSelect' name='Status[{$row['Product_ID']}]' aria-label='Floating label select example'>
                                                        <option value='1' " . ($selectedStatus == '1' ? 'selected' : '') . ">Active</option>
                                                        <option value='0' " . ($selectedStatus == '0' ? 'selected' : '') . ">Inactive</option>
                                                    </select>
                                                </div>
                                            </td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='9'>No products found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <hr>
                            <button type="button" class="btn btn-primary" onclick="back()">Back</button>
                            <button type='submit' name='edit2' class='btn btn-primary'>Edit</button>
                        </form>
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
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
    </script>
</body>
</html>
