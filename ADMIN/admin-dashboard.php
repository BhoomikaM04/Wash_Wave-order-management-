<?php

session_start();

include "../db.php";

/* TOTAL */
$total_query = mysqli_query($conn, "SELECT * FROM orders");
$total_orders = mysqli_num_rows($total_query);

/* PENDING */
// Count both standard 'Pending' orders and newly placed 'Paid' UPI orders that haven't been processed yet
$pending_query = mysqli_query($conn, "SELECT * FROM orders WHERE status='Pending' OR status='Paid'");
$pending_orders = mysqli_num_rows($pending_query);

/* READY */
$ready_query = mysqli_query($conn, "SELECT * FROM orders WHERE status='Ready'");
$ready_orders = mysqli_num_rows($ready_query);

/* COMPLETED */
$completed_query = mysqli_query($conn, "SELECT * FROM orders WHERE status='Completed'");
$completed_orders = mysqli_num_rows($completed_query);

if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    
    <title>Admin Dashboard - WashWave</title>
    <script>
        function toggleMenu() {
            let menu = document.getElementById("dropdown");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }

        function uploadImage() {
            document.getElementById("fileInput").click();
        }

        function changeImage(event) {
            let reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById("profilePic").src = e.target.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        window.onclick = function (e) {
            if (!e.target.closest(".profile")) {
                document.getElementById("dropdown").style.display = "none";
            }
        }
    </script>
    
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, sans-serif; background: #f8f9fa; color: #333; }
        .dashboard-container { display: flex; min-height: 100vh; flex-direction: column; }
        .sidebar { position: fixed; bottom: 0; left: 0; width: 100%; height: 70px; background: #fff; box-shadow: 0 -3px 12px rgba(0,0,0,.12); z-index: 999; display: flex; align-items: center; justify-content: center; }
        .menu { display: flex; flex-direction: row; justify-content: space-around; align-items: center; width: 100%; padding: 0 10px; }
        .logo { display: none; }
        .menu a { text-decoration: none; display: flex; flex-direction: column; align-items: center; font-size: 11px; color: black; flex: 1; gap: 4px; }
        .menu a i { font-size: 20px; }
        .menu a:hover { color: rgb(106, 105, 105); }
        .main { width: 100%; min-height: 100vh; display: flex; flex-direction: column; padding-bottom: 80px; }
        .header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); gap: 15px; }
        .search-box { display: flex; align-items: center; background: #f1f3f6; padding: 6px 12px; border-radius: 6px; flex: 1; max-width: 300px; }
        .search-box i { color: #777; }
        .search-box input { border: none; background: transparent; padding: 6px; margin-left: 4px; color: black; width: 100%; outline: none; }
        .profile { display: flex; align-items: center; gap: 15px; white-space: nowrap; }
        .content { padding: 20px; width: 100%; }
        .content h1 { margin-top: 0; font-size: 24px; }
        .cards { display: grid; grid-template-columns: 1fr; gap: 15px; margin-top: 20px; }
        @media (min-width: 576px) { .cards { grid-template-columns: repeat(2, 1fr); } }
        .card { padding: 25px 20px; border-radius: 12px; color: white; text-align: center; transition: 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.15); min-height: 180px; display: flex; flex-direction: column; justify-content: center; }
        .card h3 { margin: 10px 0 5px 0; font-size: 16px; font-weight: 500; opacity: 0.9; }
        .card h2 { margin: 0; font-size: 28px; }
        .card:hover { transform: translateY(-5px); }
        .total { background: linear-gradient(165deg, #0f2027, #2c5364, #00c9a7); }
        .total:hover { box-shadow: 0 0 30px rgba(0, 128, 128, 0.6); cursor: pointer; }
        .pending { background: linear-gradient(135deg, #ff6a00, #ffdd00); }
        .pending:hover { box-shadow: 0 0 30px rgba(255, 128, 0, 0.6); cursor: pointer; }
        .ready { background: linear-gradient(135deg, #00eeff, #09408d); }
        .ready:hover { box-shadow: 0 0 30px rgba(0, 238, 255, 0.6); cursor: pointer; }
        .completed { background: linear-gradient(135deg, #00b09b, #96c93d); }
        .completed:hover { box-shadow: 0 0 30px rgba(1, 197, 138, 0.6); cursor: pointer; }
        .orders-box { margin-top: 30px; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table-responsive { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th, td { padding: 14px 12px; text-align: left; }
        tr { border-bottom: 1px solid #eee; }
        th { border-bottom: 2px solid #eee; font-weight: 600; color: #666; }

        /* Status Badges - Consolidated to laundry states only */
        .status-completed, .status-ready, .status-processing, .status-pending, .status-cancelled {
            padding: 5px 12px;
            border-radius: 20px;
            color: white;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            text-transform: capitalize;
        }
        .status-pending { background-color: #f59e0b; color: #fff; }
        .status-processing { background-color: #3b82f6; }
        .status-ready { background-color: #14b8a6; }
        .status-completed { background-color: #22c55e; }
        
        /* ADDED: High-End Professional Red Cancelled Badge Styling */
        .status-cancelled { background-color: #ef4444; color: #fff; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2); }

        @media (min-width: 1024px) {
            .dashboard-container { flex-direction: row; }
            .sidebar { position: fixed; top: 0; bottom: auto; left: 0; width: 110px; height: 100vh; box-shadow: 3px 0 12px rgba(0,0,0,.08); flex-direction: column; justify-content: flex-start; padding-top: 20px; }
            .menu { flex-direction: column; justify-content: flex-start; gap: 10px; height: 100%; }
            .logo { display: block; width: 60px; height: auto; margin-bottom: 30px; border-radius: 50%; }
            .menu a { flex: none; margin: 12px 0; width: 100%; font-size: 12px; }
            .main { margin-left: 110px; width: calc(100% - 110px); padding-bottom: 0; }
            .cards { grid-template-columns: repeat(4, 1fr); gap: 20px; }
        }
    </style>
</head>

<body>

<div class="dashboard-container">

    <div class="sidebar">
        <div class="menu">
            <img src="images/washwave-logo.jpg" class="logo" alt="Logo">
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

            <div class="profile">
                <i class="fas fa-bell"></i>
                <span>Admin</span>
            </div>
        </div>

        <div class="content">
            <h1>Dashboard</h1>

            <div class="cards">
                <div class="card total">
                    <i class="fas fa-clipboard-list fa-2x"></i>
                    <h3>Total Orders</h3>
                    <h2><?php echo $total_orders; ?></h2>
                </div>
                
                <div class="card pending">
                    <i class="fas fa-hourglass-half fa-2x"></i>
                    <h3>Pending Orders</h3>
                    <h2><?php echo $pending_orders; ?></h2>
                </div>
                
                <div class="card ready">
                    <i class="fas fa-clock fa-2x"></i>
                    <h3>Orders Ready</h3>
                    <h2><?php echo $ready_orders; ?></h2>
                </div>
            
                <div class="card completed">
                    <i class="fas fa-check-circle fa-2x"></i>
                    <h3>Completed Orders</h3>
                    <h2><?php echo $completed_orders; ?></h2>
                </div>
            </div>

            <div class="orders-box">
                <h1 style="font-size: 20px; margin-bottom: 15px;">Recent Orders</h1>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer Name</th>
                                <th>Service</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
                            while($row = mysqli_fetch_assoc($query)) {
                                $db_status = $row['status'];

                                // CLEANING ENGINE: Maps raw database payment tracking back down into standard clean laundry statuses
                                if ($db_status === 'Paid') {
                                    $display_status = 'Pending';
                                } else {
                                    $display_status = $db_status;
                                }
                            ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['service_type']); ?></td>
                                <td>
                                    <span class="status-<?php echo strtolower($display_status); ?>">
                                        <?php echo htmlspecialchars($display_status); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

</div>

</body>
</html>