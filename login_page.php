<?php
    session_start();
    if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) { 
        header("location: dashboard.php"); 
        exit; 
    }
    include "header.php";
?>
<div class="glass-wrapper animate-fade">
    <div class="glass-card">
        <div class="text-center mb-4">
            <img src="logo.png" onerror="this.src='logo.jpg'; this.onerror=null;" class="brand-logo-img" alt="logo">
            <h3 class="fw-bold text-white m-0">Login</h3>
        </div>
        
        <?php if(isset($_REQUEST['logged_out'])): ?>
            <div class="alert alert-success border-0 bg-white bg-opacity-10 text-white small py-2 rounded-3 text-center mb-3">You have been logged out successfully. Please sign in again.</div>
        <?php endif; ?>
        <?php if(isset($_REQUEST['msg'])): ?>
            <div class="alert alert-success border-0 bg-white bg-opacity-10 text-white small py-2 rounded-3 text-center mb-3">
                <?php echo htmlspecialchars($_REQUEST['msg']); ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_REQUEST['error'])): ?>
            <div class="alert alert-danger border-0 bg-danger bg-opacity-20 text-white small py-2 rounded-3 text-center mb-3">
                Invalid Parameters.
            </div>
        <?php endif; ?>

        <form action="login_process.php" method="post">
            <div class="form-glass-group">
                <label>Email / Username</label>
                <input type="text" name="username" class="form-glass-input" required minlength="3" autocomplete="username" placeholder="username@gmail.com">
            </div>
            
            <div class="form-glass-group">
                <label>Password</label>
                <input type="password" name="password" class="form-glass-input" required minlength="6" autocomplete="current-password" placeholder="Password">
            </div>
            
            <div class="text-start mb-4">
                <a href="forget_password.php" class="small text-decoration-none" style="color: rgba(255,255,255,0.6); font-size: 13px;">Forgot Password?</a>
            </div>
            
            <button type="submit" class="btn btn-glass-submit mb-4">Sign in</button>

            <div class="text-center">
                <span class="small" style="color: rgba(255,255,255,0.6); font-size: 13px;">
                    Don't have an account? <a href="register.php" class="text-white text-decoration-none fw-bold ms-1">Register for free</a>
                </span>
            </div>
        </form>
    </div>
</div>
<?php include "footer.php"; ?>
