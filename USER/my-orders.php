<?php
include('../db.php'); // Ensure accurate path configuration to root database file
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- PROCESS USER REQUEST TO COMPLETELY REMOVE/DELETE AN ACTIVE ORDER ---
if (isset($_GET['action']) && $_GET['action'] == 'cancel_order' && isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Hard delete from the system database so it vanishes completely for both user and admin panels
    $delete_query = "DELETE FROM orders WHERE id = ? AND user_id = ? AND (status = 'Pending' OR status = 'Paid')";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("ii", $order_id, $user_id);
    
    if($stmt->execute()) {
        header("Location: my-orders.php?msg=Order removed and cancelled successfully.");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - WashWave</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
       body {
    background: #f8f9fa;
    font-family: Arial, sans-serif;
}

.main-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    padding: 30px;
    margin-top: 40px;
}

/* MATCHED TO PREVIOUS CODES: Custom Dark Table Row Formatting Blueprints */
.custom-dark-header {
    background-color: #212529 !important;
}

.custom-dark-header th {
    padding: 14px 16px;
    font-weight: 600;
    color: #ffffff !important;
    background-color: #212529 !important;
    border-bottom: none;
    font-size: 14px;
}

/* MATCHED TO PREVIOUS CODES: Unified Action Button Style mapping */
.back-items-btn {
    display: inline-flex;
    align-items: center;
    text-decoration: none;
    background-color: #212529;
    color: #ffffff !important;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.2s ease-in-out;
    border: none;
}

.back-items-btn:hover {
    background-color: rgb(106, 105, 105);
    color: #ffffff !important;
}

/* Modernized Matching Lifecycle Badges */
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

/* CHANGED: Swapped completed naming convention background rule targets to delivered */
.status-delivered, .status-completed {
    background-color: #d1e7dd;
    color: #0f5132;
}

.status-cancelled {
    background-color: #ef4444;
    color: #ffffff;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.2);
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

/* Separate Payment Status Badges matching Admin Panel */
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
    </style>
</head>
<body>

<div class="container">
    <div class="main-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0"><i class="fas fa-box me-2"></i> My Laundry Orders</h3>
            <a href="user-dashboard.php" class="back-items-btn"><i class="fas fa-arrow-left me-2"></i> Back to Items</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success py-2"><?php echo htmlspecialchars($_GET['msg']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr class="custom-dark-header">
                        <th>Order ID</th>
                        <th>Service Type</th>
                        <th>Cloth Details List</th>
                        <th>Total Items</th>
                        <th>Price Amount</th>
                        <th>Payment Mode</th>
                        <th>Payment Status</th>
                        <th>Laundry Progress</th>
                        <th class="text-center">Order Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $db_status = $row['status'];
                            $payment_method = $row['payment_method'];

                            // --- RESOLVE THE SPLIT BETWEEN LAUNDRY STATUS & PAYMENT STATUS ---
                            if ($payment_method === 'UPI' || $db_status === 'Paid') {
                                $payment_status = 'Paid';
                            } else {
                                $payment_status = 'Unpaid';
                            }

                            // CHANGED: Filter out 'Paid' and switch 'Completed' to 'Delivered' for front-facing visualization
                            if ($db_status === 'Paid') {
                                $display_status = 'Pending';
                            } elseif ($db_status === 'Completed') {
                                $display_status = 'Delivered';
                            } else {
                                $display_status = $db_status;
                            }
                            ?>
                            <tr>
                                <td><strong>#WS-<?php echo $row['id']; ?></strong></td>
                                <td><span class='badge bg-secondary text-capitalize'><?php echo htmlspecialchars($row['service_type']); ?></span></td>
                                <td><small class='text-muted'><?php echo htmlspecialchars($row['cloth_list']); ?></small></td>
                                <td><?php echo htmlspecialchars($row['total_clothes']); ?> Pcs</td>
                                <td><strong class='text-primary'>₹<?php echo htmlspecialchars($row['total_price']); ?></strong></td>
                                
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

                                <td>
                                    <span class="badge-status status-<?php echo strtolower($display_status); ?>">
                                        <?php echo htmlspecialchars($display_status); ?>
                                    </span>
                                </td>

                                <td class='text-center'>
                                    <?php if ($display_status === 'Pending'): ?>
                                        <a href='my-orders.php?action=cancel_order&order_id=<?php echo $row['id']; ?>' 
                                           class='btn btn-sm btn-danger fw-bold px-3 py-1' 
                                           onclick="return confirm('Are you sure you want to cancel and permanently delete this order?');"
                                           style="font-size:12px; border-radius:6px;">
                                            <i class="fas fa-trash-alt me-1"></i> Cancel Order
                                        </a>
                                    <?php else: ?>
                                        <span class='text-muted' style='font-size:13px;'>In Progress / Locked</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center text-muted py-4'>No laundry history recorded yet. Place your first request!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>