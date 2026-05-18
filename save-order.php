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
$user_name      = $_SESSION['user_name'];
$service_type   = $_POST['service_type'] ?? '';
$total_clothes  = $_POST['total_clothes'] ?? 0;
$total_price    = $_POST['total_price'] ?? 0;
$address        = $_POST['address'] ?? '';
$delivery_date  = $_POST['delivery_date'] ?? '';
$cloth_list     = $_POST['cloth_list'] ?? '';

// Capture the payment method coming from your form selection ('POD' or 'UPI')
$payment_method = $_POST['payment_method'] ?? 'POD';

// Dynamic Payment Status Check:
// If payment method is UPI, status becomes 'Paid'. If POD, it remains 'Pending'.
$status         = ($payment_method === 'UPI') ? 'Paid' : 'Pending';

/* =========================
   VALIDATION
========================= */
if (
    empty($service_type) ||
    empty($cloth_list) ||
    empty($address) ||
    empty($delivery_date)
) {
    die("Missing required fields (JS not sending data properly)");
}

/* =========================
   INSERT QUERY (Including payment_method)
========================= */
$sql = "INSERT INTO orders 
(user_id, user_name, service_type, cloth_list, total_clothes, total_price, address, delivery_date, payment_method, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

/* =========================
   BIND PARAMETERS (Added two 's' fields at the end)
========================= */
$stmt->bind_param(
    "isssidssss",
    $user_id,        // i
    $user_name,      // s
    $service_type,   // s
    $cloth_list,     // s
    $total_clothes,  // i
    $total_price,    // d
    $address,        // s
    $delivery_date,  // s
    $payment_method, // s (New)
    $status          // s (New)
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

        window.location.href='USER/my-orders.php';
    </script>";

} else {
    echo "Error: " . $stmt->error;
}
?>