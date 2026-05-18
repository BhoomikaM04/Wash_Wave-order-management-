<?php
include('../db.php'); // Ensure accurate path configuration to your database file
session_start();

// Check if management admin authorization credential parameters exist...
if (!isset($_SESSION['admin'])) { 
    header("Location: admin-login.php"); 
    exit(); 
}

// --- SQL METRICS QUERIES ---
// 1. Total Customers
$total_cust_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$total_cust_row = mysqli_fetch_assoc($total_cust_query);
$total_customers = $total_cust_row['total'];

// 2. Active Customers (Customers who have placed at least one order)
$active_cust_query = mysqli_query($conn, "SELECT COUNT(DISTINCT user_id) as active FROM orders");
$active_cust_row = mysqli_fetch_assoc($active_cust_query);
$active_customers = $active_cust_row['active'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registry - WashWave Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { background: #f8f9fa; font-family: Arial, sans-serif; margin: 0; color: #333; }
        
        /* Layout Structure matching Dashboard Layout */
        .dashboard-container { display: flex; min-height: 100vh; flex-direction: column; }
        .sidebar { position: fixed; bottom: 0; left: 0; width: 100%; height: 70px; background: #fff; box-shadow: 0 -3px 12px rgba(0,0,0,.12); z-index: 999; display: flex; align-items: center; justify-content: center; }
        .menu { display: flex; flex-direction: row; justify-content: space-around; align-items: center; width: 100%; padding: 0 10px; }
        .logo { display: none; }
        
        /* Sidebar Navigation Standard Links (RESTORED EXACTLY FROM DASHBOARD) */
        .menu a { text-decoration: none; display: flex; flex-direction: column; align-items: center; font-size: 11px; color: black; flex: 1; gap: 4px; }
        .menu a i { font-size: 20px; }
        .menu a:hover { color: rgb(106, 105, 105); }
        
        .main { width: 100%; min-height: 100vh; display: flex; flex-direction: column; padding-bottom: 80px; }
        
        /* Dynamic Standalone Header Area */
        .header { display: flex; align-items: center; padding: 35px 25px 20px 25px; background: transparent; gap: 15px; }
        .header h1 { font-size: 32px; font-weight: 500; color: #1e293b; margin: 0; letter-spacing: -0.5px; }
        .header i { color: #4f46e5; font-size: 28px; }

        .content { padding: 0 25px 25px 25px; width: 100%; }

        /* MATCHED TO DASHBOARD: Mini Cards Layout */
        .mini-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 15px; border-left: 4px solid #4f46e5; }
        .mini-card i { color: #4f46e5; background: #eeebff; padding: 12px; border-radius: 8px; }

        /* Corporate CRM Table Layout Canvas */
        .table-card { background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.04); padding: 25px; margin-top: 25px; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; }
        
        /* CHANGED: Custom styles to enforce the #212529 dark background across the entire header row */
        .custom-dark-header { background-color: #212529 !important; }
        .custom-dark-header th { padding: 14px 16px; font-weight: 600; color: #ffffff !important; background-color: #212529 !important; border-bottom: none; }
        
        td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 14px; }
        tr:hover td { background-color: #f8fafc; }
        
        .avatar-sub { width: 36px; height: 36px; background: #eeebff; color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; font-weight: 700; font-size: 13px; border: 1px solid #e0dbff; }
        .count-badge { background-color: #e0fbf7; color: #007764; padding: 5px 12px; border-radius: 6px; font-weight: 700; font-size: 12px; border: 1px solid #c1f6ed; display: inline-block; }

        @media (min-width: 1024px) {
            .dashboard-container { flex-direction: row; }
            .sidebar { position: fixed; top: 0; bottom: auto; left: 0; width: 110px; height: 100vh; box-shadow: 3px 0 12px rgba(0,0,0,.08); flex-direction: column; justify-content: flex-start; padding-top: 20px; background: #fff; }
            .menu { flex-direction: column; justify-content: flex-start; gap: 10px; height: 100%; }
            .logo { display: block; width: 60px; height: auto; margin-bottom: 30px; border-radius: 50%; }
            .menu a { flex: none; margin: 12px 0; width: 100%; font-size: 12px; }
            .main { margin-left: 110px; width: calc(100% - 110px); padding-bottom: 0; }
        }
    </style>
</head>
<body>

<div class="dashboard-container">

    <div class="sidebar">
        <div class="menu">
            <img src="images/washwave-logo.jpg" class="logo" alt="Logo">
            <a href="admin-dashboard.php"><i class="fas fa-th-large"></i>Dashboard</a>
            <a href="admin-customers.php"><i class="fas fa-users" style="color: rgb(106, 105, 105);"></i><span style="color: rgb(106, 105, 105);">Customers</span></a>
            <a href="admin-orders.php"><i class="fas fa-box"></i>Orders</a>
            <a href="admin-logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
        </div>
    </div>

    <div class="main">
        
        <div class="header">
            <h1><i class="fas fa-users me-2"></i>Customer Registry</h1>
        </div>

        <div class="content">
            
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="mini-card">
                        <i class="fas fa-users fa-2x"></i>
                        <div>
                            <h6 class="text-muted mb-1" style="font-size:13px;">Total Registered Users</h6>
                            <h4 class="fw-bold m-0"><?php echo $total_customers; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mini-card" style="border-left-color: #22c55e;">
                        <i class="fas fa-user-check fa-2x" style="color: #22c55e; background: #e6f9ed;"></i>
                        <div>
                            <h6 class="text-muted mb-1" style="font-size:13px;">Active Accounts</h6>
                            <h4 class="fw-bold m-0"><?php echo $active_customers; ?></h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="table align-middle m-0">
                        <thead>
                            <tr class="custom-dark-header">
                                <th style="width: 100px;">User ID</th>
                                <th>Client Name</th>
                                <th>Email Address</th>
                                <th>Contact Number</th>
                                <th class="text-center">Activity Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT u.*, COUNT(o.id) AS total_orders 
                                      FROM users u 
                                      LEFT JOIN orders o ON u.id = o.user_id 
                                      GROUP BY u.id 
                                      ORDER BY u.id DESC";
                            
                            $result = $conn->query($query);

                            if ($result && $result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    $initial = strtoupper(substr($row['name'] ?? 'U', 0, 1));
                                    ?>
                                    <tr>
                                        <td><span class="text-muted fw-bold">#USR-<?php echo $row['id']; ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-sub"><?php echo $initial; ?></div>
                                                <span class="fw-bold text-dark"><?php echo htmlspecialchars($row['name']); ?></span>
                                            </div>
                                        </td>
                                        <td class="text-secondary"><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td>
                                            <span class="text-secondary">
                                                <i class="fas fa-phone-alt me-1" style="font-size:11px; opacity:0.7;"></i> 
                                                <?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="count-badge">
                                                <i class="fas fa-history me-1" style="font-size:11px;"></i>
                                                <?php echo $row['total_orders']; ?> Orders Placed
                                            </span>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-5 text-muted fw-medium'>No user profiles registered inside the system database yet.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>