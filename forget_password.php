<?php 
    session_start(); 
    require_once "config.php"; 

    if (isset($_REQUEST["email"])) {
        $email = $_REQUEST["email"];
        $str = "select username from users where email='" . $email . "'";
        $res = $link->query($str);
        
        if ($res->num_rows == 1) {
            $row = $res->fetch_assoc();
            $username = $row['username'];
            $new_otp = rand(1000, 9999);
            
            $str = "update users set otp_code='" . $new_otp . "' where email='" . $email . "'";
            $link->query($str);
            
            header("location: change_password.php?username=" . $username);
            exit();
        } else {
            $error_msg = "Email ID not found in database!";
        }
    }

    include "header.php"; 
?>
<div class="glass-wrapper animate-fade">
    <div class="glass-card" style="max-width: 420px; width: 100%;">
        <div class="text-center mb-3">
            <div class="fw-semibold text-white-50 small mb-1" style="letter-spacing: 1px;">RECOVERY NODE</div>
            <h3 class="fw-bold text-white m-0">Reset Password</h3>
        </div>
        
        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-20 text-white text-center rounded-3 small py-2 mb-3">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="post">
            <div class="form-glass-group">
                <label>Registered Email ID</label>
                <input type="email" name="email" class="form-glass-input" required placeholder="name@domain.com">
            </div>
            <button type="submit" class="btn btn-glass-submit mb-3">Generate Recovery OTP</button>
            <div class="text-center">
                <a href="login_page.php" class="small text-white text-decoration-none fw-semibold" style="opacity: 0.7;">Back to Login</a>
            </div>
        </form>
    </div>
</div>
<?php include "footer.php"; ?>
