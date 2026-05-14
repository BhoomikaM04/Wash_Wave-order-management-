<?php
session_start();

$conn = new mysqli("localhost", "root", "", "wash_wave");

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$message = "";
$icon = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
        $message = "Enter valid Gmail!";
        $icon = "error";
    } else {

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                // ✅ SESSION SET
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];

                // ✅ DIRECT REDIRECT (NO SWEETALERT DELAY)
                header("Location: user-dashboard.php");
                exit();

            } else {
                $message = "Incorrect Password!";
                $icon = "error";
            }

        } else {
            $message = "User not found!";
            $icon = "error";
        }

        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<title>User Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    font-family: Arial;
    background: #f2f2f2;
}

.form-box {
    max-width: 400px;
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

<div class="container d-flex justify-content-center align-items-center min-vh-100">
<div class="card shadow p-4 form-box">

<h2 class="text-center mb-4">User Login</h2>

<form method="POST" class="needs-validation" novalidate>

<!-- USER ID -->
<div class="mb-3">
    <label class="form-label">User ID</label>
    <input type="email" name="email" id="email"
           class="form-control"
           pattern="^[a-zA-Z0-9._%+-]+@gmail\.com$"
           value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
           required>

    <div class="invalid-feedback">
        User ID must be a valid Gmail (example@gmail.com)
    </div>
</div>

<!-- PASSWORD -->
<div class="mb-3">
    <label class="form-label">Password</label>

    <div class="position-relative">
        <input type="password" name="password" id="password"
               class="form-control pe-5"
               required>

        <i class="fa-solid fa-eye-slash eye-icon"
           onclick="togglePassword('password', this)"></i>
    </div>

    <div class="invalid-feedback">
        Please enter your password
    </div>
</div>

<button type="submit" class="btn btn-success w-100">Login</button>

<p class="mt-3 text-center">
    New user? <a href="user-register.php">Register here</a>
</p>

</form>

</div>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (!empty($message)): ?>
<script>
Swal.fire({
    icon: '<?php echo $icon; ?>',
    title: '<?php echo ($icon == "success") ? "Success" : "Error"; ?>',
    text: '<?php echo $message; ?>'
}).then(() => {
    <?php if ($icon == "success"): ?>
        window.location.href = "user-dashboard.php";
    <?php endif; ?>
});
</script>
<?php endif; ?>

<!-- Bootstrap Validation -->
<script>
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')

  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})();
</script>

<!-- Real-time Gmail Validation -->
<script>
const email = document.getElementById("email");

email.addEventListener("input", () => {
    const regex = /^[a-zA-Z0-9._%+-]+@gmail\.com$/;
    email.classList.toggle("is-valid", regex.test(email.value));
    email.classList.toggle("is-invalid", !regex.test(email.value));
});
</script>

<!-- Eye Toggle -->
<script>
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