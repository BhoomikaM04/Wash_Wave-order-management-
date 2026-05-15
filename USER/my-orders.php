<?php
session_start();
include "../db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders</title>

<!-- Bootstrap CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
    background: #f4f6f9;
}

.container-box {
    margin-top: 40px;
}

.card-header {
    background: #2c3e50;
    color: white;
    font-weight: bold;
}

.status {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: white;
}

.Pending { background: orange; }
.Completed { background: green; }
.Cancelled { background: red; }

.table-responsive {
    border-radius: 10px;
    overflow: hidden;
}

.empty-box {
    text-align: center;
    padding: 40px;
    background: white;
    border-radius: 10px;
}
</style>
</head>

<body>

<div class="container container-box">

    <h2 class="text-center mb-4">📦 My Orders</h2>

    <?php if ($result->num_rows > 0): ?>

    <div class="card shadow">
        <div class="card-header">
            Order History
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Service</th>
                        <th>Clothes</th>
                        <th>Price</th>
                        <th>Address</th>
                        <th>Pickup Date</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['service_type']; ?></td>

                        <td>
                            <span class="badge bg-primary">
                                <?php echo $row['total_clothes']; ?>
                            </span>
                        </td>

                        <td>₹<?php echo $row['total_price']; ?></td>

                        <td><?php echo $row['address']; ?></td>

                        <td><?php echo $row['pickup_date']; ?></td>

                        <td>
                            <span class="status <?php echo $row['status']; ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>

                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php endwhile; ?>

                </tbody>
            </table>
        </div>
    </div>

    <?php else: ?>

        <div class="empty-box shadow">
            <h4>No Orders Found 😔</h4>
            <p>You haven’t placed any orders yet.</p>
        </div>

    <?php endif; ?>

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// small UX improvement
console.log("My Orders Page Loaded");
</script>

</body>
</html>