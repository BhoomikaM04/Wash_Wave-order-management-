<?php
session_start();

// ✅ PROTECT PAGE
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
<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
/* ==========================================================================
   Base Styles & Resets
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

.dashboard-container {
    display: flex;
    min-height: 100vh;
    flex-direction: column; /* Mobile/Tablet base layout */
}

/* ==========================================================================
   Navigation Component & High-Clarity Branding Layout
   ========================================================================== */
.sidebar {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 75px; 
    background: #fff;
    box-shadow: 0 -3px 12px rgba(0,0,0,.12);
    z-index: 999999; /* Forces navbar buttons to be on the top layer across all devices */
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
    padding: 0 5px;
}

/* Large screen variant logo */
.logo {
    display: none; 
}

/* First Element Mobile Brand Box Container (Non-clickable standalone asset) */
.mobile-logo-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    flex: 1;
}

.mobile-logo-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    border: 1px solid #eee;
    
    /* Blurriness Patch: Uses high contrast edge rendering alongside smooth interpolation filtering */
    image-rendering: -webkit-optimize-contrast;
    image-rendering: auto;
}

.brand-label {
    font-size: 9px;
    font-weight: bold;
    color: #333;
    margin-top: 2px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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
    cursor: pointer;
}

.menu a i {
    font-size: 20px;
}

.menu a:hover, 
.menu .active-link {
    color: rgb(106, 105, 105) !important;
}

/* ==========================================================================
   Main Content Structure Setup
   ========================================================================== */
.main {
    width: 100%;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding-bottom: 95px; /* Leaves clear space for mobile sticky bar layout rules */
}

.welcome-box h1 {
    font-size: 38px;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 8px;
}

.welcome-box h5 {
    font-size: 1.25rem;
    font-weight: normal;
}

.content {
    padding: 30px 20px;
    width: 100%;
}

.form-select {
    border-radius: 8px;
    border: 1px solid #ced4da;
    background-color: white;
    color: #333;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    min-height: 54px;
    font-size: 16px;
    font-weight: 500;
}

.form-select:focus {
    background-color: white;
    color: #333;
    border-color: rgb(106, 105, 105);
    outline: none;
    box-shadow: 0 0 0 0.2rem rgba(106, 105, 105, 0.15);
}

/* ==========================================================================
   Laundry Choice Cards Configuration
   ========================================================================== */
.laundry-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: 0.3s ease;
    height: 100%;
    border: 1px solid #eee;
}

.laundry-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.item-icon {
    width: 75px;
    height: 75px;
    object-fit: contain;
}

.laundry-card h5 {
    color: #333;
    margin-top: 14px;
    font-size: 18px;
    font-weight: 700;
}

.price {
    color: #6c757d;
    margin-bottom: 16px;
    font-size: 16px;
    font-weight: 600;
}

.qty-box {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 14px;
}

.qty-box button {
    width: 36px;
    height: 36px;
    border: 1px solid #ccc;
    border-radius: 50%;
    background: #fff;
    color: #333;
    font-size: 18px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: 0.2s;
}

.qty-box button:hover {
    background: #f1f3f6;
    color: black;
}

.qty-box span {
    font-size: 18px;
    font-weight: 700;
    min-width: 25px;
    text-align: center;
}

.proceed-btn {
    background-color: #333;
    border-color: #333;
    color: white;
    border-radius: 8px;
    padding: 12px 40px;
    font-size: 18px;
    font-weight: 600;
    transition: 0.2s;
}

.proceed-btn:hover {
    background-color: rgb(106, 105, 105);
    border-color: rgb(106, 105, 105);
    color: white;
}

/* ==========================================================================
   Desktop Responsive Breakpoints (Targets viewports larger than 768px/Tablets)
   ========================================================================== */
@media (min-width: 769px) {
    .dashboard-container {
        flex-direction: row; 
    }

    .sidebar {
        position: fixed;
        top: 0;
        bottom: auto;
        left: 0;
        width: 120px; 
        height: 100vh;
        box-shadow: 3px 0 12px rgba(0,0,0,.08);
        flex-direction: column;
        justify-content: flex-start;
        padding-top: 25px;
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
        width: 85px;
        height: 85px;
        margin-bottom: 25px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #eee;
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        image-rendering: -webkit-optimize-contrast;
        image-rendering: auto;
    }

    .mobile-logo-container {
        display: none !important;
    }

    .menu a {
        flex: none;
        margin: 12px 0;
        width: 100%;
        font-size: 12px;
    }

    .main {
        margin-left: 120px;
        width: calc(100% - 120px);
        padding-bottom: 0; 
    }
    
    .content {
        padding: 40px;
    }
}

@media(max-width: 768px) {
    .welcome-box h1 { 
      font-size: 32px; 
    }
}
</style>
</head>

<body>

<div class="dashboard-container">

    <div class="sidebar">
        <div class="menu">
            <img src="HOME-Images/wash_wave-logo.jpg" class="logo" alt="Logo">
            
            <div class="mobile-logo-container">
                <img src="HOME-Images/wash_wave-logo.jpg" class="mobile-logo-icon" alt="Brand Logo">
                <span class="brand-label">WashWave</span>
            </div>
            
            <a href="user-dashboard.php" class="active-link">
                <i class="fas fa-th-large"></i>
                Dashboard
            </a>
            
            <a href="my-orders.php">
                <i class="fas fa-box"></i>
                My Orders
            </a>
            
            <a href="./user-logout.php">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>

    <div class="main">
        <div class="content">
            
            <div class="welcome-box mb-4 text-center text-md-start">
              <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?> 👋</h1>
              <h5 class="text-muted">Ready to get your laundry done today?</h5>
            </div>
            
            <div class="row g-3 mb-4">
              <div class="col-12 col-md-6">
                <select class="form-select" id="categorySelect">
                  <option value="clothes" selected>👕 Clothes</option>
                  <option value="bedding">🛏️ Bulky Bedding</option>
                  <option value="carpets">🧼 Carpets</option>
                  <option value="shoes">👟 Shoes</option>
                </select>
              </div>

              <div class="col-12 col-md-6">
                <select class="form-select" id="serviceSelect">
                  <option value="laundry" selected>👕 Regular Laundry</option>
                  <option value="dryclean">🧼 Dry Cleaning</option>
                  <option value="ironing">👔 Ironing</option>
                  <option value="wash-fold">🧺 Wash & Fold</option>
                </select>
              </div>
            </div> 

            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4" id="itemsContainer">

              <div class="col item" data-category="clothes" data-service="laundry,ironing,wash-fold">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/shirt.png" class="item-icon" alt="Shirt">
                  <h5>Shirt</h5>
                  <p class="price">₹20</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('shirt',-1)">-</button>
                    <span id="shirt">0</span>
                    <button type="button" onclick="changeQty('shirt',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry,wash-fold">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/t-shirt.png" class="item-icon" alt="T-Shirt">
                  <h5>T-Shirt</h5>
                  <p class="price">₹15</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('tshirt',-1)">-</button>
                    <span id="tshirt">0</span>
                    <button type="button" onclick="changeQty('tshirt',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry,wash-fold">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/jeans.png" class="item-icon" alt="Jeans">
                  <h5>Jeans</h5>
                  <p class="price">₹30</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('jeans',-1)">-</button>
                    <span id="jeans">0</span>
                    <button type="button" onclick="changeQty('jeans',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry,ironing">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/trousers.png" class="item-icon" alt="Trousers">
                  <h5>Trousers</h5>
                  <p class="price">₹20</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('trousers',-1)">-</button>
                    <span id="trousers">0</span>
                    <button type="button" onclick="changeQty('trousers',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry,ironing">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/formal-pant.png" class="item-icon" alt="Formal Pants">
                  <h5>Formal Pants</h5>
                  <p class="price">₹30</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('formalpants',-1)">-</button>
                    <span id="formalpants">0</span>
                    <button type="button" onclick="changeQty('formalpants',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry,wash-fold">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/hoodie.png" class="item-icon" alt="Hoodie">
                  <h5>Hoodie</h5>
                  <p class="price">₹25</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('hoodie',-1)">-</button>
                    <span id="hoodie">0</span>
                    <button type="button" onclick="changeQty('hoodie',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry,wash-fold,ironing">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/top.png" class="item-icon" alt="Top">
                  <h5>Top</h5>
                  <p class="price">₹20</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('top',-1)">-</button>
                    <span id="top">0</span>
                    <button type="button" onclick="changeQty('top',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry,wash-fold,ironing">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/kurta.png" class="item-icon" alt="Kurti">
                  <h5>Kurti</h5>
                  <p class="price">₹25</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('kurti',-1)">-</button>
                    <span id="kurti">0</span>
                    <button type="button" onclick="changeQty('kurti',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/skirt.png" class="item-icon" alt="Skirt">
                  <h5>Skirt</h5>
                  <p class="price">₹20</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('skirt',-1)">-</button>
                    <span id="skirt">0</span>
                    <button type="button" onclick="changeQty('skirt',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry,wash-fold">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/track.png" class="item-icon" alt="Track Suit">
                  <h5>Track Suit</h5>
                  <p class="price">₹40</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('tracksuit',-1)">-</button>
                    <span id="tracksuit">0</span>
                    <button type="button" onclick="changeQty('tracksuit',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry,wash-fold">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/sweater.png" class="item-icon" alt="Sweater">
                  <h5>Sweater</h5>
                  <p class="price">₹30</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('sweater',-1)">-</button>
                    <span id="sweater">0</span>
                    <button type="button" onclick="changeQty('sweater',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry,ironing">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/uniform.png" class="item-icon" alt="Uniform">
                  <h5>Uniform</h5>
                  <p class="price">₹30</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('uniform',-1)">-</button>
                    <span id="uniform">0</span>
                    <button type="button" onclick="changeQty('uniform',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="laundry">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/towel.png" class="item-icon" alt="Towel">
                  <h5>Towel</h5>
                  <p class="price">₹30</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('towel',-1)">-</button>
                    <span id="towel">0</span>
                    <button type="button" onclick="changeQty('towel',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="dryclean,ironing">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/blazer.png" class="item-icon" alt="Blazer">
                  <h5>Blazer</h5>
                  <p class="price">₹50</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('blazer',-1)">-</button>
                    <span id="blazer">0</span>
                    <button type="button" onclick="changeQty('blazer',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="dryclean,ironing">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/suit.png" class="item-icon" alt="Suit">
                  <h5>Suit</h5>
                  <p class="price">₹60</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('suit',-1)">-</button>
                    <span id="suit">0</span>
                    <button type="button" onclick="changeQty('suit',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/denim-jacket.png" class="item-icon" alt="Jacket">
                  <h5>Jacket</h5>
                  <p class="price">₹40</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('jacket',-1)">-</button>
                    <span id="jacket">0</span>
                    <button type="button" onclick="changeQty('jacket',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="dryclean,ironing">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/saree.png" class="item-icon" alt="Saree">
                  <h5>Saree</h5>
                  <p class="price">₹50</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('saree',-1)">-</button>
                    <span id="saree">0</span>
                    <button type="button" onclick="changeQty('saree',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/gown.png" class="item-icon" alt="Gown">
                  <h5>Gown</h5>
                  <p class="price">₹60</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('gown',-1)">-</button>
                    <span id="gown">0</span>
                    <button type="button" onclick="changeQty('gown',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/lehenga.png" class="item-icon" alt="Lehenga">
                  <h5>Lehenga</h5>
                  <p class="price">₹70</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('lehenga',-1)">-</button>
                    <span id="lehenga">0</span>
                    <button type="button" onclick="changeQty('lehenga',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="clothes" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/blouse.png" class="item-icon" alt="Blouse">
                  <h5>Blouse</h5>
                  <p class="price">₹30</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('blouse',-1)">-</button>
                    <span id="blouse">0</span>
                    <button type="button" onclick="changeQty('blouse',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="bedding" data-service="laundry,wash-fold">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/bed-sheet.png" class="item-icon" alt="Bedsheet">
                  <h5>Bedsheet</h5>
                  <p class="price">₹60</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('bedsheet',-1)">-</button>
                    <span id="bedsheet">0</span>
                    <button type="button" onclick="changeQty('bedsheet',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="bedding" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/blanket.png" class="item-icon" alt="Blanket">
                  <h5>Blanket</h5>
                  <p class="price">₹80</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('blanket',-1)">-</button>
                    <span id="blanket">0</span>
                    <button type="button" onclick="changeQty('blanket',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="bedding" data-service="wash-fold">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/pillow.png" class="item-icon" alt="Pillow Cover">
                  <h5>Pillow Cover</h5>
                  <p class="price">₹25</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('pillow',-1)">-</button>
                    <span id="pillow">0</span>
                    <button type="button" onclick="changeQty('pillow',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="bedding" data-service="laundry">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/sofa.png" class="item-icon" alt="Sofa Cover">
                  <h5>Sofa Cover</h5>
                  <p class="price">₹25</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('sofacover',-1)">-</button>
                    <span id="sofacover">0</span>
                    <button type="button" onclick="changeQty('sofacover',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="bedding" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/comforter.png" class="item-icon" alt="Comforter">
                  <h5>Comforter</h5>
                  <p class="price">₹25</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('comforter',-1)">-</button>
                    <span id="comforter">0</span>
                    <button type="button" onclick="changeQty('comforter',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="carpets" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/small-carpet.png" class="item-icon" alt="Small Carpet">
                  <h5>Small Carpet</h5>
                  <p class="price">₹70</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('smallcarpet',-1)">-</button>
                    <span id="smallcarpet">0</span>
                    <button type="button" onclick="changeQty('smallcarpet',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="carpets" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/large-carpet.png" class="item-icon" alt="Large Carpet">
                  <h5>Large Carpet</h5>
                  <p class="price">₹120</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('largecarpet',-1)">-</button>
                    <span id="largecarpet">0</span>
                    <button type="button" onclick="changeQty('largecarpet',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="carpets" data-service="laundry">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/door-mat.png" class="item-icon" alt="Door Mat">
                  <h5>Door Mat</h5>
                  <p class="price">₹120</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('doormat',-1)">-</button>
                    <span id="doormat">0</span>
                    <button type="button" onclick="changeQty('doormat',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="carpets" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/rug.png" class="item-icon" alt="Floor Rug">
                  <h5>Floor Rug</h5>
                  <p class="price">₹120</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('floorrug',-1)">-</button>
                    <span id="floorrug">0</span>
                    <button type="button" onclick="changeQty('floorrug',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="shoes" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/sneakers.png" class="item-icon" alt="Sneakers">
                  <h5>Sneakers</h5>
                  <p class="price">₹40</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('sneakers',-1)">-</button>
                    <span id="sneakers">0</span>
                    <button type="button" onclick="changeQty('sneakers',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="shoes" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/formal-shoe.png" class="item-icon" alt="Formal Shoes">
                  <h5>Formal Shoes</h5>
                  <p class="price">₹40</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('formalshoes',-1)">-</button>
                    <span id="formalshoes">0</span>
                    <button type="button" onclick="changeQty('formalshoes',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="shoes" data-service="dryclean">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/boots.png" class="item-icon" alt="Boots">
                  <h5>Boots</h5>
                  <p class="price">₹40</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('boots',-1)">-</button>
                    <span id="boots">0</span>
                    <button type="button" onclick="changeQty('boots',1)">+</button>
                  </div>
                </div>
              </div>

              <div class="col item" data-category="shoes" data-service="laundry">
                <div class="laundry-card text-center">
                  <img src="HOME-Images/slippers.png" class="item-icon" alt="Slippers">
                  <h5>Slippers</h5>
                  <p class="price">₹40</p>
                  <div class="qty-box">
                    <button type="button" onclick="changeQty('slippers',-1)">-</button>
                    <span id="slippers">0</span>
                    <button type="button" onclick="changeQty('slippers',1)">+</button>
                  </div>
                </div>
              </div>

            </div>

            <div class="d-flex justify-content-end mt-4">
              <button type="button" class="btn proceed-btn btn-lg" onclick="proceedOrder()">
              Proceed →
              </button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/* =========================
   FILTER ENGINE
========================= */
const categoryDropdown = document.getElementById("categorySelect");
const serviceDropdown = document.getElementById("serviceSelect");
const cards = document.querySelectorAll(".item");

filterCards();

categoryDropdown.addEventListener("change", filterCards);
serviceDropdown.addEventListener("change", () => {
    filterCards();
    updateUiCounters();
});

function filterCards() {
  const selectedCategory = categoryDropdown.value;
  const selectedService = serviceDropdown.value;

  cards.forEach(card => {
    const cardCategory = card.getAttribute("data-category");
    const cardServices = card
      .getAttribute("data-service")
      .split(",")
      .map(s => s.trim());

    if (
      cardCategory === selectedCategory &&
      cardServices.includes(selectedService)
    ) {
      card.style.display = "block";
    } else {
      card.style.display = "none";
    }
  });
}

/* ==========================================================================
   MULTI-SERVICE QUANTITY SYSTEM
   ========================================================================== */
const multiQuantities = {};

function changeQty(itemUniqueId, change) {
  const currentService = serviceDropdown.value;
  const stateKey = `${itemUniqueId}_${currentService}`;

  if (!multiQuantities[stateKey]) {
    multiQuantities[stateKey] = 0;
  }

  multiQuantities[stateKey] += change;

  if (multiQuantities[stateKey] < 0) {
    multiQuantities[stateKey] = 0;
  }

  document.getElementById(itemUniqueId).innerText = multiQuantities[stateKey];
}

function updateUiCounters() {
  const currentService = serviceDropdown.value;
  
  cards.forEach(card => {
    const span = card.querySelector(".qty-box span");
    const itemUniqueId = span.id;
    const stateKey = `${itemUniqueId}_${currentService}`;
    
    span.innerText = multiQuantities[stateKey] || 0;
  });
}

/* ==========================================================================
   PROCEED ORDER EXPORT WITH SWEETALERT2 POPUPS
   ========================================================================== */
function proceedOrder() {
    let selectedItems = [];
    let totalClothes = 0;
    let totalPrice = 0;
    let clothList = [];
    let uniqueServicesUsed = new Set(); 

    const serviceNames = {
        'laundry': 'Regular Laundry',
        'dryclean': 'Dry Cleaning',
        'ironing': 'Ironing',
        'wash-fold': 'Wash & Fold'
    };

    for (const [key, qty] of Object.entries(multiQuantities)) {
        if (qty > 0) {
            const underscoreIdx = key.lastIndexOf('_');
            const itemUniqueId = key.substring(0, underscoreIdx);
            const itemService = key.substring(underscoreIdx + 1);

            const targetSpan = document.getElementById(itemUniqueId);
            const parentCard = targetSpan.closest(".laundry-card");
            
            let itemName = parentCard.querySelector("h5").innerText;
            let itemPrice = parseInt(parentCard.querySelector(".price").innerText.replace("₹", ""));

            if (serviceNames[itemService]) {
                uniqueServicesUsed.add(serviceNames[itemService]);
            }

            selectedItems.push({
                name: itemName,
                price: itemPrice,
                qty: qty,
                service: serviceNames[itemService] || itemService
            });

            totalClothes += qty;
            totalPrice += itemPrice * qty;
            clothList.push(`${itemName} x ${qty}`);
        }
    }

    if (selectedItems.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Empty Basket!',
            text: 'Please select at least one garment or service item before moving forward.',
            confirmButtonColor: '#333333'
        });
        return;
    }

    let finalServiceTypeString = Array.from(uniqueServicesUsed).join(", ");

    localStorage.setItem("washwave_order", JSON.stringify(selectedItems));
    localStorage.setItem("washwave_total_clothes", totalClothes);
    localStorage.setItem("washwave_total_price", totalPrice);
    localStorage.setItem("washwave_cloth_list", clothList.join(", "));
    localStorage.setItem("washwave_service_type", finalServiceTypeString);

    Swal.fire({
        icon: 'success',
        title: 'Items Confirmed!',
        text: 'Generating your custom order summary breakdown...',
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
        didClose: () => {
            window.location.href = "order-summary.php";
        }
    });
}
</script>
</body>
</html>