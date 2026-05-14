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
<title>Dashboard</title>

<!-- ✅ Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{
  background:#f4f6f9;
  font-family:Arial, sans-serif;
  overflow-x:hidden;
}

/* =========================
   SIDEBAR
========================= */

.sidebar{
  height:100vh;
  background:#2c3e50;
  color:white;
  padding:24px;
  position:fixed;
  top:0;
  left:0;
  width:16.666667%;
  z-index:1000;
  overflow-y:auto;
}

.sidebar h4{
  font-weight:700;
  letter-spacing:1px;
  font-size:28px;
}

/* NAV LINKS */

.sidebar .nav-link{

  color:#ddd;

  margin-bottom:14px;

  border-radius:12px;

  padding:14px 16px;

  font-size:18px;

  display:flex;

  align-items:center;

  gap:12px;

  transition:0.3s;
}

.sidebar .nav-link i{

  font-size:22px;
}

.sidebar .nav-link:hover{

  background:#3f5c78;

  color:white;
}

/* ACTIVE LINK */

.sidebar .active-link{

  background:#0d6efd;

  color:white !important;
}

/* =========================
   MAIN CONTENT
========================= */

.main{

  margin-left:16.66%;

  padding:40px;
}

/* =========================
   WELCOME
========================= */

.welcome-box{

  margin-bottom:28px;
}

.welcome-box h1{

  font-size:42px;

  font-weight:700;
}

.welcome-box h5{

  font-size:22px;

  margin-top:10px;
}

/* =========================
   DROPDOWNS
========================= */

.form-select{

  border-radius:14px;

  border:none;

  box-shadow:0 2px 8px rgba(0,0,0,0.08);

  min-height:62px;

  font-size:20px;

  font-weight:500;
}

.form-select:focus{

  box-shadow:0 0 0 0.2rem rgba(13,110,253,.25);
}

/* =========================
   LAUNDRY CARD
========================= */

.laundry-card{

  background:white;

  border-radius:18px;

  padding:26px;

  box-shadow:0 4px 10px rgba(0,0,0,0.08);

  transition:0.3s;

  height:100%;
}

.laundry-card:hover{

  transform:translateY(-5px);

  box-shadow:0 8px 20px rgba(0,0,0,0.12);
}

/* ITEM IMAGE */

.item-icon{

  width:90px;

  height:90px;

  object-fit:contain;
}

/* ITEM NAME */

.laundry-card h5{

  margin-top:16px;

  font-size:24px;

  font-weight:700;
}

/* PRICE */

.price{

  color:#6c757d;

  margin-bottom:18px;

  font-size:20px;

  font-weight:500;
}

/* =========================
   QUANTITY BOX
========================= */

.qty-box{

  display:flex;

  justify-content:center;

  align-items:center;

  gap:18px;
}

.qty-box button{

  width:46px;

  height:46px;

  border:none;

  border-radius:50%;

  background:#0d6efd;

  color:white;

  font-size:24px;

  font-weight:bold;

  transition:0.2s;
}

.qty-box button:hover{

  transform:scale(1.08);

  background:#0b5ed7;
}

.qty-box span{

  font-size:26px;

  font-weight:700;

  min-width:30px;

  text-align:center;
}

/* =========================
   PROCEED BUTTON
========================= */

.proceed-btn{

  border-radius:14px;

  padding:14px 38px;

  font-size:22px;

  font-weight:700;

  box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

.proceed-btn:hover{

  transform:translateY(-2px);
}

/* =========================
   MOBILE BOTTOM NAV
========================= */

.bottom-nav{

  background:#2c3e50;

  padding:14px 0;

  box-shadow:0 -2px 10px rgba(0,0,0,0.15);

  z-index:2000;
}

/* NAV ITEMS */

.bottom-nav .nav-item{

  color:white;

  text-decoration:none;

  display:flex;

  flex-direction:column;

  align-items:center;

  justify-content:center;

  font-size:16px;

  font-weight:600;

  transition:0.3s;
}

/* ICONS */

.bottom-nav .nav-item i{

  font-size:30px;

  margin-bottom:4px;
}

/* HOVER */

.bottom-nav .nav-item:hover{

  color:#0d6efd;
}

/* =========================
   TABLET
========================= */

@media(max-width:991px){

  .main{

    margin-left:0;

    padding:28px;

    padding-bottom:120px;
  }

  .welcome-box h1{

    font-size:38px;
  }

  .welcome-box h5{

    font-size:22px;
  }
}

/* =========================
   MOBILE
========================= */

@media(max-width:576px){

  .main{

    padding:20px;

    padding-bottom:130px;
  }

  .laundry-card{

    padding:24px;

    border-radius:18px;
  }

  .item-icon{

    width:95px;

    height:95px;
  }

  .laundry-card h5{

    font-size:26px;
  }

  .price{

    font-size:22px;
  }

  .qty-box{

    gap:20px;
  }

  .qty-box button{

    width:50px;

    height:50px;

    font-size:28px;
  }

  .qty-box span{

    font-size:28px;
  }

  .bottom-nav{

    padding:16px 0;
  }

  .bottom-nav .nav-item{

    font-size:16px;
  }

  .bottom-nav .nav-item i{

    font-size:32px;
  }

  .proceed-btn{

    width:100%;

    font-size:24px;

    padding:16px;
  }

  .welcome-box h1{

    font-size:34px;
  }

  .welcome-box h5{

    font-size:20px;
  }

  .form-select{

    min-height:64px;

    font-size:20px;
  }
}

</style>
<body>

<div class="container-fluid">
  <div class="row">

    <!-- Sidebar -->
<nav class="col-lg-2 d-none d-lg-block sidebar">

  <h4 class="mb-4">WashWave</h4>

  <ul class="nav flex-column">

    <li class="nav-item">
      <a class="nav-link active-link"
         href="user-dashboard.php">

        <i class="bi bi-house-door-fill"></i>
        Home

      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link"
         href="#">

        <i class="bi bi-bag-check-fill"></i>
        My Orders

      </a>
    </li>

    <li class="nav-item">
      <a class="nav-link"
         href="user-logout.php">

        <i class="bi bi-box-arrow-right"></i>
        Logout

      </a>
    </li>

  </ul>

</nav>
    <!-- Main Content -->
    <main class="col-lg-10  main">
      
      <!-- Welcome -->
      
<div class="welcome-box mb-4 text-center text-lg-start">
  <h1>Welcome, <?php echo $_SESSION['user_name']; ?> 👋</h1>
  <h5 class="text-muted">Ready to get your laundry done today?</h5>
</div>
<!-- Dropdown -->
<div class="row g-3 mb-3">

  <!-- CATEGORY -->
  <div class="col-12 col-md-6">
    <select class="form-select form-select-lg w-100" id="categorySelect">
      <option value="clothes" selected>👕 Clothes</option>
      <option value="bedding">🛏️ Bulky Bedding</option>
      <option value="carpets">🧼 Carpets</option>
      <option value="shoes">👟 Shoes</option>
    </select>
  </div>

  <!-- SERVICE -->
  <div class="col-12 col-md-6">
    <select class="form-select form-select-lg w-100" id="serviceSelect">
      <option value="laundry" selected> 👕Regular Laundry</option>
      <option value="dryclean">🧼 Dry Cleaning</option>
      <option value="ironing">👔 Ironing</option>
      <option value="wash-fold">🧺 Wash & Fold</option>
    </select>
  </div>

</div> 



  <!-- CLOTHES (6) -->
<!-- ITEMS CONTAINER -->
<div class="row g-4 mt-2" id="itemsContainer">

  <!-- ================= CLOTHES ================= -->
<!-- shirt -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry,ironing,wash-fold">
    <div class="laundry-card text-center">
      <img src="HOME-Images/shirt.png" class="item-icon">
      <h5>Shirt</h5>
      <p class="price">₹20</p>

      <div class="qty-box">
        <button onclick="changeQty('shirt',-1)">-</button>
        <span id="shirt">0</span>
        <button onclick="changeQty('shirt',1)">+</button>
      </div>
    </div>
  </div>

<!-- T-Shirt -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry,wash-fold">
    <div class="laundry-card text-center">
      <img src="HOME-Images/t-shirt.png" class="item-icon">
      <h5>T-Shirt</h5>
      <p class="price">₹15</p>

      <div class="qty-box">
        <button onclick="changeQty('tshirt',-1)">-</button>
        <span id="tshirt">0</span>
        <button onclick="changeQty('tshirt',1)">+</button>
      </div>
    </div>
  </div>

<!-- Jeans -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry,wash-fold">
    <div class="laundry-card text-center">
      <img src="HOME-Images/jeans.png" class="item-icon">
      <h5>Jeans</h5>
      <p class="price">₹30</p>

      <div class="qty-box">
        <button onclick="changeQty('jeans',-1)">-</button>
        <span id="jeans">0</span>
        <button onclick="changeQty('jeans',1)">+</button>
      </div>
    </div>
  </div>

<!-- trousers -->
<div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry,ironing">
    <div class="laundry-card text-center">
      <img src="HOME-Images/trousers.png" class="item-icon">
      <h5>Trousers</h5>
      <p class="price">₹20</p>

      <div class="qty-box">
        <button onclick="changeQty('trousers',-1)">-</button>
        <span id="trousers">0</span>
        <button onclick="changeQty('trousers',1)">+</button>
      </div>
    </div>
  </div>

  <!-- formal pants -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry,ironing">
    <div class="laundry-card text-center">
      <img src="HOME-Images/formal-pant.png" class="item-icon">
      <h5>Formal Pants</h5>
      <p class="price">₹30</p>

      <div class="qty-box">
        <button onclick="changeQty('formalpants',-1)">-</button>
        <span id="formalpants">0</span>
        <button onclick="changeQty('formalpants',1)">+</button>
      </div>
    </div>
  </div>

  <!-- Hoodie -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry,wash-fold">
    <div class="laundry-card text-center">
      <img src="HOME-Images/hoodie.png" class="item-icon">
      <h5>Hoodie</h5>
      <p class="price">₹25</p>

      <div class="qty-box">
        <button onclick="changeQty('hoodie',-1)">-</button>
        <span id="hoodie">0</span>
        <button onclick="changeQty('hoodie',1)">+</button>
      </div>
    </div>
  </div>

  <!-- top -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry,wash-fold, ironing">
    <div class="laundry-card text-center">
      <img src="HOME-Images/top.png" class="item-icon">
      <h5>Top</h5>
      <p class="price">₹20</p>

      <div class="qty-box">
        <button onclick="changeQty('top',-1)">-</button>
        <span id="top">0</span>
        <button onclick="changeQty('top',1)">+</button>
      </div>
    </div>
  </div>

  <!-- Kurti -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry,wash-fold, ironing">
    <div class="laundry-card text-center">
      <img src="HOME-Images/kurta.png" class="item-icon">
      <h5>Kurti</h5>
      <p class="price">₹25</p>

      <div class="qty-box">
        <button onclick="changeQty('kurti',-1)">-</button>
        <span id="kurti">0</span>
        <button onclick="changeQty('kurti',1)">+</button>
      </div>
    </div>
  </div>

  <!-- skirt -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry">
    <div class="laundry-card text-center">
      <img src="HOME-Images/skirt.png" class="item-icon">
      <h5>Skirt</h5>
      <p class="price">₹20</p>

      <div class="qty-box">
        <button onclick="changeQty('skirt',-1)">-</button>
        <span id="skirt">0</span>
        <button onclick="changeQty('skirt',1)">+</button>
      </div>
    </div>
  </div>

  <!-- track-suit -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry,wash-fold">
    <div class="laundry-card text-center">
      <img src="HOME-Images/track.png" class="item-icon">
      <h5>Track Suit</h5>
      <p class="price">₹40</p>

      <div class="qty-box">
        <button onclick="changeQty('tracksuit',-1)">-</button>
        <span id="tracksuit">0</span>
        <button onclick="changeQty('tracksuit',1)">+</button>
      </div>
    </div>
  </div>

  <!-- sweater -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry,wash-fold">
    <div class="laundry-card text-center">
      <img src="HOME-Images/sweater.png" class="item-icon">
      <h5>Sweater</h5>
      <p class="price">₹30</p>

      <div class="qty-box">
        <button onclick="changeQty('sweater',-1)">-</button>
        <span id="sweater">0</span>
        <button onclick="changeQty('sweater',1)">+</button>
      </div>
    </div>
  </div>

  <!-- uniform -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry,ironing">
    <div class="laundry-card text-center">
      <img src="HOME-Images/uniform.png" class="item-icon">
      <h5>Uniform</h5>
      <p class="price">₹30</p>

      <div class="qty-box">
        <button onclick="changeQty('uniform',-1)">-</button>
        <span id="uniform">0</span>
        <button onclick="changeQty('uniform',1)">+</button>
      </div>
    </div>
  </div>

   <!-- towel -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="laundry">
    <div class="laundry-card text-center">
      <img src="HOME-Images/towel.png" class="item-icon">
      <h5>Towel</h5>
      <p class="price">₹30</p>

      <div class="qty-box">
        <button onclick="changeQty('towel',-1)">-</button>
        <span id="towel">0</span>
        <button onclick="changeQty('towel',1)">+</button>
      </div>
    </div>
  </div>

  <!-- Blazer -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="dryclean,ironing">
    <div class="laundry-card text-center">
      <img src="HOME-Images/blazer.png" class="item-icon">
      <h5>Blazer</h5>
      <p class="price">₹50</p>

      <div class="qty-box">
        <button onclick="changeQty('blazer',-1)">-</button>
        <span id="blazer">0</span>
        <button onclick="changeQty('blazer',1)">+</button>
      </div>
    </div>
  </div>

  <!-- suit -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="dryclean,ironing">
    <div class="laundry-card text-center">
      <img src="HOME-Images/suit.png" class="item-icon">
      <h5>Suit</h5>
      <p class="price">₹60</p>

      <div class="qty-box">
        <button onclick="changeQty('suit',-1)">-</button>
        <span id="suit">0</span>
        <button onclick="changeQty('suit',1)">+</button>
      </div>
    </div>
  </div>

  <!-- jacket -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/denim-jacket.png" class="item-icon">
      <h5>Jacket</h5>
      <p class="price">₹40</p>

      <div class="qty-box">
        <button onclick="changeQty('jacket',-1)">-</button>
        <span id="jacket">0</span>
        <button onclick="changeQty('jacket',1)">+</button>
      </div>
    </div>
  </div>


    <!-- saree -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="dryclean,ironing">
    <div class="laundry-card text-center">
      <img src="HOME-Images/saree.png" class="item-icon">
      <h5>Saree</h5>
      <p class="price">₹50</p>

      <div class="qty-box">
        <button onclick="changeQty('saree',-1)">-</button>
        <span id="saree">0</span>
        <button onclick="changeQty('saree',1)">+</button>
      </div>
    </div>
  </div>

    <!-- gown -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes"data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/gown.png" class="item-icon">
      <h5>Gown</h5>
      <p class="price">₹60</p>

      <div class="qty-box">
        <button onclick="changeQty('gown',-1)">-</button>
        <span id="gown">0</span>
        <button onclick="changeQty('gown',1)">+</button>
      </div>
    </div>
  </div>

    <!-- lehenga -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/lehenga.png" class="item-icon">
      <h5>Lehenga</h5>
      <p class="price">₹70</p>

      <div class="qty-box">
        <button onclick="changeQty('lehenga',-1)">-</button>
        <span id="lehenga">0</span>
        <button onclick="changeQty('lehenga',1)">+</button>
      </div>
    </div>
  </div>

    <!-- blouse -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="clothes" data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/blouse.png" class="item-icon">
      <h5>Blouse</h5>
      <p class="price">₹30</p>

      <div class="qty-box">
        <button onclick="changeQty('blouse',-1)">-</button>
        <span id="blouse">0</span>
        <button onclick="changeQty('blouse',1)">+</button>
      </div>
    </div>
  </div>

  <!-- ================= BEDDING ================= -->

  <!-- bedsheet -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="bedding" data-service="laundry,wash-fold">
    <div class="laundry-card text-center">
      <img src="HOME-Images/bed-sheet.png" class="item-icon">
      <h5>Bedsheet</h5>
      <p class="price">₹60</p>

      <div class="qty-box">
        <button onclick="changeQty('bedsheet',-1)">-</button>
        <span id="bedsheet">0</span>
        <button onclick="changeQty('bedsheet',1)">+</button>
      </div>
    </div>
  </div>

  <!-- blanket -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="bedding" data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/blanket.png" class="item-icon">
      <h5>Blanket</h5>
      <p class="price">₹80</p>

      <div class="qty-box">
        <button onclick="changeQty('blanket',-1)">-</button>
        <span id="blanket">0</span>
        <button onclick="changeQty('blanket',1)">+</button>
      </div>
    </div>
  </div>

  <!-- pillow cover -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="bedding" data-service="wash-fold">
    <div class="laundry-card text-center">
      <img src="HOME-Images/pillow.png" class="item-icon">
      <h5>Pillow Cover</h5>
      <p class="price">₹25</p>

      <div class="qty-box">
        <button onclick="changeQty('pillow',-1)">-</button>
        <span id="pillow">0</span>
        <button onclick="changeQty('pillow',1)">+</button>
      </div>
    </div>
  </div>

<!-- sofa cover -->
    <div class="col-12 col-sm-6 col-lg-4 item" data-category="bedding"data-service="laundry">
    <div class="laundry-card text-center">
      <img src="HOME-Images/sofa.png" class="item-icon">
      <h5>Sofa Cover</h5>
      <p class="price">₹25</p>

      <div class="qty-box">
        <button onclick="changeQty('sofacover',-1)">-</button>
        <span id="sofacover">0</span>
        <button onclick="changeQty('sofacover',1)">+</button>
      </div>
    </div>
  </div>

  <!-- comforter -->
    <div class="col-12 col-sm-6 col-lg-4 item" data-category="bedding" data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/comforter.png" class="item-icon">
      <h5>Comforter </h5>
      <p class="price">₹25</p>

      <div class="qty-box">
        <button onclick="changeQty('comforter',-1)">-</button>
        <span id="comforter">0</span>
        <button onclick="changeQty('comforter',1)">+</button>
      </div>
    </div>
  </div>

  <!-- ================= CARPETS ================= -->

  <!-- small carpet -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="carpets"data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/small-carpet.png" class="item-icon">
      <h5>Small Carpet</h5>
      <p class="price">₹70</p>

      <div class="qty-box">
        <button onclick="changeQty('smallcarpet',-1)">-</button>
        <span id="smallcarpet">0</span>
        <button onclick="changeQty('smallcarpet',1)">+</button>
      </div>
    </div>
  </div>

  <!-- large carpet -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="carpets" data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/large-carpet.png" class="item-icon">
      <h5>Large Carpet</h5>
      <p class="price">₹120</p>

      <div class="qty-box">
        <button onclick="changeQty('largecarpet',-1)">-</button>
        <span id="largecarpet">0</span>
        <button onclick="changeQty('largecarpet',1)">+</button>
      </div>
    </div>
  </div>

  <!-- door mat -->
    <div class="col-12 col-sm-6 col-lg-4 item" data-category="carpets" data-service="laundry">
    <div class="laundry-card text-center">
      <img src="HOME-Images/door-mat.png" class="item-icon">
      <h5>Door Mat</h5>
      <p class="price">₹120</p>

      <div class="qty-box">
        <button onclick="changeQty('doormat',-1)">-</button>
        <span id="doormat">0</span>
        <button onclick="changeQty('doormat',1)">+</button>
      </div>
    </div>
  </div>

  <!-- floor rug -->
    <div class="col-12 col-sm-6 col-lg-4 item" data-category="carpets" data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/rug.png" class="item-icon">
      <h5>Floor Rug</h5>
      <p class="price">₹120</p>

      <div class="qty-box">
        <button onclick="changeQty('floorrug',-1)">-</button>
        <span id="floorrug">0</span>
        <button onclick="changeQty('floorrug',1)">+</button>
      </div>
    </div>
  </div>

  <!-- ================= SHOES ================= -->

  <!-- sneakers -->
  <div class="col-12 col-sm-6 col-lg-4 item" data-category="shoes"data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/sneakers.png" class="item-icon">
      <h5>Sneakers</h5>
      <p class="price">₹40</p>

      <div class="qty-box">
        <button onclick="changeQty('sneakers',-1)">-</button>
        <span id="sneakers">0</span>
        <button onclick="changeQty('sneakers',1)">+</button>
      </div>
    </div>
  </div>

  <!-- formal shoe -->
    <div class="col-12 col-sm-6 col-lg-4 item" data-category="shoes" data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/formal-shoe.png" class="item-icon">
      <h5>Formal Shoes</h5>
      <p class="price">₹40</p>

      <div class="qty-box">
        <button onclick="changeQty('formalshoes',-1)">-</button>
        <span id="formalshoes">0</span>
        <button onclick="changeQty('formalshoes',1)">+</button>
      </div>
    </div>
  </div>

  <!-- boots -->
    <div class="col-12 col-sm-6 col-lg-4 item" data-category="shoes" data-service="dryclean">
    <div class="laundry-card text-center">
      <img src="HOME-Images/boots.png" class="item-icon">
      <h5>Boots</h5>
      <p class="price">₹40</p>

      <div class="qty-box">
        <button onclick="changeQty('boots',-1)">-</button>
        <span id="boots">0</span>
        <button onclick="changeQty('boots',1)">+</button>
      </div>
    </div>
  </div>

  <!-- slippers -->
    <div class="col-12 col-sm-6 col-lg-4 item" data-category="shoes" data-service="laundry">
    <div class="laundry-card text-center">
      <img src="HOME-Images/slippers.png" class="item-icon">
      <h5>Slippers</h5>
      <p class="price">₹40</p>

      <div class="qty-box">
        <button onclick="changeQty('slippers',-1)">-</button>
        <span id="slippers">0</span>
        <button onclick="changeQty('slippers',1)">+</button>
      </div>
    </div>
  </div>

</div>

<div class="text-end mt-4 mb-5">

  <button
    class="btn btn-primary btn-lg proceed-btn"
    onclick="proceedOrder()">

    Proceed →

  </button>

</div>

</main>
</div>
</div>

<script>

const categoryDropdown = document.getElementById("categorySelect");
const serviceDropdown = document.getElementById("serviceSelect");

const cards = document.querySelectorAll(".item");

/* DEFAULT FILTER */
filterCards();

/* CATEGORY CHANGE */
categoryDropdown.addEventListener("change", filterCards);

/* SERVICE CHANGE */
serviceDropdown.addEventListener("change", filterCards);


/* FILTER FUNCTION */
function filterCards() {

  const selectedCategory = categoryDropdown.value;
  const selectedService = serviceDropdown.value;

  cards.forEach(card => {

    const cardCategory = card.getAttribute("data-category");

    /* MULTIPLE SERVICES SUPPORT */
    const cardServices = card.getAttribute("data-service").split(",");

    /* SHOW CARD */
    if (
      cardCategory === selectedCategory &&
      cardServices.includes(selectedService)
    ) {

      card.style.display = "block";

    }

    /* HIDE CARD */
    else {

      card.style.display = "none";

    }

  });

}

</script>



<!-- QUANTITY JS -->
<script>

const quantities = {};

function changeQty(item, change){

  if(!quantities[item]){
    quantities[item] = 0;
  }

  quantities[item] += change;

  if(quantities[item] < 0){
    quantities[item] = 0;
  }

  document.getElementById(item).innerText = quantities[item];

}

</script>
<script>
function proceedOrder(){

    let selectedItems = [];

    let totalClothes = 0;

    let totalPrice = 0;

    /* GET ALL CARDS */
    document.querySelectorAll(".item")
    .forEach(card => {

        let itemName =
            card.querySelector("h5").innerText;

        let itemPrice =
            parseInt(
                card.querySelector(".price")
                .innerText.replace("₹","")
            );

        let qtySpan =
            card.querySelector(".qty-box span");

        let qty =
            parseInt(qtySpan.innerText);

        if(qty > 0){

            selectedItems.push({

                name: itemName,
                price: itemPrice,
                qty: qty

            });

            totalClothes += qty;

            totalPrice += itemPrice * qty;
        }

    });

    /* NOTHING SELECTED */
    if(selectedItems.length === 0){

        alert("Please select at least one item");

        return;
    }

    /* SAVE TO LOCAL STORAGE */
    localStorage.setItem(
        "washwave_order",
        JSON.stringify(selectedItems)
    );

    localStorage.setItem(
        "washwave_total_clothes",
        totalClothes
    );

    localStorage.setItem(
        "washwave_total_price",
        totalPrice
    );

    /* REDIRECT */
    window.location.href =
        "order-summary.php";
}
</script>

<!-- Bottom Nav (Mobile Only) -->
<!-- Bootstrap Icons -->
<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


<!-- Bottom Nav -->
<nav class="navbar fixed-bottom d-lg-none bottom-nav">

  <div class="container-fluid d-flex justify-content-around text-center">

    <!-- HOME -->
    <a href="user-dashboard.php"
       class="nav-item">

      <i class="bi bi-house-door-fill"></i>

      <span>Home</span>

    </a>

    <!-- ORDERS -->
    <a href="my-orders.php"
       class="nav-item">

      <i class="bi bi-bag-check-fill"></i>

      <span>Orders</span>

    </a>

    <!-- LOGOUT -->
    <a href="user-logout.php"
       class="nav-item">

      <i class="bi bi-box-arrow-right"></i>

      <span>Logout</span>

    </a>

  </div>

</nav>

</body>
</html>