<?php
// Include database connection
include 'dataconnection.php';

// Ensure the database connection is established
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the 'id' parameter is set and handle accordingly
if (isset($_GET['id']) && $_GET['id'] == 1) {
    // Export orders to Excel

    // Set headers to indicate that the response is an Excel file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="orders_report.xls"');
    header('Cache-Control: max-age=0');

    // Query to fetch the order data
    $sql = "
    SELECT o.Order_ID, c.Customer_Username, i.Quantity, i.Total_Price, o.Order_Status, o.Order_Date
    FROM `order` o
    JOIN order_item i ON o.Order_ID = i.Order_ID
    JOIN customer c ON o.Customer_ID = c.Customer_ID
    JOIN product p ON i.Product_ID = p.Product_ID
    ORDER BY o.Order_Date DESC";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Start the HTML table
        echo "<table border='1'>";
        echo "<tr>
                <th>#</th>
                <th>Order ID</th>
                <th>Customer Username</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Order Status</th>
                <th>Order Date</th>
              </tr>";

        // Fetch each row and write it to the table
        $num = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $formattedOrderDate = (new DateTime($row['Order_Date']))->format('Y-m-d');
            $num++;
            echo "<tr>";
            echo "<td>{$num}</td>";
            echo "<td>{$row['Order_ID']}</td>";
            echo "<td>{$row['Customer_Username']}</td>";
            echo "<td>{$row['Quantity']}</td>";
            echo "<td>{$row['Total_Price']}</td>";
            echo "<td>{$row['Order_Status']}</td>";
            echo "<td>{$formattedOrderDate}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Error executing the query: " . mysqli_error($conn);
    }
} elseif (isset($_GET['id']) && $_GET['id'] == 2) {
    // Export customers to Excel

    // Set headers to indicate that the response is an Excel file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="customers_report.xls"');
    header('Cache-Control: max-age=0');

    // Query to fetch the customer data
    $sql = "SELECT * FROM customer ORDER BY Customer_ID DESC";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Start the HTML table
        echo "<table border='1'>";
        echo "<tr>
                <th>#</th>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Register Time</th>
                <th>Address 1</th>
                <th>Address 2</th>
                <th>Address 3</th>
              </tr>";

        // Fetch each row and write it to the table
        $num = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $formattedOrderDate = (new DateTime($row['Customer_Register_Time']))->format('Y-m-d');
            $num++;
            echo "<tr>";
            echo "<td>{$num}</td>";
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
        echo "</table>";
    } else {
        echo "Error executing the query: " . mysqli_error($conn);
    }
} elseif (isset($_GET['id']) && $_GET['id'] == 3) {
    
    // Ensure the database connection is established
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    // Set headers to indicate that the response is an Excel file
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="product_list.xls"');
    header('Cache-Control: max-age=0');
    
    // Query to fetch the product data
    $sql = "SELECT *, p.Product_ID AS Product_ID FROM product p 
            JOIN record_time r ON p.Product_ID = r.Product_ID 
            JOIN product_category pc ON p.PC_ID = pc.PC_ID
            JOIN size s ON p.Size_ID = s.Size_ID
            LEFT JOIN product_delete pd ON p.Product_ID = pd.Product_ID
            WHERE pd.Product_ID IS NULL
            ORDER BY p.Product_ID DESC";
    
    $result = mysqli_query($conn, $sql);
    
    if ($result) {
        // Start the HTML table
        echo "<table border='1'>";
        echo "<tr>
                <th>#</th>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Cost</th>
                <th>Size</th>
                <th>Status</th>
              </tr>";
    
        // Fetch each row and write it to the table
        $num = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $num++;
            echo "<tr>";
            // Fetch the image filename corresponding to the product ID
            $image = $row['Product_Image'];
            echo "<td>{$num}</td>";
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
            echo "</tr>";   
        }
        echo "</table>";
    } else {
        echo "Error executing the query: " . mysqli_error($conn);
    }

} else {
    echo "Invalid request";
}
?>