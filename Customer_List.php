<?php require 'dataconnection.php' ?>
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


            <!-- recent sales Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="vh-100 bg-secondary rounded mx-0" style="padding:10px 10px 10px 10px;overflow-y:auto;scrollbar-color:white gray; scrollbar-width:thin;">
                <div style="display:flex;justify-content:space-between">
                        <h3>Customer List</h3>
                        <form action="export_orders_to_excel.php?id=2"method="post">
                            <button type="submit" class="btn btn-success">Export to Excel</button>
                        </form>
                    </div>
                        <div class="table-responsive">
                        <table class="table text-start align-middle table-bordered table-hover mb-0">
                        <thead>
                                <tr class="text-white">
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
                                
                                $sql = "SELECT * FROM customer ORDER BY Customer_ID DESC";
                                $result = mysqli_query($conn, $sql);
                                
                                if ($result) {
                                    $num = 0;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $formattedOrderDate = (new DateTime($row['Customer_Register_Time']))->format('Y-m-d');
                                        echo "<tr>";
                                        echo "<th scope='row'>" . ($num + 1) . "</th>";
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
                        <hr>
                        <button type="button" class="btn btn-primary" onclick="back()">Back</button>
                    </div>
                </div>
            </div>
            <!-- recent sales End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

     <!--Main Script-->
     <?php require "js/Main_js.php"?>
</body>

</html>