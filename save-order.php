<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: USER/user-login.php");
    exit();
}

/* =========================
   SAFE POST DATA
========================= */
$user_id        = $_SESSION['user_id'];
$service_type   = $_POST['service_type'] ?? '';
$total_clothes  = $_POST['total_clothes'] ?? 0;
$total_price    = $_POST['total_price'] ?? 0;
$address        = $_POST['address'] ?? '';
$pickup_date    = $_POST['pickup_date'] ?? '';
$cloth_list     = $_POST['cloth_list'] ?? '';

/* =========================
   VALIDATION
========================= */
if (
    empty($service_type) ||
    empty($cloth_list) ||
    empty($address) ||
    empty($pickup_date)
) {
    die("Missing required fields (JS not sending data properly)");
}

/* =========================
   INSERT QUERY
========================= */
$sql = "INSERT INTO orders 
(user_id, service_type, cloth_list, total_clothes, total_price, address, pickup_date, status)
VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

/* =========================
   BIND PARAMETERS
========================= */
$stmt->bind_param(
    "issidss",
    $user_id,        // i
    $service_type,   // s
    $cloth_list,     // s
    $total_clothes,  // i
    $total_price,    // d
    $address,        // s
    $pickup_date     // s
);

/* =========================
   EXECUTE
========================= */
if ($stmt->execute()) {

    echo "<script>
        alert('Order placed successfully!');

        localStorage.removeItem('washwave_order');
        localStorage.removeItem('washwave_total_clothes');
        localStorage.removeItem('washwave_total_price');
        localStorage.removeItem('washwave_service_type');

        window.location.href='USER/user-dashboard.php';
    </script>";

} else {
    echo "Error: " . $stmt->error;
}
?>