<?php 
    session_start(); 
    require_once "config.php"; 

    $username = "";
    if (isset($_REQUEST["username"])) {
        $username = $_REQUEST["username"];
    }

    if (isset($_REQUEST["otp"]) && isset($_REQUEST["new_pass"])) {
        $otp_check = $_REQUEST["otp"];
        $new_pass = $_REQUEST["new_pass"];
        
        $str = "select otp_code from users where username='" . $username . "'";
        $db_res = $link->query($str);
        
        if ($db_res && $db_res->num_rows == 1) {
            $db_row = $db_res->fetch_assoc();
            if ($otp_check === $db_row['otp_code']) {
                $str = "update users set password='" . $new_pass . "', otp_code=NULL where username='" . $username . "'";
                $link->query($str);
                header("location: login_page.php?msg=Password updated successfully. Login now.");
                exit();
            } else {
                $error_msg = "Incorrect Recovery Token / OTP!";
            }
        }
    }

    include "header.php"; 
?>
<div class="glass-wrapper animate-fade">
    <div class="glass-card" style="max-width: 420px; width:100%;">
        <h4 class="fw-bold text-center text-white mb-4">Set New Password</h4>
        
        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-20 text-white text-center rounded-3 small py-2 mb-3">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>" />
            <div class="form-glass-group">
                <label>Enter Reset OTP</label>
                <input type="text" name="otp" class="form-glass-input" required placeholder="0000">
            </div>
            <div class="form-glass-group">
                <label>Enter New Password</label>
                <input type="password" name="new_pass" class="form-glass-input" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-glass-submit">Update Password</button>
        </form>
    </div>
</div>
<?php include "footer.php"; ?>
