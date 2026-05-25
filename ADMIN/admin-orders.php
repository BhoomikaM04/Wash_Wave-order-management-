<?php
include('../db.php'); // Ensure accurate path configuration to your database file
session_start();

// Check if management admin authorization credential parameters exist...
if (!isset($_SESSION['admin'])) { 
    header("Location: admin-login.php"); 
    exit(); 
}

// --- 1. HANDLE LAUNDRY WORKFLOW STATUS LIFECYCLE UPDATES ---
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];
    
    // Updates only the laundry progress status field
    $status_update = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($status_update);
    $stmt->bind_param("si", $new_status, $order_id);
    if ($stmt->execute()) {
        header("Location: admin-orders.php?success=Laundry status updated successfully!");
        exit();
    }
}

// --- 2. ADMINISTRATIVE CASH RECEPTACLE VERIFICATION DISPATCHER ---
if (isset($_GET['action']) && $_GET['action'] == 'confirm_received' && isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    
    // Updates the billing status layer to Paid.
    $admin_update = "UPDATE orders SET status = 'Paid' WHERE id = ?";
    $stmt = $conn->prepare($admin_update);
    $stmt->bind_param("i", $order_id);
    
    if ($stmt->execute()) {
        header("Location: admin-orders.php?success=Payment marked as Paid in database!");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders Management Panel - WashWave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
      body {
    background: #f8f9fa;
    font-family: Arial, sans-serif;
}

/* Modernized Matching Laundry Status Badges */
.badge-status {
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 13px;
    text-transform: capitalize;
    display: inline-block;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-processing {
    background-color: #cff4fc;
    color: #055160;
}

.status-ready {
    background-color: #e0fbf7;
    color: #007764;
}

/* UPDATED: Remapped style matching from completed to delivered */
.status-delivered, .status-completed {
    background-color: #d1e7dd;
    color: #0f5132;
}

/* Professional Red Cancelled Badge Style matching User Side */
.status-cancelled {
    background-color: #ef4444;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2);
}

/* Separate Payment Status Badges */
.badge-payment {
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 700;
    font-size: 12px;
    display: inline-block;
}

.pay-paid {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.pay-unpaid {
    background-color: #e2e8f0;
    color: #64748b;
    border: 1px solid #cbd5e1;
    font-family: monospace;
}

/* Professional Payment Mode Badges */
.pm-badge {
    font-weight: 700;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.pm-upi {
    color: #0d6efd;
}

.pm-cod {
    color: #4f46e5;
}

.form-select-sm {
    font-size: 13px;
    border-radius: 6px;
}
    </style>
</head>
<body>

<div class="container-fluid px-4 py-5">
    <div class="card border-0 shadow-sm p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark m-0"><i class="fas fa-tasks me-2"></i> WashWave Master Order Dispatch Registry</h4>
            <a href="admin-dashboard.php" class="btn btn-dark btn-sm">Dashboard Overview</a>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success py-2"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle border">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Service Layout</th>
                        <th>Garments Summary</th>
                        <th>Total Price</th>
                        <th>Expected Delivery</th>
                        <th>Laundry Progress</th>
                        <th>Modify Laundry Status</th>
                        <th class="text-center">Cash Actions</th>
                        <th>Payment Mode</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Displaying active orders naturally present in your system database
                    $query = "SELECT * FROM orders ORDER BY created_at DESC";
                    $result = $conn->query($query);

                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $db_status = $row['status']; 
                            $payment_method = $row['payment_method'];

                            // --- RESOLVE THE SPLIT BETWEEN LAUNDRY STATUS & PAYMENT STATUS ---
                            if ($payment_method === 'UPI' || $db_status === 'Paid') {
                                $payment_status = 'Paid';
                            } else {
                                $payment_status = 'Unpaid';
                            }

                            // Determine Laundry Progress Status safely and swap Completed -> Delivered
                            if ($db_status === 'Paid') {
                                $laundry_status = 'Pending'; 
                            } elseif ($db_status === 'Completed') {
                                $laundry_status = 'Delivered';
                            } else {
                                $laundry_status = $db_status;
                            }
                            ?>
                            <tr>
                                <td><strong>#WS-<?php echo $row['id']; ?></strong></td>
                                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                
                                <td><span class='badge bg-secondary text-capitalize'><?php echo htmlspecialchars($row['service_type']); ?></span></td>
                                
                                <td><small><?php echo htmlspecialchars($row['cloth_list']); ?> (<?php echo $row['total_clothes']; ?> Pcs)</small></td>
                                <td><strong class='text-primary'>₹<?php echo htmlspecialchars($row['total_price']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['delivery_date']); ?></td>

                                <td>
                                    <span class="badge-status status-<?php echo strtolower($laundry_status); ?>">
                                        <?php echo htmlspecialchars($laundry_status); ?>
                                    </span>
                                </td>

                                <td>
                                    <form action="admin-orders.php" method="POST" class="d-flex gap-1 align-items-center">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <select name="status" class="form-select form-select-sm" style="width: 120px;">
                                            <?php
                                            // CHANGED: Replaced 'Completed' option string array component with 'Delivered'
                                            $statuses = ['Pending', 'Processing', 'Ready', 'Delivered', 'Cancelled'];
                                            foreach($statuses as $st) {
                                                // Ensure historical database evaluations find selection match cleanly
                                                $selected = ($laundry_status == $st || ($st == 'Delivered' && $laundry_status == 'Completed')) ? 'selected' : '';
                                                echo "<option value='{$st}' {$selected}>{$st}</option>";
                                            }
                                            ?>
                                        </select>
                                        <button type="submit" name="update_status" class="btn btn-sm btn-dark px-2 py-1" style="font-size:11px;">Update</button>
                                    </form>
                                </td>

                                <td class="text-center">
                                    <?php if ($payment_status === 'Paid'): ?>
                                        <span class="text-success small fw-bold"><i class="fas fa-check-circle me-1"></i> Received</span>
                                    <?php else: ?>
                                        <a href="admin-orders.php?action=confirm_received&order_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-success fw-bold py-1 px-2" style="font-size:12px;">Confirm Cash Received</a>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($payment_method === 'UPI'): ?>
                                        <span class="pm-badge pm-upi"><i class="fas fa-mobile-alt"></i> UPI</span>
                                    <?php else: ?>
                                        <span class="pm-badge pm-cod"><i class="fas fa-hand-holding-usd"></i> COD</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($payment_status === 'Paid'): ?>
                                        <span class="badge-payment pay-paid"><i class="fas fa-check me-1"></i> Paid</span>
                                    <?php else: ?>
                                        <span class="badge-payment pay-unpaid">NULL</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='11' class='text-center py-4 text-muted'>No transaction entries recorded yet in system registry.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>