<?php

session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin-login.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin-dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <title>Document</title>
 <!-- JavaScript -->
     <script>
                function toggleMenu() {
                    let menu = document.getElementById("dropdown");
                    menu.style.display = menu.style.display === "block" ? "none" : "block";
                }

                function uploadImage() {
                    document.getElementById("fileInput").click();
                }

                function changeImage(event) {
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        document.getElementById("profilePic").src = e.target.result;
                    }
                    reader.readAsDataURL(event.target.files[0]);
                }

                window.onclick = function (e) {
                    if (!e.target.closest(".profile")) {
                        document.getElementById("dropdown").style.display = "none";
                    }
                }
</script>
 
</head>

<body>

<div class="container">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="menu">
            <img src="images/washwave-logo.jpg" class="logo" width="60px">
            <a href="#"><i class="fas fa-th-large"></i>Dashboard</a>
            <a href="#"><i class="fas fa-users"></i>Customers</a>
            <a href="#"><i class="fas fa-box"></i>Orders</a>
            <a href="admin-logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
        </div>
    </div>

    <!-- Main -->
    <div class="main">

        <!-- Header -->
        <div class="header">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search">
            </div>

            <div class="profile">
                <i class="fas fa-bell"></i>
                <span>Admin</span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <h1>Dashboard</h1>

            <!-- Cards -->
            <div class="cards">

                <div class="card total">
                    <i class="fas fa-clipboard-list fa-2x"></i>
                    <h3>Total Orders</h3>
                    <h2>150</h2>
                </div>

                <div class="card pending">
                    <i class="fas fa-hourglass-half fa-2x"></i>
                    <h3>Pending Orders</h3>
                    <h2>45</h2>
                </div>

                <div class="card ready">
                    <i class="fas fa-clock fa-2x"></i>
                    <h3>Orders Ready</h3>
                    <h2>80</h2>
                </div>

                <div class="card completed">
                    <i class="fas fa-check-circle fa-2x"></i>
                    <h3>Completed Orders</h3>
                    <h2>105</h2>
                </div>

            </div>

            <!-- Orders Table -->
            <div class="orders-box">
                <h1>Recent Orders</h1>

                <table>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Service</th>
                        <th>Status</th>
                    </tr>

                    <tr>
                        <td>1023</td>
                        <td>John Smith</td>
                        <td>Wash & Iron</td>
                        <td><span class="status-pending">Pending</span></td>
                    </tr>

                    <tr>
                        <td>1020</td>
                        <td>Emily Davis</td>
                        <td>Dry Cleaning</td>
                        <td><span class="status-completed">Completed</span></td>
                    </tr>

                    <tr>
                        <td>1018</td>
                        <td>Michael Lee</td>
                        <td>Wash Only</td>
                        <td><span class="status-processing">Processing</span></td>
                    </tr>

                    <tr>
                        <td>1015</td>
                        <td>Sarah Brown</td>
                        <td>Ironing</td>
                        <td><span class="status-ready">Ready</span></td>
                    </tr>

                </table>
            </div>

        </div>

    </div>

</div>

</body>
</html>


