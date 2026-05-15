<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Order Summary</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
body{
    background:#f4f6f9;
    font-family:Arial;
}

/* SIDEBAR */
.sidebar{
  height:100vh;
  background:#2c3e50;
  color:white;
  padding:24px 18px;
  position:fixed;
  left:0;
  top:0;
  width:240px;
  overflow-y:auto;
  z-index:1000;
}

.sidebar .nav-link{
  color:#dcdcdc;
  margin-bottom:12px;
  border-radius:12px;
  padding:12px 14px;
  display:flex;
  align-items:center;
  font-size:16px;
}

.sidebar .nav-link:hover{
  background:#3f5c78;
  color:white;
}

.active-link{
  background:#0d6efd;
  color:white !important;
}

/* MAIN */
.main{
  margin-left:240px;
  padding:30px;
}

.summary-container{
    width:100%;
    padding:20px;
}

.item-card{
    border:1px solid #e5e5e5;
    border-radius:14px;
    padding:15px;
    margin-bottom:15px;
    background:#fafafa;
}

.total-box{
    background:#0d6efd;
    color:white;
    border-radius:18px;
    padding:20px;
}

.place-btn{
    width:100%;
    padding:14px;
    font-size:18px;
    border:none;
    border-radius:14px;
    background:#198754;
    color:white;
}

/* =========================
   ✅ FIXED BOTTOM NAV
========================= */

.bottom-nav{
  background:#2c3e50;
  padding:10px 0;
  position:fixed;
  bottom:0;
  left:0;
  width:100%;
  display:flex;
  justify-content:space-around;
  align-items:center;
  z-index:2000;
}

.bottom-nav .nav-item{
  color:white;
  text-decoration:none;
  display:flex;
  flex-direction:column;
  align-items:center;
  font-size:12px;
}

.bottom-nav .nav-item i{
  font-size:22px;
  margin-bottom:3px;
}

/* MOBILE */
@media(max-width:991px){
  .sidebar{ display:none; }
  .main{ margin-left:0; padding:20px; padding-bottom:90px; }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<nav class="sidebar d-none d-lg-block">
  <h4 class="mb-4 text-white">WashWave</h4>

  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link active-link" href="user-dashboard.php">
        <i class="bi bi-house-door-fill me-2"></i> Home
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="my-orders.php">
        <i class="bi bi-bag-check-fill me-2"></i> My Orders
      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link" href="user-logout.php">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
      </a>
    </li>
  </ul>
</nav>

<!-- MAIN -->
<div class="main">
<div class="summary-container">

    <h1 class="mb-3 fw-bold">Order Summary</h1>

    <h4>Your Cart Items</h4>
    <div id="itemsContainer"></div>

    <div class="total-box my-4">
        <h5>Total Clothes: <span id="totalClothes">0</span></h5>
        <h5>Total Price: ₹<span id="totalPrice">0</span></h5>
    </div>

    <form action="../save-order.php" method="POST" onsubmit="return prepareOrder()">

       <input type="hidden" name="service_type" id="serviceTypeInput">
<input type="hidden" name="total_clothes" id="totalClothesInput">
<input type="hidden" name="total_price" id="totalPriceInput">
<input type="hidden" name="cloth_list" id="clothListInput">

        <div class="mb-3">
            <label class="fs-5">Address</label>
            <textarea class="form-control" name="address" required></textarea>
        </div>

        <div class="mb-3 col-lg-4">
            <label class="fs-5">Pickup Date</label>
            <input type="date" id="pickup_date" name="pickup_date" class="form-control" required>
        </div>

        <button class="place-btn" type="submit">Place Order</button>

    </form>

</div>
</div>

<!-- BOTTOM NAV (FIXED) -->
<nav class="bottom-nav d-lg-none">

  <a href="user-dashboard.php" class="nav-item">
    <i class="bi bi-house-door-fill"></i>
    <span>Home</span>
  </a>

  <a href="my-orders.php" class="nav-item">
    <i class="bi bi-bag-check-fill"></i>
    <span>Orders</span>
  </a>

  <a href="user-logout.php" class="nav-item">
    <i class="bi bi-box-arrow-right"></i>
    <span>Logout</span>
  </a>

</nav>

<script>

let orderData = JSON.parse(localStorage.getItem("washwave_order")) || [];
let totalClothes = localStorage.getItem("washwave_total_clothes") || 0;
let totalPrice = localStorage.getItem("washwave_total_price") || 0;

/* 👇 THIS IS YOUR SERVICE TYPE (IMPORTANT FIX) */
let serviceType = localStorage.getItem("washwave_service_type") || "Laundry";

let itemsContainer = document.getElementById("itemsContainer");

/* CLOTH LIST STRING */
let clothList = "";

/* =========================
   RENDER ITEMS
========================= */
orderData.forEach(item => {

    // 👇 cloth list only
    clothList += `${item.name} x ${item.qty}, `;

    itemsContainer.innerHTML += `
        <div class="item-card">
            <div class="d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="mb-1">${item.name}</h5>
                    <p class="mb-0 text-muted">Qty: ${item.qty}</p>
                </div>

                <h5>₹${item.price * item.qty}</h5>
            </div>
        </div>
    `;
});

/* =========================
   SHOW TOTALS
========================= */
document.getElementById("totalClothes").innerText = totalClothes;
document.getElementById("totalPrice").innerText = totalPrice;

/* =========================
   DATE RESTRICTION
========================= */
window.onload = function () {

    const dateInput = document.getElementById("pickup_date");

    let today = new Date();
    today.setDate(today.getDate() + 1);

    let yyyy = today.getFullYear();
    let mm = String(today.getMonth() + 1).padStart(2, '0');
    let dd = String(today.getDate()).padStart(2, '0');

    dateInput.min = `${yyyy}-${mm}-${dd}`;
};

/* =========================
   SUBMIT ORDER
========================= */
function prepareOrder() {

    if (orderData.length === 0) {
        alert("No items selected!");
        return false;
    }

    /* SERVICE TYPE (laundry / ironing etc.) */
    document.getElementById("serviceTypeInput").value = serviceType;

    /* CLOTH LIST (shirt x1, jeans x2 etc.) */
    document.getElementById("clothListInput").value = clothList;

    /* TOTALS */
    document.getElementById("totalClothesInput").value = totalClothes;
    document.getElementById("totalPriceInput").value = totalPrice;

    return true;
}


</script>

</body>
</html>