<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="lib/owlcarousel/owl.carousel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

<!-- Sales and Navigation Functions -->
<script>
    function salesreport(productID) {
        window.location.href = "Sales.php?id=" + productID;
    }

    function back() {
        window.history.back();
    }
</script>

<!-- Sales Data and Charts -->
<?php

$sql = "SELECT 
            YEAR(b.Invoice_Date) AS year, 
            SUM(b.Total_Amount) AS total_price, 
            SUM(r.Quantity * p.Product_Cost) AS total_cost
        FROM 
            bill_master b
        JOIN 
            bill_master_transaction r ON b.Invoice_ID = r.Invoice_ID
        JOIN 
            product p ON r.Product_ID = p.Product_ID
        WHERE 
            b.Invoice_Date BETWEEN '2018-01-01' AND CURDATE()
        GROUP BY 
            YEAR(b.Invoice_Date)";

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }                        
    $years = [];
    $totalPrices = [];
    $totalCosts = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $years[] = $row['year'];
        $totalPrices[] = $row['total_price'];
        $totalCosts[] = $row['total_cost'];
    }

    mysqli_close($conn);
?>
<script>
    const years = <?php echo json_encode($years); ?>;
    const totalPrices = <?php echo json_encode($totalPrices); ?>;
    const totalCosts = <?php echo json_encode($totalCosts); ?>;

    const lineColors = ["rgba(0,0,255,1.0)", "rgba(255,0,0,1.0)"];
    
    new Chart("myChart", {
        type: "line",
        data: {
            labels: years,
            datasets: [{
                fill: false,
                lineTension: 0,
                backgroundColor: lineColors[0],
                borderColor: lineColors[0],
                label: 'Total Price',
                data: totalPrices,
            }, {
                fill: false,
                lineTension: 0,
                backgroundColor: lineColors[1],
                borderColor: lineColors[1],
                label: 'Total Cost',
                data: totalCosts,
            }]
        },
        options: {
            legend: { display: true },
            scales: {
                yAxes: [{ ticks: { min: 0 } }],
            }
        }
    });

    const barColors = ["red", "green", "blue", "orange", "brown", "purple", "pink"];

    new Chart("Sales", {
        type: "bar",
        data: {
            labels: years,
            datasets: [{
                label: 'Total Price',
                backgroundColor: barColors,
                data: totalPrices,
            }]
        },
        options: {
            legend: { display: false },
            title: {
                display: true,
                text: "Total Price by Year"
            }
        }
    });
</script>

<!-- Additional UI Functionality -->
<script>
    (function ($) {
        "use strict";

        // Spinner
        var spinner = function () {
            setTimeout(function () {
                if ($('#spinner').length > 0) {
                    $('#spinner').removeClass('show');
                }
            }, 1);
        };
        spinner();
        
        // Back to top button
        $(window).scroll(function () {
            if ($(this).scrollTop() > 300) {
                $('.back-to-top').fadeIn('slow');
            } else {
                $('.back-to-top').fadeOut('slow');
            }
        });
        $('.back-to-top').click(function () {
            $('html, body').animate({ scrollTop: 0 }, 1500, 'easeInOutExpo');
            return false;
        });

        $('.sidebar-toggler').click(function () {
            $('.sidebar, .content').toggleClass("open");
            return false;
        });

        /* Additional code commented out for clarity */
    })(jQuery);
</script>
