<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
    exit();
}

// --- DEFINE THE DATE FILTER LOGIC ---
$date_filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$where_clause = "";

if ($date_filter == 'today') {
    $where_clause = " WHERE DATE(created_at) = CURRENT_DATE()";
} elseif ($date_filter == 'yesterday') {
    $where_clause = " WHERE DATE(created_at) = CURRENT_DATE() - INTERVAL 1 DAY";
} elseif ($date_filter == 'custom' && !empty($start_date) && !empty($end_date)) {
    $start = mysqli_real_escape_string($conn, $start_date);
    $end = mysqli_real_escape_string($conn, $end_date);
    $where_clause = " WHERE DATE(created_at) BETWEEN '$start' AND '$end'";
}

/* TOTAL CARD COUNT */
$total_query = mysqli_query($conn, "SELECT * FROM orders" . $where_clause);
$total_orders = mysqli_num_rows($total_query);

/* PENDING */
$p_cond = empty($where_clause) ? " WHERE status='Pending' OR status='Paid'" : $where_clause . " AND (status='Pending' OR status='Paid')";
$pending_query = mysqli_query($conn, "SELECT * FROM orders" . $p_cond);
$pending_orders = mysqli_num_rows($pending_query);

/* READY */
$r_cond = empty($where_clause) ? " WHERE status='Ready'" : $where_clause . " AND status='Ready'";
$ready_query = mysqli_query($conn, "SELECT * FROM orders" . $r_cond);
$ready_orders = mysqli_num_rows($ready_query);

/* COMPLETED */
$c_cond = empty($where_clause) ? " WHERE status='Completed'" : $where_clause . " AND status='Completed'";
$completed_query = mysqli_query($conn, "SELECT * FROM orders" . $c_cond);
$completed_orders = mysqli_num_rows($completed_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <title>Admin Dashboard - WashWave</title>
    <script>
        function toggleMenu() {
            let menu = document.getElementById("dropdown");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }
        window.onclick = function (e) {
            if (!e.target.closest(".profile")) {
                let drop = document.getElementById("dropdown");
                if(drop) drop.style.display = "none";
            }
        }
    </script>
    
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f8f9fa; color: #333; }
        .dashboard-container { display: flex; min-height: 100vh; flex-direction: column; }
        
        /* --- SIDEBAR DESKTOP CORE FIXED POSITIONS --- */
        .sidebar {
            position: fixed; top: 0; bottom: auto; left: 0; width: 180px; height: 100vh;
            background: #fff; box-shadow: 3px 0 12px rgba(0,0,0,.08); display: flex;
            flex-direction: column; justify-content: flex-start; padding-top: 20px; z-index: 999;
        }
        .menu { display: flex; flex-direction: column; justify-content: flex-start; gap: 15px; height: 100%; padding: 0; width: 100%; }
        .logo { display: block; width: 130px; height: 130px; margin: 0 auto 25px auto; object-fit: cover; border-radius: 50%; border: 2px solid #eee; background: #fff; }
        .menu a { text-decoration: none; display: flex; flex-direction: column; align-items: center; font-size: 13px; font-weight: 500; color: black; gap: 4px; margin: 8px 0; width: 100%; }
        .menu a i { font-size: 20px; }
        .menu a:hover { color: rgb(106, 105, 105); }

        /* Main Content wrapper matches clean margins offset from sidebar */
        .main { margin-left: 180px; width: calc(100% - 180px); display: flex; flex-direction: column; padding-bottom: 0; min-height: 100vh; }
        .header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); gap: 15px; }
        .search-box { display: flex; align-items: center; background: #f1f3f6; padding: 6px 12px; border-radius: 6px; flex: 1; max-width: 300px; }
        .search-box input { border: none; background: transparent; padding: 6px; margin-left: 4px; color: black; width: 100%; outline: none; }
        .mobile-header-logo { display: none; }

        /* --- DASHBOARD METRIC GRADIENTS (Maintained matching clean colors) --- */
        .cards a { text-decoration: none; display: block; }
        .card-stat { padding: 25px 20px; border-radius: 12px; color: white; text-align: center; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.15); min-height: 180px; border: none; display: flex; flex-direction: column; justify-content: center; align-items: center; }
        .card-stat h3 { margin: 10px 0 5px 0; font-size: 16px; font-weight: 500; opacity: 0.9; }
        .card-stat h2 { margin: 0; font-size: 28px; font-weight: 700; }
        .card-stat:hover { transform: translateY(-5px); }

        .total { background: linear-gradient(165deg, #0f2027, #2c5364, #00c9a7); }
        .total:hover { box-shadow: 0 0 30px rgba(0, 128, 128, 0.6); }
        .pending { background: linear-gradient(135deg, #ff6a00, #ffdd00); }
        .pending:hover { box-shadow: 0 0 30px rgba(255, 128, 0, 0.6); }
        .ready { background: linear-gradient(135deg, #00eeff, #09408d); }
        .ready:hover { box-shadow: 0 0 30px rgba(0, 238, 255, 0.6); }
        .completed { background: linear-gradient(135deg, #00b09b, #96c93d); }
        .completed:hover { box-shadow: 0 0 30px rgba(1, 197, 138, 0.6); }

        /* --- RECENT ORDER BADGES --- */
        .status-completed, .status-ready, .status-processing, .status-pending, .status-cancelled { padding: 5px 12px; border-radius: 20px; color: white; font-size: 12px; font-weight: 600; display: inline-block; text-transform: capitalize; }
        .status-pending { background-color: #f59e0b; color: #fff; }
        .status-processing { background-color: #3b82f6; }
        .status-ready { background-color: #14b8a6; }
        .status-completed { background-color: #22c55e; }
        .status-cancelled { background-color: #ef4444; color: #fff; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2); }

        /* --- MOBILE OVERRIDES FOR PHONE LAYOUT RESPONSIVENESS --- */
        @media (max-width: 768px) {
            .sidebar { position: fixed; bottom: 0; top: auto; left: 0; width: 100%; height: 70px; flex-direction: row; box-shadow: 0 -3px 12px rgba(0,0,0,.12); padding-top: 0; align-items: center; justify-content: center; }
            .menu { flex-direction: row; justify-content: space-around; align-items: center; padding: 0 10px; }
            .logo { display: none; }
            .menu a { font-size: 11px; flex: 1; margin: 0; gap: 2px; }
            .menu a i { font-size: 18px; }
            .main { margin-left: 0; width: 100%; padding-bottom: 80px; }
            .mobile-header-logo { display: block; width: 45px; height: 45px; object-fit: cover; border-radius: 50%; border: 1px solid #eee; }
        }
    </style>
</head>
<body>

<div class="dashboard-container">

    <div class="sidebar">
        <div class="menu">
            <img src="images/wash_wave-logo.jpg" class="logo" alt="WashWave Logo">
            <a href="admin-dashboard.php"><i class="fas fa-th-large" style="color: rgb(106, 105, 105);"></i><span style="color: rgb(106, 105, 105);">Dashboard</span></a>
            <a href="admin-customers.php"><i class="fas fa-users"></i>Customers</a>
            <a href="admin-orders.php"><i class="fas fa-box"></i>Orders</a>
            <a href="admin-logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
        </div>
    </div>

    <div class="main">

        <div class="header">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search...">
            </div>
            <img src="images/wash_wave-logo.jpg" class="mobile-header-logo" alt="WashWave Logo">
        </div>

        <div class="container-fluid p-4">
            <h1 class="fw-bold mb-4" style="font-size: 32px;">Dashboard Overview</h1>

            <div class="card p-3 border-0 shadow-sm mb-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                    <div class="btn-group" role="group">
                        <a href="admin-dashboard.php?filter=all" class="btn btn-sm <?php echo ($date_filter == 'all') ? 'btn-dark' : 'btn-light'; ?> fw-semibold px-3 py-2">All Time</a>
                        <a href="admin-dashboard.php?filter=today" class="btn btn-sm <?php echo ($date_filter == 'today') ? 'btn-dark' : 'btn-light'; ?> fw-semibold px-3 py-2">Today</a>
                        <a href="admin-dashboard.php?filter=yesterday" class="btn btn-sm <?php echo ($date_filter == 'yesterday') ? 'btn-dark' : 'btn-light'; ?> fw-semibold px-3 py-2">Yesterday</a>
                    </div>
                    
                    <form action="admin-dashboard.php" method="GET" class="d-flex flex-wrap align-items-center gap-2 m-0">
                        <input type="hidden" name="filter" value="custom">
                        <span class="small fw-bold text-secondary">From:</span>
                        <input type="date" name="start_date" class="form-control form-control-sm w-auto" value="<?php echo htmlspecialchars($start_date); ?>" required>
                        <span class="small fw-bold text-secondary">To:</span>
                        <input type="date" name="end_date" class="form-control form-control-sm w-auto" value="<?php echo htmlspecialchars($end_date); ?>" required>
                        <button type="submit" class="btn btn-sm btn-dark fw-bold px-3">Filter Range</button>
                    </form>
                </div>
            </div>

            <div class="row g-3 cards mb-4">
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <a href="admin-orders.php">
                        <div class="card-stat total">
                            <i class="fas fa-clipboard-list fa-2x"></i>
                            <h3>Total Orders</h3>
                            <h2><?php echo $total_orders; ?></h2>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <a href="admin-orders-pending.php">
                        <div class="card-stat pending">
                            <i class="fas fa-hourglass-half fa-2x"></i>
                            <h3>Pending Orders</h3>
                            <h2><?php echo $pending_orders; ?></h2>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <a href="admin-orders-ready.php">
                        <div class="card-stat ready">
                            <i class="fas fa-clock fa-2x"></i>
                            <h3>Orders Ready</h3>
                            <h2><?php echo $ready_orders; ?></h2>
                        </div>
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3">
                    <a href="admin-orders-completed.php">
                        <div class="card-stat completed">
                            <i class="fas fa-check-circle fa-2x"></i>
                            <h3>Completed Orders</h3>
                            <h2><?php echo $completed_orders; ?></h2>
                        </div>
                    </a>
                </div>
            </div>

            <div class="row g-4">
                
               <div class="col-12 col-lg-8 d-flex">
                    <div class="card p-4 border-0 shadow-sm w-100 d-flex flex-column">
                        <h2 class="h5 fw-bold mb-3">Recent Orders</h2>
                        <div class="table-responsive flex-grow-1">
                            <table class="table align-middle m-0">
                                <thead class="table-light text-secondary">
                                    <tr>
                                    <th>Order ID</th>
                                    <th>Customer Name</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                $table_query = "SELECT * FROM orders" . $where_clause . " ORDER BY created_at DESC LIMIT 5";
                                $query = mysqli_query($conn, $table_query);
                    
                                if (mysqli_num_rows($query) > 0) {
                                    while($row = mysqli_fetch_assoc($query)) {
                                        $db_status = $row['status'];
                                        $display_status = ($db_status === 'Paid') ? 'Pending' : $db_status;
                                ?>
                                <tr>
                                <td class="fw-bold">#<?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['service_type']); ?></td>
                                <td>
                                    <span class="status-<?php echo strtolower($display_status); ?>">
                                        <?php echo htmlspecialchars($display_status); ?>
                                    </span>
                                </td>
                                </tr>
                                <?php 
                                }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No entries found within selected range.</td></tr>";
                                }
                                ?>
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="card p-4 border-0 shadow-sm h-100 d-flex flex-column align-items-center justify-content-center">
                        <h2 class="h6 fw-bold mb-3 text-center w-100">Order Breakdown</h2>
                        <div class="position-relative w-100 d-flex justify-content-center" style="max-width: 230px; height: 230px;">
                            <canvas id="orderPieChart"></canvas>
                        </div>
                    </div>
                </div>

            </div> </div> </div> </div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('orderPieChart').getContext('2d');
    
    const pendingCount = <?php echo $pending_orders; ?>;
    const readyCount = <?php echo $ready_orders; ?>;
    const completedCount = <?php echo $completed_orders; ?>;

    if (pendingCount === 0 && readyCount === 0 && completedCount === 0) {
        ctx.font = "14px Arial";
        ctx.fillStyle = "#999";
        ctx.textAlign = "center";
        ctx.fillText("No analytical data plotted", 115, 115);
        return;
    }

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Pending', 'Ready', 'Completed'],
            datasets: [{
                data: [pendingCount, readyCount, completedCount],
                backgroundColor: [
                    '#ff9f43', 
                    '#00d2ff', 
                    '#22c55e'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        font: { size: 12, weight: '600' },
                        padding: 15
                    }
                }
            }
        }
    });
});
</script>

</body>
</html>