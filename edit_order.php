<?php 
    require 'dataconnection.php';

   
    if(isset($_POST["edit2"])) {
        // Loop through the posted statuses and update each order accordingly
        foreach($_POST["Status"] as $orderID => $status) {
            $update = mysqli_prepare($conn, "UPDATE bill_master SET Invoice_Status = ? WHERE Invoice_ID = ?");
            if (!$update) {
                die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($update, "ss", $status, $orderID);
            mysqli_stmt_execute($update);
            
            if(mysqli_stmt_affected_rows($update) > 0) {
                echo "<script>alert('Order $orderID Updated Successfully')</script>";
            } else {
                echo "<script>alert('Order $orderID Update Failed')</script>";
            }
        }

        header("Location: order.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.html' ?>
    <style>
        .flex-container {
            display: flex;
        }
        .flex-container > div {
            margin-right: 10px;
            margin-left: 10px;
            padding-right: 50px;
        }
    </style>
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
                        <div>
                            <h3>Order (Edit)</h3>
                        </div>
                    </div>
                    <hr>

                    <!-- Table for displaying all orders -->
<div class="table-responsive">
    <table class="table text-start align-middle table-bordered table-hover mb-0">
        <!-- Table header -->
        <thead>
            <tr class="text-white">
                <th scope="col">#</th>
                <th scope="col">Order ID</th>
                <th scope="col">Product Name</th>
                <th scope="col">Customer</th>
                <th scope="col">Date</th>
                <th scope="col">Quantity</th>
                <th scope="col">Total Price</th>
                <th scope="col">Address</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <!-- Table body -->
        <tbody>
            <?php
                // Fetch all orders
                $query = "SELECT 
                b.Invoice_ID, p.Product_Name, c.Customer_Username, b.Invoice_Date, 
                r.Quantity, b.Total_Amount, b.Delivery_Address, b.Invoice_Status
            FROM `bill_master` b
            JOIN bill_master_transaction r ON b.Invoice_ID = r.Invoice_ID 
            JOIN product p ON r.Product_ID = p.Product_ID 
            JOIN customer c ON b.Customer_ID = c.Customer_ID
            WHERE o.Invoice_Status != 'Delivered'
            ORDER BY o.Invoice_ID DESC";
                
                $result = mysqli_query($conn, $sql);
                
                if ($result) {
                    $num = 0;
                    // Display each order as a table row
                    echo "<form action='edit_order.php' method='post'>";
                    while ($row = mysqli_fetch_assoc($result)) {
                        $num++;
                        // Store the current order's status in the selected statuses array
                        $selectedStatus = isset($_POST["Status"][$row['Order_ID']]) ? $_POST["Status"][$row['Order_ID']] : $row['Order_Status'];
                        // Display each order as a table row
                        echo "<tr>";        
                        echo "<td>" . ($num) . "</td>";           
                        echo "<td>{$row['Invoice_ID']}</td>";
                        echo "<td>{$row['Product_Name']}</td>";
                        echo "<td>{$row['Customer_Username']}</td>";
                        echo "<td>{$row['Invoice_Date']}</td>";
                        echo "<td>{$row['Quantity']}</td>";
                        echo "<td>{$row['Total_Amount']}</td>";
                        echo "<td>{$row['Delivery_Address']}</td>";
                        echo "<td>
                                <div class='form-floating mb-3'>
                                    <select class='form-select' id='floatingSelect' name='Status[{$row['Order_ID']}]' aria-label='Floating label select example'>
                                        <option value='Ordered' " . ($selectedStatus == 'Ordered' ? 'selected' : '') . ">Ordered</option>
                                        <option value='Preparing' " . ($selectedStatus == 'Preparing' ? 'selected' : '') . ">Preparing</option>
                                        <option value='In transit' " . ($selectedStatus == 'In transit' ? 'selected' : '') . ">In transit</option>
                                        <option value='Delivered' " . ($selectedStatus == 'Delivered' ? 'selected' : '') . ">Delivered</option>
                                    </select>
                                </div>  
                            </td>";
                        echo "</tr>";
                    }
                    // Add the "Edit" button for bulk editing
                    echo "<button type='submit' name='edit2' class='btn btn-primary'>Edit</button>";
                    echo "</form>";
                } else {
                    echo "Error executing the query: " . mysqli_error($conn);
                }
            ?>
        </tbody>
    </table>
    <hr>
    <button type="button" class="btn btn-primary" onclick="back()">Back</button>
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
        // Prevent form resubmission on page refresh
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
</body>
</html>