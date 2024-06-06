<?php 
require 'dataconnection.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['editpage'])) {
    header("Location: edit_order.php");
    exit();
}

if (isset($_POST["search"])) {
    $Order_ID = trim($_POST["Invoice_ID"] ?? '');
    if ($Order_ID !== '') {
        $query = "SELECT 
                b.Invoice_ID, p.Product_Name, c.Customer_Username, b.Invoice_Date, 
                r.Quantity, b.Total_Amount, b.Delivery_Address, b.Invoice_Status
            FROM `bill_master` b
            JOIN bill_master_transaction r ON b.Invoice_ID = r.Invoice_ID 
            JOIN product p ON r.Product_ID = p.Product_ID 
            JOIN customer c ON b.Customer_ID = c.Customer_ID
            WHERE b.Invoice_ID = ?";
        
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $Order_ID);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($result->num_rows > 0) {
                $row = mysqli_fetch_assoc($result);
                $ID = $row['Invoice_ID'];
                $Product_Name = $row['Product_Name'];
                $Customer_Username = $row['Customer_Username'];
                $Order_Date = $row['Invoice_Date'];
                $Quantity = $row['Quantity'];
                $Total_Price = $row['Total_Amount'];
                $Shipping_Address = $row['Delivery_Address'];
                $Order_Status = $row['Invoice_Status'];
                extract($row);
            } else {
                echo "<script>alert('Order ID does not exist');</script>";
            }
        } else {
            die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
        }
    }
}

if (isset($_POST["edit"])) {
    $ID = trim($_POST["Invoice_ID"] ?? '');
    $Status = trim($_POST["Status"] ?? '');
    
    if ($ID !== '' && $Status !== '') {
        $query = "UPDATE `order` SET Order_Status = ? WHERE Order_ID = ?";
        
        if ($stmt = mysqli_prepare($conn, $query)) {
            mysqli_stmt_bind_param($stmt, "ss", $Status, $ID);
            mysqli_stmt_execute($stmt);
            
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                echo "<script>alert('Order Updated Successfully');</script>";
            } else {
                echo "<script>alert('Order Update Failed');</script>";
            }

            header("Location: order.php");
            exit();
        } else {
            die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
        }
    }
}

function fetchOrders($conn, $statuses, $offset, $limit, $excludeDelivered = false) {
    $query = "SELECT 
    b.Invoice_ID, p.Product_Name, c.Customer_Username, b.Invoice_Date, 
    r.Quantity, b.Total_Amount, b.Delivery_Address, b.Invoice_Status
FROM `bill_master` b
JOIN bill_master_transaction r ON b.Invoice_ID = r.Invoice_ID 
JOIN product p ON r.Product_ID = p.Product_ID 
JOIN customer c ON b.Customer_ID = c.Customer_ID
    WHERE b.Invoice_Status IN ('" . implode("','", $statuses) . "')";
    if ($excludeDelivered) {
        $query .= " AND b.Invoice_Status != 'Delivered' ";
    }
    $query .= "ORDER BY b.Invoice_ID DESC LIMIT ?, ?";

    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "ii", $offset, $limit);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    } else {
        die("Prepare failed: (" . mysqli_errno($conn) . ") " . mysqli_error($conn));
    }
}

$offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
$limit = 10;
$statuses = ['Ordered', 'Preparing', 'In transit'];

$result = fetchOrders($conn, $statuses, $offset, $limit, true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include 'head.html'; ?>
    <style>
        .flex-container {
            display: flex;
            margin-bottom: 10px;
        }

        .flex-container > div {
            margin: 0 10px;
        }

        .container-fluid {
            padding: 10px;
        }

        .table-responsive {
            overflow-y: auto;
            scrollbar-color: white gray;
            scrollbar-width: thin;
        }

        .scrollable-table {
            height: 370px;
            overflow-y: auto;
            scrollbar-color: white gray;
            scrollbar-width: thin;
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
        <?php include 'sidebar.php'; ?>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <?php include 'navbar.php'; ?>
            <!-- Navbar End -->

            <!-- Order List Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="vh-100 bg-secondary rounded mx-0 p-4">
                    <div class="flex-container">
                        <div><h2>Order</h2></div>
                        <div>
                            <form action="order.php" method="post">
                                <input type="text" name="Order_ID" placeholder="Change status, enter ID">
                                <button type="submit" class="btn btn-primary" name="search">Search</button>
                            </form>
                        </div>
                        <div>
                            <form action="order.php" method="post">
                                <button name="editpage" class="btn btn-primary">Multi Edit</button>
                            </form>
                        </div>
                    </div>

                    <?php if (isset($Order_ID) && isset($ID)): ?>
                        <form action="order.php" method="post">
                            <input type="hidden" name="Order_ID" value="<?php echo $ID; ?>">
                            <table class="table text-start align-middle table-bordered table-hover mb-0">
                                <thead>
                                    <tr class="text-white">
                                        <th>#</th>
                                        <th>Order ID</th>
                                        <th>Product Name</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><?php echo $ID; ?></td>
                                        <td><?php echo $Product_Name; ?></td>
                                        <td><?php echo $Customer_Username; ?></td>
                                        <td><?php echo $Order_Date; ?></td>
                                        <td><?php echo $Quantity; ?></td>
                                        <td><?php echo $Total_Price; ?></td>
                                        <td><?php echo $Shipping_Address; ?></td>
                                        <td>
                                            <div class="form-floating mb-3">
                                                <select class="form-select" id="floatingSelect" name="Status" aria-label="Floating label select example">
                                                    <option value="Ordered" <?php echo ($Order_Status == 'Ordered') ? 'selected' : ''; ?>>Ordered</option>
                                                    <option value="Preparing" <?php echo ($Order_Status == 'Preparing') ? 'selected' : ''; ?>>Preparing</option>
                                                    <option value="In transit" <?php echo ($Order_Status == 'In transit') ? 'selected' : ''; ?>>In transit</option>
                                                    <option value="Delivered" <?php echo ($Order_Status == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="submit" name="edit" class="btn btn-primary">Edit</button>
                        </form>
                        <hr>
                    <?php endif; ?>

                    <!--Main content-->
                    <!-- Order List -->
                    <div class="table-responsive">
                        <div class="scrollable-table">
                            <h4>Order List</h4>
                            <table class="table text-start align-middle table-bordered table-hover mb-0" id="orderListTable">
                                <thead>
                                    <tr class="text-white">
                                        <th>#</th>
                                        <th>Order ID</th>
                                        <th>Product Name</th>
                                        <th>Customer</th>
                                        <th>Order Date</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="orderListBody">
                                    <?php
                                    $result = fetchOrders($conn, $statuses, 0, 10);
                                    $num = 0;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $num++;
                                        $formattedOrderDate = (new DateTime($row['Order_Date']))->format('Y-m-d');
                                        echo "<tr>";
                                        echo "<td>$num</td>";
                                        echo "<td>{$row['Invoice_ID']}</td>";
                                        echo "<td>{$row['Product_Name']}</td>";
                                        echo "<td>{$row['Customer_Username']}</td>";
                                        echo "<td>{$row['Invoice_Date']}</td>";
                                        echo "<td>{$row['Quantity']}</td>";
                                        echo "<td>{$row['Total_Amount']}</td>";
                                        echo "<td>{$row['Delivery_Address']}</td>";
                                        echo "<td>{$row['Invoice_Status']}</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button class="btn btn-primary" id="loadMoreOrders" data-offset="10">Load More</button>
                        </div>
                    </div>
                    <hr>

                    <!-- Order History -->
                    <div class="table-responsive">
                        <div class="scrollable-table">
                            <h4>Order History</h4>
                            <table class="table text-start align-middle table-bordered table-hover mb-0" id="orderHistoryTable">
                                <thead>
                                    <tr class="text-white">
                                        <th>#</th>
                                        <th>Order ID</th>
                                        <th>Product Name</th>
                                        <th>Customer</th>
                                        <th>Order Date</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="orderHistoryBody">
                                    <?php
                                    $result = fetchOrders($conn, ['Delivered'], 0, 10);
                                    $num = 0;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $num++;
                                        $formattedOrderDate = (new DateTime($row['Order_Date']))->format('Y-m-d');
                                        echo "<tr>";
                                        echo "<td>$num</td>";
                                        echo "<td>{$row['Invoice_ID']}</td>";
                                        echo "<td>{$row['Product_Name']}</td>";
                                        echo "<td>{$row['Customer_Username']}</td>";
                                        echo "<td>{$row['Invoice_Date']}</td>";
                                        echo "<td>{$row['Quantity']}</td>";
                                        echo "<td>{$row['Total_Amount']}</td>";
                                        echo "<td>{$row['Delivery_Address']}</td>";
                                        echo "<td>{$row['Invoice_Status']}</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                            <button class="btn btn-primary" id="loadMoreHistory" data-offset="10">Load More</button>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-primary" onclick="history.back()">Back</button>
                </div>
            </div>
            <!-- Order List End -->
        </div>
        <!-- Content End -->
    </div>

    <!-- Main Script -->
    <?php require "js/Main_js.php"; ?>
    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        document.getElementById('loadMoreOrders').addEventListener('click', function() {
            loadMoreOrders(['Ordered', 'Preparing', 'In transit'], 'orderListTable', 'orderListBody', this);
        });

        document.getElementById('loadMoreHistory').addEventListener('click', function() {
            loadMoreOrders(['Delivered'], 'orderHistoryTable', 'orderHistoryBody', this);
        });

        function loadMoreOrders(statuses, tableId, tbodyId, button) {
            const offset = button.getAttribute('data-offset');
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'load_more_orders.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    document.getElementById(tbodyId).innerHTML += xhr.responseText;
                    button.setAttribute('data-offset', parseInt(offset) + 10);
                }
            };
            xhr.send('statuses=' + JSON.stringify(statuses) + '&offset=' + offset);
        }
    </script>
</body>
</html>
