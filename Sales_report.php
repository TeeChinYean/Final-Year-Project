<?php 
require 'dataconnection.php';
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


            <!-- Table Start -->
            <div class="container-fluid pt-4 px-4">
                     <div class="row g-4">
                        <div class="col-sm-12 col-xl-6">
                                <div class="bg-secondary rounded h-100 p-4" >
                                        <h4 class="mb-4">Sales</h4>
                                    <canvas id="Sales" style="background-color:white;"></canvas>
                                </div>
                        </div>
                   
                        <div class="col-sm-12 col-xl-6">
                                <div class="bg-secondary rounded h-100 p-4">
                                    <h4 class="mb-4">Sales & Revenue</h4>      
                                    <canvas id="myChart" style="background-color:white;"></canvas>                                                                
                                </div>
                        </div>
                    
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                        <div style="display:flex;justify-content:space-between;">
                            <h5 class="mb-4">Recent Sales</h5>
                            <a href="recent_sales.php">Show All</a>
                        </div>
                        <div>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                               <?php
                                
                                $sql = "
                                SELECT b.Invoice_ID, c.Customer_Username, r.Quantity, b.Total_Amount, b.Invoice_Date
                                FROM bill_master b
                                JOIN bill_master_transaction r ON b.Invoice_ID = r.Invoice_ID
                                JOIN product p ON r.Product_ID = p.Product_ID
                                JOIN customer c ON b.Customer_ID = c.Customer_ID
                                ORDER BY b.Invoice_Date DESC
                                LIMIT 5";
                                
                                $result = mysqli_query($conn, $sql);
                                
                                if ($result) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        
                                        echo "<tr>";
                                        echo "<td>{$row['Invoice_ID']}</td>";
                                        echo "<td>{$row['Customer_Username']}</td>";
                                        echo "<td>{$row['Quantity']}</td>";
                                        echo "<td>{$row['Total_Amount']}</td>";
                                        echo "<td>{$row['Invoice_Date']}</td>";
                                        echo "<form action='Sales_report.php' method='post'>"; 
                                        echo "<td><button type='button'onclick='salesreport({$row['Invoice_ID']})'  class='btn btn-primary'>View</button></td>";
                                        echo "</form>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "Error executing the query: " . mysqli_error($conn);
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                            <div style="display:flex;justify-content:space-between;">
                                <h5 class="mb-4">Product quantity Lower than 10</h5>
                            </div>
                            <div  style="height:180px;overflow-y:auto;scrollbar-color:white gray; scrollbar-width:thin;">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Product ID</th>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Category</th>
                                            <th scope="col">Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    
                                    $sql = "SELECT * FROM product p JOIN product_category c ON p.PC_ID = c.PC_ID WHERE Product_quantity_available <= 10 ORDER BY Product_ID";
                                    $result = mysqli_query($conn, $sql);
                                    
                                    if ($result) {
                                        $num = 0;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $num++;
                                            echo "<tr>";
                                            echo "<td>" . ($num) . "</td>";
                                            echo "<td>{$row['Product_ID']}</td>";
                                            echo "<td>{$row['Product_Name']}</td>";
                                            echo "<td>{$row['Category']}</td>";
                                            echo "<td>{$row['Product_quantity_available']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "Error executing the query: " . mysqli_error($conn);
                                    }
                                    
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                        <div style="display:flex;justify-content:space-between;">
                            <h5 class="mb-4">Order status</h5>
                            <a href="order.php">Show All</a>
                        </div>
                        <div>
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Order ID</th>
                                        <th scope="col">Product Name</th>
                                        <th scope="col">Order Status</th>
                                        <th scope="col">Order Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php

                                        $sql = "SELECT b.Invoice_ID, p.Product_Name, b.Invoice_Status, b.Invoice_Date FROM bill_master b JOIN bill_master_transaction r ON b.Invoice_ID = r.Invoice_ID JOIN product p ON r.Product_ID = p.Product_ID ORDER BY b.Invoice_Date DESC LIMIT 5";

                                        $result = mysqli_query($conn, $sql);

                                        if ($result) {
                                            $num = 0;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $num++;
                                                echo "<tr>";
                                                echo "<td>" . ($num) . "</td>";
                                                echo "<td>{$row['Invoice_ID']}</td>";
                                                echo "<td>{$row['Product_Name']}</td>";
                                                echo "<td>{$row['Invoice_Status']}</td>";
                                                echo "<td>{$row['Invoice_Date']}</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "Error executing the query: " . mysqli_error($conn);
                                        }

                                  ?>
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary rounded h-100 p-4">
                            <div style="display:flex;justify-content:space-between;">
                            <h5 class="mb-4">Newtest Products</h5>
                            <a href="product_list-page.php">Show All</a>
                            </div>
                            <div>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                        <th scope="col">#</th>
                                            <th scope="col">Product ID</th>
                                            <th scope="col">Product Name</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    
                                    $sql = "SELECT * FROM product p JOIN record_time r ON p.Product_ID = r.Product_ID ORDER BY r.record_time DESC LIMIT 5";
                                    $result = mysqli_query($conn, $sql);

                                    if ($result) {
                                        $num = 0;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $num++;
                                            echo "<tr>";
                                            echo "<td>" . ($num) . "</td>";
                                            echo "<td>{$row['Product_ID']}</td>";
                                            echo "<td>{$row['Product_Name']}</td>";
                                            echo "<td>{$row['Product_quantity_available']}</td>";
                                            echo "<td>{$row['record_time']}</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "Error executing the query: " . mysqli_error($conn);
                                    }
                                    
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--template-->
                    <div class="col-12">
                        <div class="bg-secondary rounded h-100 p-4">
                        <div style="display:flex;justify-content:space-between;">
                            <h5 class="mb-4">Customer Account List</h5>
                            <a href="Customer_List.php">Show All</a>
                        </div>
                            <div class="table-responsive" style="overflow-x:auto;scrollbar-color:white gray; scrollbar-width:thin;">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">ID</th>
                                            <th scope="col">Username</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Phone</th>
                                            <th scope="col">Register Time</th>
                                            <th scope="col">Address 1</th>
                                            <th scope="col">Address 2</th>
                                            <th scope="col">Address 3</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                
                                        $sql = "SELECT * FROM customer ORDER BY Customer_ID DESC LIMIT 5";
                                        $result = mysqli_query($conn, $sql);
                                        
                                        if ($result) {
                                            $num = 0;
                                            while ($row = mysqli_fetch_assoc($result)) {
                                                $formattedOrderDate = (new DateTime($row['Customer_Register_Time']))->format('Y-m-d');
                                                $num++;
                                                echo "<tr>";
                                                echo "<th scope='row'>" . ($num) . "</th>";
                                                echo "<td>{$row['Customer_ID']}</td>";
                                                echo "<td>{$row['Customer_Username']}</td>";
                                                echo "<td>{$row['Customer_Email']}</td>";
                                                echo "<td>{$row['Customer_Phone_Number']}</td>";
                                                echo "<td>{$formattedOrderDate}</td>";
                                                echo "<td>{$row['Customer_Delivery_Address_1']}</td>";
                                                echo "<td>{$row['Customer_Delivery_Address_2']}</td>";
                                                echo "<td>{$row['Customer_Delivery_Address_3']}</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "Error executing the query: " . mysqli_error($conn);
                                        }
                                        
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                        
                </div>
            </div>
            <!-- Table End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>
    
    <!--Main Script-->
    <?php require "js/Main_js.php"?>
</body>

</html>