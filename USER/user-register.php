<?php

/* ================= DATABASE CONNECTION ================= */
$conn = new mysqli("localhost", "root", "", "wash_wave");

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

/* ================= INITIAL VARIABLES ================= */
$message = "";
$icon = "";

/* ================= RETAIN FORM VALUES ================= */
$name = $email = $phone = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    /* ================= SAFE INPUT ASSIGNMENT ================= */
    $name = isset($_POST['name']) ? trim($_POST['name']) : "";
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : "";
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    /* ================= VALIDATIONS ================= */
    if (!preg_match("/^[a-zA-Z]+$/", $name)) {
        $message = "Name must contain only alphabets!";
        $icon = "error";
    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/@gmail\.com$/", $email)) {
        $message = "Enter a valid Gmail address!";
        $icon = "error";
    }

    elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $message = "Phone number must be exactly 10 digits!";
        $icon = "error";
    }

    elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters!";
        $icon = "error";
    }

    elseif (!preg_match("/[A-Z]/", $password)) {
        $message = "Password must contain at least one uppercase letter!";
        $icon = "error";
    }

    elseif (!preg_match("/[a-z]/", $password)) {
        $message = "Password must contain at least one lowercase letter!";
        $icon = "error";
    }

    elseif (!preg_match("/[0-9]/", $password)) {
        $message = "Password must contain at least one number!";
        $icon = "error";
    }

    elseif (preg_match_all("/[^a-zA-Z0-9]/", $password) != 1) {
        $message = "Password must contain exactly one special character!";
        $icon = "error";
    }

    elseif ($password !== $confirm) {
        $message = "Password does not match!";
        $icon = "error";
    }

    else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email=? OR phone=?");
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "User already registered!";
            $icon = "warning";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $hashedPassword);

            if ($stmt->execute()) {
                $message = "Registration successful!";
                $icon = "success";
            } else {
                $message = "Registration failed! Try again.";
                $icon = "error";
            }
        }

        $stmt->close();
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Registration</title>

<!-- ================= BOOTSTRAP & ICONS ================= -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- ================= CUSTOM STYLES ================= -->
<style>
body {
    font-family: Arial;
    background: #f2f2f2;
}

.form-box {
    max-width: 450px;
    width: 100%;
}

.eye-icon {
    position: absolute;
    right: 28px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: black;
    z-index: 2;
}
</style>
</head>

<body>

<!-- ================= FORM UI ================= -->
<div class="container d-flex justify-content-center align-items-center min-vh-100">
<div class="card shadow p-4 form-box">

<h2 class="text-center mb-4">User Registration</h2>

<form method="POST" action="" class="needs-validation" novalidate>
<!-- Name -->
<div class="mb-3">
    <label class="form-label">Name</label>
    <input type="text" class="form-control" id="name" name="name"
    value="<?php echo htmlspecialchars($name); ?>" required>
    <div class="invalid-feedback">Only alphabets allowed</div>
</div>

<!-- Email -->
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" class="form-control" id="email" name="email"
    value="<?php echo htmlspecialchars($email); ?>" required>
    <div class="invalid-feedback">Enter valid Gmail</div>
</div>

<!-- Phone -->
<div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text" class="form-control" id="phone" name="phone"
    value="<?php echo htmlspecialchars($phone); ?>" required>
    <div class="invalid-feedback">Must be exactly 10 digits</div>
</div>

<!-- Password -->
<div class="mb-3">
    <label class="form-label">Password</label>
    <div class="position-relative">
        <input type="password" class="form-control pe-5" id="password" name="password" required>
        <i class="fa-solid fa-eye-slash eye-icon" onclick="togglePassword('password', this)"></i>
    </div>
    <div class="invalid-feedback">Invalid password format</div>
</div>

<!-- Confirm Password -->
<div class="mb-3">
    <label class="form-label">Confirm Password</label>
    <div class="position-relative">
        <input type="password" class="form-control pe-5" id="confirm_password" name="confirm_password" required>
        <i class="fa-solid fa-eye-slash eye-icon" onclick="togglePassword('confirm_password', this)"></i>
    </div>
    <div class="invalid-feedback">Passwords do not match</div>
</div>

<!-- Submit Button -->
<button type="submit" class="btn btn-primary w-100">Register</button>

<p class="mt-3 text-center">
    Already registered? <a href="user-login.php">Login here</a>
</p>

</form>

</div>
</div>

<!-- ================= SWEETALERT SCRIPT ================= -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- ================= PASS PHP DATA TO JAVASCRIPT ================= -->
<script>
const message = <?php echo json_encode($message); ?>;
const icon = <?php echo json_encode($icon); ?>;

/* ================= SWEETALERT HANDLING (PURE JS) ================= */
if (message) {
    Swal.fire({
        icon: icon,
        title: icon === "success" ? "Success" : "Notice",
        text: message
    }).then(() => {
        if (icon === "success") {
            window.location.href = "user-register.php";
        }
    });
}
</script>

<!-- ================= FORM VALIDATION (JS) ================= -->
<script>
const name = document.getElementById("name");
const email = document.getElementById("email");
const phone = document.getElementById("phone");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirm_password");

name.addEventListener("input", () => {
    toggleValidation(name, /^[a-zA-Z]+$/.test(name.value));
});

email.addEventListener("input", () => {
    toggleValidation(email, email.value.endsWith("@gmail.com"));
});

phone.addEventListener("input", () => {
    toggleValidation(phone, /^[0-9]{10}$/.test(phone.value));
});

password.addEventListener("input", () => {
    let valid =
        password.value.length >= 8 &&
        /[A-Z]/.test(password.value) &&
        /[a-z]/.test(password.value) &&
        /[0-9]/.test(password.value) &&
        (password.value.match(/[^a-zA-Z0-9]/g) || []).length === 1;

    toggleValidation(password, valid);
});

confirmPassword.addEventListener("input", () => {
    toggleValidation(confirmPassword, password.value === confirmPassword.value);
});

function toggleValidation(input, isValid) {
    input.classList.toggle("is-valid", isValid);
    input.classList.toggle("is-invalid", !isValid);
}

/* ================= PASSWORD TOGGLE ================= */
function togglePassword(fieldId, icon) {
    let input = document.getElementById(fieldId);

    if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye-slash", "fa-eye");
    } else {
        input.type = "password";
        icon.classList.replace("fa-eye", "fa-eye-slash");
    }
}
</script>

</body>
</html>