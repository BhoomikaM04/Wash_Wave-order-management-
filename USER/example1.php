<?php
session_start();

$conn = new mysqli("localhost", "root", "", "wash_wave");

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

//  LINKING LOGIN → DASHBOARD (SESSION CHECK)
if (!isset($_SESSION['user_id'])) {
    header("Location: user-login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $items = $_POST['items'];
    $address = trim($_POST['address']);
    $pickup_date = $_POST['pickup_date'];

    if (empty($items) || empty($address) || empty($pickup_date)) {
        echo "<script>alert('All fields are required');</script>";
    } else {

        $stmt = $conn->prepare("INSERT INTO my_orders (user_id, service_type, address, pickup_date, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("isss", $user_id, $items, $address, $pickup_date);

        if ($stmt->execute()) {
            echo "<script>alert('Order Placed Successfully'); window.location.href='my_orders.php';</script>";
        } else {
            echo "<script>alert('Order Failed');</script>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>

    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            margin: 0;
        }

        .sidebar {
            width: 200px;
            height: 100vh;
            background: #111;
            color: white;
            position: fixed;
            padding: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: green;
        }

        .main {
            margin-left: 220px;
            padding: 20px;
        }

        .card {
            display: inline-block;
            width: 150px;
            background: white;
            padding: 15px;
            margin: 10px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 0 5px gray;
            cursor: pointer;
        }

        .card.selected {
            border: 2px solid green;
        }

        input, textarea {
            width: 300px;
            padding: 10px;
            margin: 10px 0;
        }

        button {
            padding: 10px 20px;
            background: green;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>

<div class="sidebar">
    <h3>WashWave</h3>
    <a href="user-dashboard.php">Dashboard</a>
    <a href="#">Billing</a>
    <a href="#">Payment</a>
    <a href="myorders.php">My Orders</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">

    <h2>Place Your Order</h2>

    <form method="POST" onsubmit="return validateForm()">

        <!-- CLOTH CARDS -->
        <div>
            <div class="card" onclick="selectItem(this, 'Shirt')">👕 Shirt (₹50)</div>
            <div class="card" onclick="selectItem(this, 'Pant')">👖 Pant (₹60)</div>
            <div class="card" onclick="selectItem(this, 'Saree')">👗 Saree (₹120)</div>
            <div class="card" onclick="selectItem(this, 'Sweater')">🧥 Sweater (₹80)</div>
        </div>

        <input type="hidden" name="items" id="items">

        <!-- ADDRESS -->
        <br>
        <textarea name="address" id="address" placeholder="Enter Pickup Address"></textarea>

        <br>

        <!-- DATE -->
        <input type="date" name="pickup_date" id="pickup_date">

        <br>

        <button type="submit">Place Order</button>

    </form>

</div>

<script>

let selectedItems = [];

function selectItem(card, item) {

    if (selectedItems.includes(item)) {
        selectedItems = selectedItems.filter(i => i !== item);
        card.classList.remove("selected");
    } else {
        selectedItems.push(item);
        card.classList.add("selected");
    }

    document.getElementById("items").value = selectedItems.join(", ");
}

function validateForm() {

    let items = document.getElementById("items").value;
    let address = document.getElementById("address").value.trim();
    let date = document.getElementById("pickup_date").value;

    if (items === "") {
        alert("Please select at least one item");
        return false;
    }

    if (address === "") {
        alert("Please enter address");
        return false;
    }

    if (date === "") {
        alert("Please select pickup date");
        return false;
    }

    return true;
}

</script>

</body>
</html>