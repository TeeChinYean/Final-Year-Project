<?php 
    require 'dataconnection.php';
// Assuming $conn is your database connection object

if(isset($_POST['psearch']))
{
    $pname = $_POST['name'];
    $sql = "SELECT *, p.Product_ID AS Product_ID FROM product p 
    JOIN record_time r ON p.Product_ID = r.Product_ID 
    JOIN product_category pc ON p.PC_ID = pc.PC_ID
    JOIN size s ON p.Size_ID = s.Size_ID
    LEFT JOIN product_delete pd ON p.Product_ID = pd.Product_ID
    WHERE pd.Product_ID IS NULL
    AND Product_Name LIKE '%$pname%'
    ORDER BY p.Product_ID DESC";

    $psearch_list = mysqli_query($conn, $sql);
    if(!$psearch_list){
        echo "Error executing the query: " . mysqli_error($conn);
    }
    
    
}

?>
    <!DOCTYPE html>
    <html lang="en">
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

            <!-- Content Start -->
            <div class="bg-secondary rounded h-100 p-4" style="margin-left:17px; margin-top:15px;margin-right:17px">
            <div>
            <div style="display:flex;justify-content:space-between"><h3>Product List</h3>
                <form action="export_orders_to_excel.php?id=3"method="post">
                        <button type="submit" class="btn btn-success">Export to Excel</button>
                    </form>
                </div>
                <div>
                    <form action="product_list-page.php" method="post">
                        <input type="text" name="name" placeholder="Search Product by name" class="form-control">
                        <button type="submit" name="psearch"class="btn btn-primary mt-2">Search</button>
                    </form>
                </div>
            </div>
            <?php if(isset($_POST['psearch'])){ ?>
                <hr>
                <table class="table text-start align-middle table-bordered table-hover mb-0">
                    <thead>
                        <tr class="text-white">
                            <th scope="col">#</th>
                            <th scope="col">Image</th>
                            <th scope="col">ID</th>
                            <th scope="col">Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Description</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Price</th>
                            <th scope="col">Cost</th>
                            <th scope="col">Size</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th> <!-- Added column for action -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php
                                if ($psearch_list) {
                                    $num = 0;
                                    while ($row = mysqli_fetch_assoc($psearch_list)) {
                                        $num++;
                                        echo "<tr>";
                                        // Fetch the image filename corresponding to the product ID
                                        $image = $row['Product_Image'];
                                        echo "<td>{$num}</td>";
                                        echo "<td><img src='img/{$image}' alt='image' width='100' height='100'></td>";
                                        echo "<td>{$row['Product_ID']}</td>";
                                        echo "<td>{$row['Product_Name']}</td>";
                                        echo "<td>{$row['Category']}</td>";
                                        echo "<td>{$row['Product_Description']}</td>";
                                        echo "<td>{$row['Product_quantity_available']}</td>";
                                        echo "<td>{$row['Product_Price']}</td>";
                                        echo "<td>{$row['Product_Cost']}</td>";
                                        echo "<td>{$row['Size']}</td>";
                                        $status = ($row['Product_Status'] == 1) ? "Active" : "Inactive";
                                        echo "<td>{$status}</td>";
                                        echo "<td><button type='button' onclick='editpage({$row['Product_ID']})' class='btn btn-primary'>Edit</button></td>";
                                        // Pass the product ID to the editpage function
                                    }}
                                
                            ?>
                                       

                        </tr>
                    </tbody>
                </table>
                <hr>
            <?php } ?>
            <?php include 'product_list.php' ?>
            <hr>
            <button type="button" class="btn btn-primary" onclick="back()">Back</button>
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