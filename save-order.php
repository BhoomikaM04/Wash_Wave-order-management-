<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: USER/user-login.php");
    exit();
}

/* SAFE POST */
$service_type   = $_POST['service_type'] ?? '';
$total_clothes  = $_POST['total_clothes'] ?? 0;
$total_price    = $_POST['total_price'] ?? 0;
$address        = $_POST['address'] ?? '';
$pickup_date    = $_POST['pickup_date'] ?? '';
$user_id        = $_SESSION['user_id'];

/* DEBUG SAFE CHECK */
if (
    empty($service_type) ||
    empty($address) ||
    empty($pickup_date)
) {
    die("Missing required fields (JS not sending data properly)");
}

/* INSERT */
$sql = "INSERT INTO orders 
(user_id, service_type, total_clothes, total_price, address, pickup_date, status)
VALUES (?, ?, ?, ?, ?, ?, 'Pending')";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "isidss",
    $user_id,
    $service_type,
    $total_clothes,
    $total_price,
    $address,
    $pickup_date
);

if ($stmt->execute()) {

    echo "<script>
        alert('Order placed successfully!');

        localStorage.removeItem('washwave_order');
        localStorage.removeItem('washwave_total_clothes');
        localStorage.removeItem('washwave_total_price');

        window.location.href='USER/user-dashboard.php';
    </script>";

} else {
    echo "Error: " . $stmt->error;
}
?>