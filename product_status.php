<?php 
    require 'dataconnection.php';

    if(isset($_POST["editpage"]))
    {
        header("Location: product_status-edit.php");
    }

    if(isset($_POST["search"])) {
        $ID = isset($_POST["Product_Id"]) ? trim($_POST["Product_Id"]) : '';
      
        $checkExisting = mysqli_prepare($conn, "SELECT * FROM product p 
        JOIN record_time r ON p.Product_ID = r.Product_ID
        JOIN product_category c ON p.PC_ID = c.PC_ID
        JOIN size s ON p.Size_ID = s.Size_ID
        LEFT JOIN product_delete pd ON p.Product_ID = pd.Product_ID
        WHERE p.Product_ID = ? and pd.Product_ID IS NULL");
        
        if (!$checkExisting) {
            die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
        }
        
        mysqli_stmt_bind_param($checkExisting, "s", $ID);
        mysqli_stmt_execute($checkExisting);
        $resultExisting = mysqli_stmt_get_result($checkExisting);
        $resultnull=true;
        if ($resultExisting->num_rows > 0) {
            $row = mysqli_fetch_assoc($resultExisting);
            $ID = $row["Product_ID"];
            $Name = $row["Product_Name"];
            $pCategory = $row["Category"];
            $pDescription = $row["Product_Description"];
            $pQuantity = $row["Product_quantity_available"];
            $pPrice = $row["Product_Price"];
            $pCost = $row["Product_Cost"];
            $Size = $row["Size"];
            $Status = $row["Product_Status"];
            $resultnull=false;
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


                
                <div class="container-fluid pt-4 px-4">
                    <div class="vh-100 bg-secondary rounded mx-0" style="padding:10px 10px 10px 10px;overflow-y:auto;scrollbar-color:white gray; scrollbar-width:thin;">
                            <div class="flex-container">
                                <div>
                                    <h3>Product Status Change</h3>
                                </div>
                                <div>
                                    <form action="product_status.php" method="post">
                                        <input type="text" name="Product_Id" placeholder="Enter name to edit">
                                        <button type="submit" class="btn btn-primary" name="search">Search</button>
                                    </form>
                                </div>
                                    <div>
                                        <form action="product_status.php" method="post">
                                            <button type="submit" class="btn btn-primary" name="editpage">Multi Edit</button>
                                        </form>
                                    </div>
                            </div>
                            <?php if(isset($checkExisting)){ 
                                echo "<hr>";
                                if($resultnull )
                                {
                                    echo "<h3>Product ID does not exist</h3>";
                                } else{?>
                              
                                
                                <form action="product_status-edit.php" method="post">
                                    <input type="hidden" name="Product_Id" value="<?php echo $ID; ?>">
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
                                                <th scope="col">Size</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td><?php echo $ID; ?></td>
                                                <td><?php echo $Name; ?></td>
                                                <td><?php echo $pCategory; ?></td>
                                                <td><?php echo $pDescription; ?></td>
                                                <td><?php echo $pQuantity; ?></td>
                                                <td><?php echo $pPrice; ?></td>
                                                <td><?php echo $Size; ?></td>
                                                <td><?php echo $pCost; ?></td>
                                                <td>
                                                    <div class='form-floating mb-3'>
                                                        <select class='form-select' id='floatingSelect' name='Status' aria-label='Floating label select example'>
                                                            <option value='1' <?php echo ($Status == '1') ? 'selected' : ''; ?>>Active</option>
                                                            <option value='0' <?php echo ($Status == '0') ? 'selected' : ''; ?>>Inactive</option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="submit" name="edit" class="btn btn-primary">Edit</button>
                                </form>
                                <?php } }?>
                                <hr>
                            <div class="table-responsive">
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
                                 $sql = "SELECT *, p.Product_ID AS Product_ID FROM product p 
                                 JOIN record_time r ON p.Product_ID = r.Product_ID 
                                 JOIN product_category pc ON p.PC_ID = pc.PC_ID
                                 JOIN size s ON p.Size_ID = s.Size_ID
                                 LEFT JOIN product_delete pd ON p.Product_ID = pd.Product_ID
                                 WHERE pd.Product_ID IS NULL
                                 ORDER BY p.Product_ID DESC";

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
                                            echo "<td>{$row['Product_Description']}</td>";
                                            echo "<td>{$row['Product_quantity_available']}</td>";
                                            echo "<td>{$row['Product_Price']}</td>";
                                            echo "<td>{$row['Product_Cost']}</td>";
                                            if($row['Product_Status'] == 1){
                                                $status="Active";
                                            }else{
                                                $status="Inactive";
                                            }
                                            echo "<td>{$status}</td>";
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