<?php
    session_start();
    require_once "config.php";

    if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
        header("location: dashboard.php");
        exit;
    }

    $error_msg = "";
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $fullname = trim($_POST["fullname"] ?? "");
        $mobile = trim($_POST["mobile"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $dob = trim($_POST["dob"] ?? "");
        $username = trim($_POST["username"] ?? "");
        $password = $_POST["password"] ?? "";

        if (!preg_match("/^[A-Za-z .'-]{2,80}$/", $fullname)) {
            $error_msg = "Please enter a valid full name.";
        } elseif (!preg_match("/^[0-9]{10}$/", $mobile)) {
            $error_msg = "Mobile number must contain exactly 10 digits.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_msg = "Please enter a valid email address.";
        } elseif ($dob === "" || $username === "" || strlen($username) < 3) {
            $error_msg = "Please complete all fields with a valid username.";
        } elseif (strlen($password) < 6) {
            $error_msg = "Password must be at least 6 characters.";
        } else {
            $check = $link->prepare("select id from users where username = ? or email = ? limit 1");
            $check->bind_param("ss", $username, $email);
            $check->execute();
            if ($check->get_result()->num_rows > 0) {
                $error_msg = "Username or email is already registered.";
            } else {
                $otp = (string)rand(1000, 9999);
                $stmt = $link->prepare("insert into users (full_name, mobile_number, email, dob, username, password, otp_code, is_verified) values (?, ?, ?, ?, ?, ?, ?, 0)");
                $stmt->bind_param("sssssss", $fullname, $mobile, $email, $dob, $username, $password, $otp);
                if ($stmt->execute()) {
                    header("location: verify_otp.php?username=" . urlencode($username));
                    exit;
                }
                $error_msg = "Registration could not be completed. Please try again.";
            }
        }
    }

    include "header.php";
?>
<div class="glass-wrapper animate-fade">
    <div class="glass-card" style="max-width: 460px; margin-top: 20px; margin-bottom: 20px;">
        <div class="text-center mb-3">
            <img src="logo.png" onerror="this.src='logo.jpg'; this.onerror=null;" class="brand-logo-img" alt="logo">
            <h3 class="fw-bold text-white m-0">Register Node</h3>
        </div>
        <?php if ($error_msg !== ""): ?><div class="alert alert-danger border-0 bg-danger bg-opacity-20 text-white text-center rounded-3 small py-2 mb-3"><?php echo htmlspecialchars($error_msg); ?></div><?php endif; ?>
        <form action="register.php" method="post">
            <div class="form-glass-group"><label>Full Name</label><input type="text" name="fullname" class="form-glass-input" required minlength="2" pattern="[A-Za-z .'-]{2,80}" placeholder="John Doe"><span class="validation-hint">Use letters and spaces only.</span></div>
            <div class="form-glass-group"><label>Mobile Number</label><input type="tel" name="mobile" class="form-glass-input" required pattern="[0-9]{10}" maxlength="10" inputmode="numeric" placeholder="10 Digit Number"><span class="validation-hint">Enter exactly 10 digits.</span></div>
            <div class="form-glass-group"><label>Email Address</label><input type="email" name="email" class="form-glass-input" required placeholder="example@mail.com"><span class="validation-hint">Enter a valid email address.</span></div>
            <div class="form-glass-group"><label>Date of Birth</label><input type="date" name="dob" class="form-glass-input" required><span class="validation-hint">Select your date of birth.</span></div>
            <div class="form-glass-group"><label>Username ID</label><input type="text" name="username" class="form-glass-input" required minlength="3" maxlength="30" placeholder="Choose Unique ID"><span class="validation-hint">Username must be at least 3 characters.</span></div>
            <div class="form-glass-group"><label>Secure Password</label><input type="password" name="password" class="form-glass-input" required minlength="6" placeholder="At least 6 characters"><span class="validation-hint">Password must be at least 6 characters.</span></div>
            <button type="submit" class="btn btn-glass-submit mb-3">Register Node</button>
            <div class="text-center"><span class="small" style="color: rgba(255,255,255,0.6); font-size:13px;">Already registered? <a href="login_page.php" class="text-white text-decoration-none fw-bold ms-1">Sign in here</a></span></div>
        </form>
    </div>
</div>
<?php include "footer.php"; ?>
