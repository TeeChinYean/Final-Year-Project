<!DOCTYPE html>
<html>
    <body>
        <script>
            function editpage(productID) {
                // Set the product ID to the session variable
                window.location.href = "edit_product.php?id=" + productID;
            }
        </script>
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
                                    // Pass the product ID to the editpage function
                                    echo "<td><button type='button' onclick='editpage({$row['Product_ID']})' class='btn btn-primary'>Edit</button></td>";
                                    echo "</tr>";   
                                }
                            } else {
                                echo "Error executing the query: " . mysqli_error($conn);
                            }
                        ?>
                    </tbody>
                </table>

    </body>
</html>
