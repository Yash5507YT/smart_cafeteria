<?php 
    session_start(); 
    require_once "config.php"; 

    $username = "";
    if (isset($_REQUEST["username"])) {
        $username = $_REQUEST["username"];
    }

    
    $mock_otp = "";
    if ($username != "") {
        $str = "select otp_code from users where username='" . $username . "'";
        $db_res = $link->query($str);
        if ($db_res && $db_res->num_rows == 1) {
            $db_row = $db_res->fetch_assoc();
            $mock_otp = $db_row['otp_code'];
        }
    }

    if (isset($_REQUEST["otp_entered"])) {
        $user_otp = $_REQUEST["otp_entered"];
        
        if ($mock_otp != "" && $user_otp === $mock_otp) {
            $str = "update users set is_verified=1, otp_code=NULL where username='" . $username . "'";
            $link->query($str);
            header("location: login_page.php?msg=Verification Success. You can login now.");
            exit();
        } else {
            $error_msg = "Incorrect OTP Code! Try again.";
        }
    }

    include "header.php"; 
?>
<div class="glass-wrapper animate-fade" style="flex-direction: column; justify-content: center; align-items: center;">
    
    
    <?php if($mock_otp != ""): ?>
        <div class="alert border-0 bg-white bg-opacity-10 text-white text-center mb-3 rounded-3 shadow-sm" style="max-width: 380px; width:100%; font-size: 14px;">
            🔑 Your Verification OTP is: <span class="badge bg-success text-white fs-5 px-3 py-1 ms-1 fw-bold"><?php echo $mock_otp; ?></span>
        </div>
    <?php endif; ?>
    
    <div class="glass-card text-center" style="max-width: 380px; width:100%; padding: 30px;">
        <h4 class="fw-bold mb-4 text-white" style="letter-spacing: -0.5px;">Enter OTP</h4>
        
        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-20 text-white text-center rounded-3 small py-2 mb-3" style="font-size: 13px;">
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($username); ?>" />
            <div class="form-glass-group mb-4">
                <input type="text" name="otp_entered" class="form-glass-input text-center fs-1 fw-bold" placeholder="0000" maxlength="4" required style="letter-spacing: 12px; padding: 10px !important;">
            </div>
            <button type="submit" class="btn btn-glass-submit py-2">Verify</button>
        </form>
    </div>
</div>
<?php include "footer.php"; ?>
