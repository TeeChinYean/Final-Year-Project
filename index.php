<?php require 'dataconnection.php';?>
<?php
    $sidebar = ($conn->query("SELECT * FROM admin WHERE Admin_ID = '$_SESSION[adminid]'"));
    $sidebar = mysqli_fetch_array($sidebar);
    if($sidebar == 0){
        header("Location: signin.php");
        exit();
    }
    else{
    $profile = "SELECT * FROM admin WHERE Admin_ID = '$_SESSION[adminid]'";
    $profile = mysqli_query($conn, $profile);
    $profile = mysqli_fetch_array($profile);
    $image = $profile['Admin_Profile_Image'];
    }

?>

<!DOCTYPE html>
<html lang="en">
    <?php include 'head.html' ?>
<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start (loading icon)-->
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

            
                <!-- Sale & Revenue Start -->
                <div class="container-fluid pt-4 px-4">
                    <!--complete the code-->
                    <div class="row g-4">
                        
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                                <i class="fa fa-chart-line fa-3x text-primary"></i>
                                <div class="ms-3">
                                    <p class="mb-2">Today Sale</p>
                                    <?php
                                        $sql = "SELECT SUM(Total_Amount) AS TotalSalesPrice 
                                        FROM `bill_master` 
                                        WHERE DATE(Invoice_Date) = CURDATE();";
                                

                                        $result = mysqli_query($conn, $sql);

                                        if ($result) {
                                            $row = mysqli_fetch_assoc($result);
                                            $total = $row['TotalSalesPrice']; // Correct alias here
                                            if($total == null)
                                            {
                                                $total = 0;
                                            }
                                            echo "<h6 class='mb-0'>$$total</h6>";
                                        } else {
                                            echo "Error executing the query: " . mysqli_error($conn);
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                                <i class="fa fa-chart-bar fa-3x text-primary"></i>
                                <div class="ms-3">
                                    <p class="mb-2">Total Sale</p>
                                    <?php
                                    
                                        
                                    $sql = "SELECT SUM(Total_Amount) AS TotalSalesPrice FROM `bill_master`";


                                    $result = mysqli_query($conn, $sql);

                                    if ($result) {
                                        $row = mysqli_fetch_assoc($result);
                                        $total = $row['TotalSalesPrice']; // Correct alias here
                                        if($total == null)
                                        {
                                            $total = 0;
                                        }
                                        echo "<h6 class='mb-0'>$$total</h6>";
                                    } else {
                                        echo "Error executing the query: " . mysqli_error($conn);
                                    }
                                
                                ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                                <i class="fa fa-chart-area fa-3x text-primary"></i>
                                <div class="ms-3">
                                    <p class="mb-2">Today Revenue</p>
                                    <?php
                                        $sql = "SELECT SUM((b.Total_Amount) - (r.Quantity * p.Product_Cost)) AS TotalSalesPrice 
                                        FROM `bill_master` b 
                                        JOIN bill_master_transaction r On b.Invoice_ID = r.Invoice_ID
                                        JOIN product p ON r.Product_ID = p.Product_ID 
                                        WHERE DATE(b.Invoice_Date) = CURDATE()";
                                

                                        $result = mysqli_query($conn, $sql);

                                        if ($result) {
                                            $row = mysqli_fetch_assoc($result);
                                            $total = $row['TotalSalesPrice']; // Correct alias here
                                            if($total == null)
                                            {
                                                $total = 0;
                                            }
                                            echo "<h6 class='mb-0'>$$total</h6>";
                                        } else {
                                            echo "Error executing the query: " . mysqli_error($conn);
                                        }
                                    ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-xl-3">
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                                <i class="fa fa-chart-pie fa-3x text-primary"></i>
                                <div class="ms-3">
                                    <p class="mb-2">Total Revenue</p>
                                    <?php
                                        $sql = "SELECT SUM((b.Total_Amount) - (r.Quantity * p.Product_Cost)) AS TotalSalesPrice 
                                        FROM `bill_master` b 
                                        JOIN bill_master_transaction r on b.Invoice_ID = r.Invoice_ID
                                        JOIN product p On r.Product_ID = p.Product_ID";
                                

                                        $result = mysqli_query($conn, $sql);

                                        if ($result) {
                                            $row = mysqli_fetch_assoc($result);
                                            $total = $row['TotalSalesPrice']; // Correct alias here
                                            if($total == null)
                                            {
                                                $total = 0;
                                            }
                                            echo "<h6 class='mb-0'>$$total</h6>";
                                        } else {
                                            echo "Error executing the query: " . mysqli_error($conn);
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Sale & Revenue End -->


                <!-- Sales Chart Start -->
                <div class="container-fluid pt-4 px-4">
                    <div class="row g-4">
                        <div class="col-sm-12 col-xl-6">
                                <div class="bg-secondary rounded h-100 p-4" >
                                        <h4 class="mb-4">Worldwide Sales</h4>
                                    <canvas id="Sales" style="background-color:white;"></canvas>
                                </div>
                        </div>
                        <div class="col-sm-12 col-xl-6">
                                <div class="bg-secondary rounded h-100 p-4">
                                    <h4 class="mb-4">Sales & Revenue</h4>      
                                    <canvas id="myChart" style="background-color:white;"></canvas>                                                                
                                </div>
                        </div>
                    </div>
                </div>
                <!-- Sales Chart End -->


                <!-- Recent Sales Start -->
                <div class="container-fluid pt-4 px-4">
                    <div class="bg-secondary text-center rounded p-4">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h4 class="mb-0">Recent Sales</h4>
                            <a href="recent_sales.php">Show All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table text-start align-middle table-bordered table-hover mb-0">
                            <thead>
                                    <tr class="text-white">
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    
                                    $sql = "
                                    SELECT b.Invoice_ID, b.Customer_ID, b.Total_Amount, b.Invoice_Date, b.Invoice_Status, r.Product_ID, r.Quantity, p.Product_Name, c.Customer_Username
                                    FROM `bill_master` b
                                    JOIN bill_master_transaction r ON b.Invoice_ID = r.Invoice_ID
                                    JOIN product p ON r.Product_ID = p.Product_ID
                                    JOIN customer c ON b.Customer_ID = c.Customer_ID
                                    ORDER BY b.Invoice_Date DESC
                                    LIMIT 5";

                                    
                                    $result = mysqli_query($conn, $sql);
                                    
                                    if ($result) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $row['Invoice_ID'] . "</td>";
                                            echo "<td>" . $row['Customer_Username'] . "</td>";
                                            echo "<td>" . $row['Quantity'] . "</td>";
                                            echo "<td>" . $row['Total_Amount'] . "</td>";
                                            echo "<td>" . $row['Invoice_Status'] . "</td>";
                                            echo "<td>" . $row['Invoice_Date']. "</td>";
                                            echo "<td><a href='sales.php?id=" . $row['Invoice_ID'] . "' class='btn btn-primary'>View</a></td>";
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
                <!-- Recent Sales End -->
           

        </div>
        <!-- Content End -->


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
