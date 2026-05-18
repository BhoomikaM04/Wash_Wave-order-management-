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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
/* ==========================================================================
   Base Styles & Resets (Dashboard Theme Alignment)
   ========================================================================== */
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f8f9fa;
    color: #333;
    overflow-x: hidden;
}

/* Layout Core Container */
.dashboard-container {
    display: flex;
    min-height: 100vh;
    flex-direction: column; /* Mobile/Tablet Default */
}

/* ==========================================================================
   Navigation Component (Identical to User Dashboard Mechanics)
   ========================================================================== */
.sidebar {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 70px;
    background: #fff;
    box-shadow: 0 -3px 12px rgba(0,0,0,.12);
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.menu {
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    align-items: center;
    width: 100%;
    padding: 0 10px;
}

.logo {
    display: none; /* Hidden on mobile/tablet */
}

.menu a {
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 11px;
    color: black;
    flex: 1;
    gap: 4px;
    transition: 0.2s ease;
}

.menu a i {
    font-size: 20px;
}

.menu a:hover, 
.menu .active-link {
    color: rgb(106, 105, 105) !important;
}

/* ==========================================================================
   Main Workspace Frame Layout
   ========================================================================== */
.main {
    width: 100%;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding-bottom: 90px; /* Leave clean space for bottom nav bar on mobile */
}

/* Page Header Panel Box Placement */
.welcome-box h1 {
    font-size: 38px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 8px;
}

/* Base Content Workspace Area */
.content {
    padding: 30px 20px;
    width: 100%;
}

/* ==========================================================================
   Professional Checkout & Summary Elements Style Configuration
   ========================================================================== */
.summary-box {
    padding: 24px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 1px solid #eee;
}

.cart-header {
    font-size: 18px;
    font-weight: 700;
    color: #444;
    border-bottom: 2px solid #eee;
    padding-bottom: 10px;
}

.item-card {
    border: none;
    border-bottom: 1px solid #f1f3f6;
    padding: 14px 0;
    background: transparent;
}

.item-card h5 {
    font-size: 16px;
    font-weight: 600;
    color: #2c3e50;
}

.item-card p {
    font-size: 14px;
    color: #777;
}

/* Premium Slate Total Box Custom styling replacing bright blue */
.total-box {
    background: #2c3e50;
    color: white;
    border-radius: 10px;
    padding: 20px;
}

.total-box h5 {
    font-size: 16px;
    font-weight: 500;
    margin-bottom: 8px;
}

.total-box h5:last-child {
    margin-bottom: 0;
}

.total-box span {
    font-weight: 700;
}

/* Professional Input Elements Styles Adjustment */
.form-label {
    font-size: 15px;
    font-weight: 600;
    color: #444;
}

.form-control {
    border-radius: 8px;
    border: 1px solid #ced4da;
    font-size: 15px;
    padding: 12px;
}

.form-control:focus {
    border-color: rgb(106, 105, 105);
    box-shadow: 0 0 0 0.2rem rgba(106, 105, 105, 0.15);
}

/* Premium Dashboard Styled Button Adjustment replacing generic bright green */
.place-btn {
    width: 100%;
    background-color: #333;
    border: none;
    color: white;
    border-radius: 8px;
    padding: 14px;
    font-size: 18px;
    font-weight: 600;
    transition: 0.2s;
}

.place-btn:hover {
    background-color: rgb(106, 105, 105);
}

/* ==========================================================================
   Desktop View Overrides (Transitions exactly at 1024px like your Dashboard)
   ========================================================================== */
@media (min-width: 1024px) {
    .dashboard-container {
        flex-direction: row; /* Layout switches side-by-side */
    }

    /* Left-anchored vertical navigation setup blueprint */
    .sidebar {
        position: fixed;
        top: 0;
        bottom: auto;
        left: 0;
        width: 110px;
        height: 100vh;
        box-shadow: 3px 0 12px rgba(0,0,0,.08);
        flex-direction: column;
        justify-content: flex-start;
        padding-top: 20px;
        background: #fff;
    }

    .menu {
        flex-direction: column;
        justify-content: flex-start;
        gap: 10px;
        height: 100%;
    }

    .logo {
        display: block;
        width: 60px;
        height: auto;
        margin-bottom: 30px;
        border-radius: 50%;
    }

    .menu a {
        flex: none;
        margin: 12px 0;
        width: 100%;
        font-size: 12px;
    }

    /* Shifts contents clear from the permanent desktop side navigation element */
    .main {
        margin-left: 110px;
        width: calc(100% - 110px);
        padding-bottom: 0; 
    }
    
    .content {
        padding: 40px;
    }
}

@media(max-width: 768px) {
    .welcome-box h1 { font-size: 32px; }
}
</style>
</head>

<body>

<div class="dashboard-container">

    <div class="sidebar">
        <div class="menu">
            <img src="images/washwave-logo.jpg" class="logo" alt="Logo">
            <a href="user-dashboard.php" class="active-link"><i class="fas fa-th-large"></i>Dashboard</a>
            <a href="my-orders.php"><i class="fas fa-box"></i>My Orders</a>
            <a href="user-logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
        </div>
    </div>

    <div class="main">

        <div class="content">
            
            <div class="welcome-box mb-4 text-center text-md-start">
              <h1>📋 Order Summary</h1>
            </div>

            <div class="row g-4">
                
                <div class="col-12 col-xl-7">
                    <div class="summary-box">
                        <div class="cart-header mb-3">
                            <i class="bi bi-cart3 me-2"></i>Your Selected Items
                        </div>
                        <div id="itemsContainer"></div>

                        <div class="total-box my-4">
                            <h5 class="d-flex justify-content-between">Total Clothes: <span id="totalClothes">0</span></h5>
                            <h5 class="d-flex justify-content-between mb-0">Total Price: <span>₹<span id="totalPrice">0</span></span></h5>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-5">
                    <div class="summary-box">
                        <div class="cart-header mb-4">
                            <i class="bi bi-geo-alt me-2"></i>Fulfillment Details
                        </div>
                        
                        <form action="payment-selection.php" method="POST" onsubmit="return prepareOrder()">
                            <input type="hidden" name="service_type" id="serviceTypeInput">
                            <input type="hidden" name="total_clothes" id="totalClothesInput">
                            <input type="hidden" name="total_price" id="totalPriceInput">
                            <input type="hidden" name="cloth_list" id="clothListInput">

                            <div class="mb-4">
                                <label class="form-label">Delivery Address</label>
                                <textarea class="form-control" name="address" rows="3" placeholder="Enter your full street address, apartment number, and pincode..." required></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Requested Delivery Date</label>
                                <input type="date" id="delivery_date" name="delivery_date" class="form-control" required>
                            </div>

                            <button class="place-btn mt-2" type="submit">Proceed</button>
                        </form>
                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
let orderData = JSON.parse(localStorage.getItem("washwave_order")) || [];
let totalClothes = localStorage.getItem("washwave_total_clothes") || 0;
let totalPrice = localStorage.getItem("washwave_total_price") || 0;
let serviceType = localStorage.getItem("washwave_service_type") || "Laundry";

let itemsContainer = document.getElementById("itemsContainer");
let clothList = "";

/* =========================
   RENDER ITEMS LIST ENGINE
========================= */
if(orderData.length === 0) {
    itemsContainer.innerHTML = `<p class="text-muted py-3 mb-0">Your cart is currently empty.</p>`;
} else {
    orderData.forEach(item => {
        clothList += `${item.name} x ${item.qty}, `;

        itemsContainer.innerHTML += `
            <div class="item-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">${item.name}</h5>
                        <p class="mb-0 text-muted">Quantity: ${item.qty}</p>
                    </div>
                    <h5 class="mb-0 text-dark fw-bold">₹${item.price * item.qty}</h5>
                </div>
            </div>
        `;
    });
}

/* =========================
   SHOW EXPORTED TOTAL CORES
========================= */
document.getElementById("totalClothes").innerText = totalClothes;
document.getElementById("totalPrice").innerText = totalPrice;

/* =========================
   DATE RESTRICTION PARSER
========================= */
window.onload = function () {
    const dateInput = document.getElementById("delivery_date");

    let today = new Date();
    today.setDate(today.getDate() + 1);

    let yyyy = today.getFullYear();
    let mm = String(today.getMonth() + 1).padStart(2, '0');
    let dd = String(today.getDate()).padStart(2, '0');

    dateInput.min = `${yyyy}-${mm}-${dd}`;
};

/* =========================
   SUBMIT ORDER CORE VALIDATION
========================= */
function prepareOrder() {
    if (orderData.length === 0) {
        alert("No items selected!");
        return false;
    }

    document.getElementById("serviceTypeInput").value = serviceType;
    document.getElementById("clothListInput").value = clothList.replace(/,\s*$/, ""); // Trims trailing comma safely
    document.getElementById("totalClothesInput").value = totalClothes;
    document.getElementById("totalPriceInput").value = totalPrice;

    return true;
}
</script>
</body>
</html>