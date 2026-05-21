<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit();
}

// Configuration: Simulated premium environment parameters
$razorpay_api_key = "rzp_live_official_4kX9aP2LzQvW4b"; 

// Capture order details from the previous order-summary page post
$service_type = $_POST['service_type'] ?? '';
$total_clothes = $_POST['total_clothes'] ?? 0;
$total_price = $_POST['total_price'] ?? 0;
$cloth_list = $_POST['cloth_list'] ?? '';
$address = $_POST['address'] ?? '';
$delivery_date = $_POST['delivery_date'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Select Payment Method</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<style>
* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    background: #f8f9fa;
    color: #333;
    overflow-x: hidden;
}

.dashboard-container {
    display: flex;
    min-height: 100vh;
    flex-direction: column;
}

/* Sidebar Navigation (Dashboard Alignment) */
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

.logo { display: none; }

.menu a {
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 11px;
    color: black;
    flex: 1;
    gap: 4px;
}

.menu a i { font-size: 20px; }

.main {
    width: 100%;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding-bottom: 90px;
}

.content {
    padding: 30px 20px;
    width: 100%;
}

/* Payment Selection Card Wrapper */
.payment-box {
    max-width: 600px;
    margin: 0 auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    border: 1px solid #eee;
}

.option-card {
    border: 2px solid #eee;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 16px;
    cursor: pointer;
    transition: 0.2s ease-in-out;
    display: flex;
    align-items: center;
    gap: 15px;
}

.option-card:hover {
    border-color: #2c3e50;
    background-color: #fdfdfd;
}

.option-card input[type="radio"] {
    width: 20px;
    height: 20px;
    accent-color: #2c3e50;
}

.option-icon {
    font-size: 28px;
    color: #2c3e50;
}

.btn-proceed {
    width: 100%;
    background-color: #333;
    color: white;
    padding: 14px;
    font-size: 16px;
    font-weight: 600;
    border: none;
    border-radius: 8px;
    transition: 0.2s;
}

.btn-proceed:hover {
    background-color: rgb(106, 105, 105);
}

/* ==========================================================================
   UPDATED: HIGH-END RESPONSIVE RAZORPAY CHECKOUT INTERFACE
   ========================================================================== */
.rzp-backdrop {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    z-index: 2000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 15px;
}

.rzp-container {
    width: 100%;
    max-width: 750px;
    min-height: 500px;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    display: flex;
    overflow: hidden;
    position: relative;
}

/* Razorpay Left Panel Branding Section */
.rzp-sidebar-brand {
    width: 35%;
    background: #0f172a;
    color: white;
    padding: 30px 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.rzp-sidebar-brand h5 { font-size: 18px; font-weight: 700; letter-spacing: -0.3px; margin: 0;}
.rzp-sidebar-brand .amount-label { font-size: 12px; color: #94a3b8; display: block; margin-top: 40px;}
.rzp-sidebar-brand .amount-value { font-size: 28px; font-weight: 800; color: #fff; display: block; line-height: 1.2;}

.rzp-trusted-badge {
    font-size: 11px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 6px;
    border-top: 1px solid #1e293b;
    padding-top: 15px;
    margin-top: 30px;
}

/* Razorpay Main Workspace Section */
.rzp-main-workspace {
    width: 65%;
    background: #fff;
    display: flex;
    flex-direction: column;
}

.rzp-workspace-header {
    padding: 24px 30px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rzp-workspace-header h6 { 
    margin: 0; 
    font-size: 15px;
    font-weight: 600;
    color: #1e293b; 
}

.rzp-workspace-header .btn-close-rzp { 
    background: none;
    border: none;
    font-size: 24px; 
    color: #94a3b8;
    cursor: pointer;
    line-height: 1;
}

.rzp-workspace-body {
    padding: 30px;
    flex-grow: 1;
}

/* Premium App Selector Grid Matrix */
.upi-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 24px;
}

.upi-app-btn {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 12px;
    text-align: center;
    cursor: pointer;
    background: #fff;
    transition: 0.15s ease-in-out;
}

.upi-app-btn:hover, .upi-app-btn.active {
    border-color: #3b82f6;
    background: #f0f6ff;
}

.upi-app-btn i {
    font-size: 22px;
    display: block;
    margin-bottom: 4px;
}

.upi-app-btn span {
    font-size: 11px;
    font-weight: 600;
    color: #475569;
    display: block;
}

.color-gpay {
    color: #ea4335;
}

.color-phonepe {
    color: #5f259f;
}

.color-paytm {
    color: #00baf2;
}

/* Custom Form Input Fields Adjustments */
.rzp-field-container {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 10px 14px;
    margin-bottom: 20px;
}

.rzp-field-container label {
    font-size: 10px;
    font-weight: 700;
    color: #3b82f6;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 2px;
}

.rzp-field-container input {
    width: 100%;
    border: none;
    background: transparent;
    outline: none;
    font-size: 15px;
    color: #1e293b;
    font-weight: 500;
}

.btn-rzp-pay {
    width: 100%;
    background: #2563eb; 
    color: white;
    border: none;
    padding: 14px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    transition: 0.2s;
}

.btn-rzp-pay:hover { 
    background: #1d4ed8; 
}

/* ==========================================================================
   CINEMATIC COIN AND CHECKMARK ANIMATION MATRIX OVERLAY
   ========================================================================== */
.animation-screen {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background: #fff;
    z-index: 2010;
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
    text-align: center;
}

.coin-wrapper {
    perspective: 1000px;
    margin-bottom: 24px;
}

.gold-coin {
    width: 90px;
    height: 90px;
    background: linear-gradient(135deg, #ffe066 0%, #f59e0b 50%, #d97706 100%);
    border-radius: 50%;
    box-shadow: 0 0 0 6px #fff, 0 0 0 9px #fbbf24, 0 15px 30px rgba(217,119,6,0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 46px;
    color: #fff;
    font-weight: 800;
    text-shadow: 0 2px 5px rgba(0,0,0,0.2);
    animation: spinCoin 1.0s infinite linear;
}

@keyframes spinCoin {
    0% { transform: rotateY(0deg); }
    100% { transform: rotateY(360deg); }
}

.success-circle {
    width: 95px;
    height: 95px;
    background: #10b981;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 30px rgba(16,185,129,0.4);
    transform: scale(0);
    animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
}

.success-circle i {
    font-size: 54px;
    color: white;
}

@keyframes popIn {
    to { transform: scale(1); }
}

.status-headline {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    margin-top: 15px;
}

/* ==========================================================================
   DESKTOP RESPONSIVE LAYOUT BLUEPRINTS (1024px Grid)
   ========================================================================== */
@media (min-width: 1024px) {
    .dashboard-container {
        flex-direction: row;
    }

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

    .main {
        margin-left: 110px;
        width: calc(100% - 110px);
        padding-bottom: 0;
    }

    .content {
        padding: 40px;
    }
}

/* ==========================================================================
   CHANGED: FIXED MOBILE VIEW LAYOUT RESPONSIVENESS OVERRIDES (768px and down)
   ========================================================================== */
@media (max-width: 768px) {
    .rzp-backdrop {
        padding: 10px;
    }
    .rzp-container {
        flex-direction: column;
        min-height: auto;
        max-height: 92vh;
        overflow-y: auto;
    }
    .rzp-sidebar-brand {
        width: 100%;
        padding: 20px;
        flex-direction: row;
        align-items: center;
        justify-content: justify;
        flex-wrap: wrap;
        gap: 10px;
    }
    .rzp-sidebar-brand .amount-label {
        margin-top: 0;
        font-size: 10px;
    }
    .rzp-sidebar-brand .amount-value {
        font-size: 22px;
    }
    .rzp-trusted-badge {
        display: none; /* Hide desktop badge style on small views to conserve display density */
    }
    .rzp-main-workspace {
        width: 100%;
    }
    .rzp-workspace-header {
        padding: 15px 20px;
    }
    .rzp-workspace-body {
        padding: 20px;
    }
    .upi-grid {
        gap: 8px;
        margin-bottom: 16px;
    }
    .upi-app-btn {
        padding: 8px;
    }
    .upi-app-btn i {
        font-size: 18px;
    }
    .upi-app-btn span {
        font-size: 10px;
    }
}
</style>
</head>
<body>

<div class="dashboard-container">

    <div class="sidebar">
        <div class="menu">
            <img src="../assets/img/logo.png" class="logo" alt="WashWave Logo">
            <a href="user-dashboard.php"><i class="fas fa-th-large"></i>Dashboard</a>
            <a href="my-orders.php"><i class="fas fa-box"></i>My Orders</a>
            <a href="user-logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
        </div>
    </div>

    <div class="main">
        <div class="content">
            
            <div class="payment-box">
                <h3 class="fw-bold mb-2">Secure Checkout</h3>
                <p class="text-muted mb-4">Choose your preferred method to complete payment processing for this checkout transaction.</p>

                <div class="alert alert-light border mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted d-block" style="font-size:13px;">Total Payable Amount</span>
                        <strong class="fs-4 text-dark">₹<?php echo htmlspecialchars($total_price); ?></strong>
                    </div>
                    <span class="badge bg-secondary px-3 py-2"><?php echo htmlspecialchars($total_clothes); ?> Clothes</span>
                </div>

                <form id="finalOrderForm" action="../save-order.php" method="POST">
                    <input type="hidden" name="service_type" value="<?php echo htmlspecialchars($service_type); ?>">
                    <input type="hidden" name="total_clothes" value="<?php echo htmlspecialchars($total_clothes); ?>">
                    <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($total_price); ?>">
                    <input type="hidden" name="cloth_list" value="<?php echo htmlspecialchars($cloth_list); ?>">
                    <input type="hidden" name="address" value="<?php echo htmlspecialchars($address); ?>">
                    <input type="hidden" name="delivery_date" value="<?php echo htmlspecialchars($delivery_date); ?>">
                    <input type="hidden" name="payment_method" id="paymentMethodInput" value="POD">

                    <div class="option-card" onclick="selectRadio('pod')">
                        <input type="radio" id="pod" name="method_select" value="POD" checked>
                        <i class="bi bi-truck option-icon"></i>
                        <div>
                            <strong class="d-block text-dark">Pay on Delivery (POD)</strong>
                            <small class="text-muted">Pay at your doorstep via cash or card upon delivery.</small>
                        </div>
                    </div>

                    <div class="option-card" onclick="selectRadio('upi')">
                        <input type="radio" id="upi" name="method_select" value="UPI">
                        <i class="bi bi-qr-code-scan option-icon"></i>
                        <div>
                            <strong class="d-block text-dark">Instant Online UPI Payment</strong>
                            <small class="text-muted">Pay securely using an animated premium Razorpay UPI payment layout framework.</small>
                        </div>
                    </div>

                    <button type="button" class="btn-proceed mt-3" onclick="processPaymentSelection()">Proceed to Fulfill Order →</button>
                </form>
            </div>

        </div>
    </div>
</div>

<div class="rzp-backdrop" id="razorpayModal">
    <div class="rzp-container">
        
        <div class="animation-screen" id="animationOverlay">
            <div class="coin-wrapper" id="coinFrame">
                <div class="gold-coin">₹</div>
            </div>
            <div class="success-circle" id="successFrame">
                <i class="bi bi-check-lg"></i>
            </div>
            <div class="status-headline" id="animationText">Connecting Securely...</div>
        </div>

        <div class="rzp-sidebar-brand">
            <div>
                <h5>WashWave</h5>
                <span class="text-muted" style="font-size: 11px; display: block;">Merchant Gateway</span>
            </div>
            <div>
                <span class="amount-label">AMOUNT TO PAY</span>
                <span class="amount-value">₹<?php echo htmlspecialchars($total_price); ?></span>
            </div>
            
            <div class="rzp-trusted-badge">
                <i class="bi bi-shield-fill-check text-primary fs-5"></i>
                <span>Razorpay Secure<br><code style="color: #94a3b8; font-size: 9px;"><?php echo htmlspecialchars(substr($razorpay_api_key, 0, 12)); ?>...</code></span>
            </div>
        </div>

        <div class="rzp-main-workspace">
            <div class="rzp-workspace-header">
                <h6>Preferred UPI Application Mode</h6>
                <button type="button" class="btn-close-rzp" onclick="closeRazorpayWindow()">&times;</button>
            </div>
            
            <div class="rzp-workspace-body">
                <div class="upi-grid">
                    <div class="upi-app-btn active" onclick="setActiveApp(this)">
                        <i class="fab fa-google-pay color-gpay"></i>
                        <span>Google Pay</span>
                    </div>
                    <div class="upi-app-btn" onclick="setActiveApp(this)">
                        <i class="bi bi-wallet2 color-phonepe"></i>
                        <span>PhonePe</span>
                    </div>
                    <div class="upi-app-btn" onclick="setActiveApp(this)">
                        <i class="bi bi-lightning-charge-fill color-paytm"></i>
                        <span>Paytm</span>
                    </div>
                </div>

                <div class="rzp-field-container">
                    <label>Virtual Payment Handle Address (UPI ID)</label>
                    <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'customer'); ?>@okaxis" placeholder="username@vpa">
                </div>

                <div class="rzp-field-container">
                    <label>Secure Verification Pin</label>
                    <input type="password" id="dummyPin" placeholder="••••" maxlength="6" style="letter-spacing:10px; font-weight:bold;">
                </div>

                <button type="button" class="btn-rzp-pay" onclick="executeMockVerification()">Pay ₹<?php echo htmlspecialchars($total_price); ?></button>
            </div>
        </div>

    </div>
</div>

<script>
function selectRadio(id) {
    document.getElementById(id).checked = true;
}

function setActiveApp(element) {
    document.querySelectorAll('.upi-app-btn').forEach(btn => btn.classList.remove('active'));
    element.classList.add('active');
}

function closeRazorpayWindow() {
    document.getElementById('razorpayModal').style.display = 'none';
}

function processPaymentSelection() {
    const isUpi = document.getElementById('upi').checked;
    
    if (isUpi) {
        document.getElementById('razorpayModal').style.display = 'flex';
    } else {
        document.getElementById('paymentMethodInput').value = 'POD';
        document.getElementById('finalOrderForm').submit();
    }
}

function executeMockVerification() {
    const pin = document.getElementById('dummyPin').value;
    if(pin.trim() === "") {
        alert("Authentication code required. Please key in your secure 4 or 6 digit transaction M-PIN.");
        return;
    }
    
    const animOverlay = document.getElementById('animationOverlay');
    const coinFrame = document.getElementById('coinFrame');
    const successFrame = document.getElementById('successFrame');
    const animText = document.getElementById('animationText');

    animOverlay.style.display = 'flex';
    coinFrame.style.display = 'block';
    successFrame.style.display = 'none';
    animText.innerText = "Verifying UPI Security Assets...";

    setTimeout(() => {
        animText.innerText = "Transferring Funds to Merchant...";
    }, 1100);

    setTimeout(() => {
        coinFrame.style.display = 'none';
        successFrame.style.display = 'flex';
        animText.innerText = "Payment Successful!";
        animText.style.color = "#10b981";
        
        setTimeout(() => {
            document.getElementById('paymentMethodInput').value = 'UPI';
            document.getElementById('finalOrderForm').submit();
        }, 1600);

    }, 2400);
}
</script>
</body>
</html>