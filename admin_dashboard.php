<?php
    session_start();
    require_once "config.php";
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");

    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login_page.php");
        exit;
    }

    $admin_id = (int)$_SESSION["user_id"];
    $check_admin = $link->query("select is_admin from users where id = " . $admin_id);
    $admin_row = $check_admin ? $check_admin->fetch_assoc() : null;
    if (!$admin_row || (int)$admin_row["is_admin"] !== 1) {
        header("location: dashboard.php");
        exit;
    }

    $allowed_statuses = [
        "pending" => "Pending",
        "confirmed" => "Confirmed",
        "preparing" => "Preparing",
        "ready" => "Ready for Pickup",
        "completed" => "Completed / Done",
        "cancelled" => "Cancelled",
        "refund_requested" => "Refund Requested",
        "refunded" => "Refunded"
    ];

    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_order_status"])) {
        $order_id = (int)($_POST["order_id"] ?? 0);
        $order_status = $_POST["order_status"] ?? "pending";
        $admin_note = trim($_POST["admin_note"] ?? "");
        if ($order_id > 0 && array_key_exists($order_status, $allowed_statuses)) {
            $stmt = $link->prepare("update orders set order_status = ?, admin_note = ?, status_updated_at = NOW() where id = ?");
            $stmt->bind_param("ssi", $order_status, $admin_note, $order_id);
            $stmt->execute();
            header("location: admin_dashboard.php?msg=Order%20%23" . $order_id . "%20status%20updated");
            exit;
        }
        header("location: admin_dashboard.php?msg=Invalid%20order%20status");
        exit;
    }

    if (isset($_GET["remove_user_id"])) {
        $del_uid = (int)$_GET["remove_user_id"];
        if ($del_uid > 0) $link->query("delete from users where id = " . $del_uid . " and is_admin = 0");
        header("location: admin_dashboard.php?msg=User%20removed%20successfully");
        exit;
    }

    $active_res = $link->query("select count(*) as count from users where is_admin=0 and last_activity >= NOW() - INTERVAL 5 MINUTE");
    $active_users = $active_res ? (int)$active_res->fetch_assoc()["count"] : 0;
    $inactive_res = $link->query("select count(*) as count from users where is_admin=0 and (last_activity < NOW() - INTERVAL 5 MINUTE or last_activity IS NULL)");
    $inactive_users = $inactive_res ? (int)$inactive_res->fetch_assoc()["count"] : 0;
    $daily_sales_res = $link->query("select sum(total_price) as total from orders where DATE(order_date) = CURDATE() and order_status not in ('cancelled', 'refunded')");
    $daily_collection = $daily_sales_res ? ($daily_sales_res->fetch_assoc()["total"] ?? 0) : 0;
    $order_count_res = $link->query("select count(*) as total from orders");
    $total_orders = $order_count_res ? (int)$order_count_res->fetch_assoc()["total"] : 0;
    $pending_res = $link->query("select count(*) as total from orders where order_status in ('pending','confirmed','preparing','ready')");
    $active_orders = $pending_res ? (int)$pending_res->fetch_assoc()["total"] : 0;

    include "header.php";
?>
<style>
    .admin-wrapper { max-width: 1280px; margin: 30px auto; padding: 0 20px 30px; flex-grow: 1; width: 100%; box-sizing: border-box; }
    .admin-topbar { background: rgba(3,29,34,.6); border: 1px solid rgba(164,255,219,.14); border-radius: 18px; padding: 15px 20px; backdrop-filter: blur(18px); }
    .admin-brand { color: #f8fafc; text-decoration: none; font-size: 20px; letter-spacing: -.4px; }
    .admin-brand span { color: #7ef0c0; }
    .admin-title-row { display:flex; align-items:center; justify-content:space-between; gap:20px; margin: 28px 0 24px; }
    .admin-eyebrow { color:#7ef0c0; font-size:11px; font-weight:800; letter-spacing:1.3px; text-transform:uppercase; margin-bottom:8px; }
    .admin-title { font-size:clamp(24px, 3vw, 34px); letter-spacing:-1px; }
    .admin-mode-badge { background:rgba(16,185,129,.14); color:#7ef0c0; border:1px solid rgba(126,240,192,.25); border-radius:999px; padding:9px 13px; font-size:11px; font-weight:800; white-space:nowrap; }
    .stat-card { height:100%; background:linear-gradient(145deg,rgba(255,255,255,.1),rgba(255,255,255,.035)); backdrop-filter:blur(20px); border:1px solid rgba(164,255,219,.16); border-radius:18px; padding:22px; text-align:left; box-shadow:inset 0 1px rgba(255,255,255,.05); transition:transform .2s ease,border-color .2s ease; }
    .stat-card:hover { transform:translateY(-4px); border-color:rgba(126,240,192,.4); }
    .stat-label { color:#9bb7b5; font-size:11px; font-weight:800; letter-spacing:.8px; }
    .stat-value { margin:10px 0 4px; font-size:30px; color:#f8fafc; }
    .stat-note { color:#72918e; font-size:12px; }
    .stat-icon { width:38px; height:38px; display:flex; align-items:center; justify-content:center; border-radius:12px; background:rgba(16,185,129,.14); color:#7ef0c0; margin-bottom:16px; }
    .table-shell { background:rgba(255,255,255,.045); backdrop-filter:blur(20px); border:1px solid rgba(164,255,219,.14); border-radius:18px; overflow:hidden; box-shadow:0 20px 60px rgba(0,18,25,.16); }
    .table-glass { color:white; margin:0; min-width:920px; }
    .table-glass th { background:rgba(1,20,28,.56); color:#7ef0c0; border:none; padding:15px; font-size:11px; letter-spacing:.7px; text-transform:uppercase; white-space:nowrap; }
    .table-glass td { background:transparent; color:#c6dcda; border-bottom:1px solid rgba(164,255,219,.08); padding:13px 15px; vertical-align:middle; font-size:13px; }
    .table-glass tbody tr:last-child td { border-bottom:none; }
    .table-glass tbody tr:hover td { background:rgba(16,185,129,.06); }
    .table-section-heading { color:#f8fafc; font-size:18px; font-weight:800; margin:0; }
    .admin-danger-btn { border-color:rgba(248,113,113,.4) !important; color:#fda4af !important; }
    .admin-danger-btn:hover { background:rgba(239,68,68,.18) !important; color:#fff !important; }
    .order-status-form { min-width:260px; }
    .order-status-form select, .order-status-form input { background:rgba(1,20,28,.72); color:#fff; border:1px solid rgba(164,255,219,.18); border-radius:8px; font-size:12px; padding:7px 9px; }
    .order-status-form select:focus, .order-status-form input:focus { outline:none; border-color:#7ef0c0; box-shadow:0 0 0 3px rgba(16,185,129,.1); }
    .status-badge { border-radius:999px; padding:6px 9px; font-size:10px; font-weight:800; white-space:nowrap; }
    .status-pending,.status-confirmed { background:rgba(251,191,36,.13); color:#fde68a; }
    .status-preparing,.status-ready { background:rgba(56,189,248,.13); color:#bae6fd; }
    .status-completed { background:rgba(16,185,129,.15); color:#86efac; }
    .status-cancelled,.status-refunded { background:rgba(248,113,113,.14); color:#fda4af; }
    .status-refund_requested { background:rgba(192,132,252,.15); color:#e9d5ff; }
    @media (max-width: 700px) { .admin-title-row { align-items:flex-start; flex-direction:column; } .admin-mode-badge { align-self:flex-start; } }
</style>

<nav class="glass-navbar"><div class="container d-flex justify-content-between align-items-center"><a class="admin-brand fw-bold m-0" href="admin_dashboard.php"><span><i class="fa-solid fa-leaf"></i></span> Smart Cafeteria <small class="text-white-50">/ Control Center</small></a><a href="logout.php" class="btn-logout-custom"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></div></nav>
<div class="admin-wrapper animate-fade">
    <div class="admin-title-row"><div><div class="admin-eyebrow">Green campus operations</div><h2 class="admin-title fw-bold text-white m-0">Systems Control Center</h2></div><span class="admin-mode-badge"><i class="fa-solid fa-shield-halved me-1"></i> Ecosystem Root Mode</span></div>
    <?php if (isset($_GET["msg"])): ?><div class="alert border-0 bg-success bg-opacity-10 text-white p-3 rounded-3 text-center mb-4 small"><i class="fa-solid fa-circle-check text-success me-1"></i> <?php echo htmlspecialchars($_GET["msg"]); ?></div><?php endif; ?>
    <div class="row mb-5 g-4">
        <div class="col-md-3"><div class="stat-card"><div class="stat-icon"><i class="fa-solid fa-signal"></i></div><div class="stat-label">ACTIVE USERS</div><div class="stat-value fw-bold"><?php echo $active_users; ?></div><div class="stat-note">Active within last 5 minutes</div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div><div class="stat-label">OPEN ORDERS</div><div class="stat-value fw-bold"><?php echo $active_orders; ?></div><div class="stat-note">Pending to ready queue</div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="stat-icon"><i class="fa-solid fa-indian-rupee-sign"></i></div><div class="stat-label">DAILY COLLECTION</div><div class="stat-value fw-bold">₹<?php echo number_format((float)$daily_collection, 2); ?></div><div class="stat-note">Excludes cancelled/refunded</div></div></div>
        <div class="col-md-3"><div class="stat-card"><div class="stat-icon"><i class="fa-solid fa-box-open"></i></div><div class="stat-label">TOTAL ORDERS</div><div class="stat-value fw-bold"><?php echo $total_orders; ?></div><div class="stat-note">Lifetime order records</div></div></div>
    </div>

    <div class="d-flex justify-content-between align-items-end gap-3 mb-3"><div><div class="admin-eyebrow mb-1">Order operations</div><h5 class="table-section-heading">Order Status Management</h5></div><span class="text-white-50 small"><i class="fa-solid fa-list-check me-1"></i> Update every order from this panel</span></div>
    <div class="table-responsive table-shell mb-5"><table class="table table-glass"><thead><tr><th>Order</th><th>Customer</th><th>Items</th><th>Total</th><th>Placed At</th><th>Current Status</th><th>Admin Controls</th></tr></thead><tbody>
    <?php
        $orders = $link->query("select o.id, o.item_list, o.total_price, o.order_date, o.order_status, o.admin_note, u.full_name, u.email from orders o join users u on o.user_id = u.id order by o.order_date desc");
        if ($orders && $orders->num_rows > 0) {
            while ($order = $orders->fetch_assoc()) {
                $current_status = array_key_exists($order["order_status"], $allowed_statuses) ? $order["order_status"] : "pending";
                $status_class = "status-" . $current_status;
    ?>
        <tr>
            <td class="fw-bold text-white">#<?php echo (int)$order["id"]; ?></td>
            <td><div class="fw-bold text-white"><?php echo htmlspecialchars($order["full_name"]); ?></div><small class="text-white-50"><?php echo htmlspecialchars($order["email"]); ?></small></td>
            <td style="max-width:230px;"><span class="small"><?php echo htmlspecialchars($order["item_list"]); ?></span></td>
            <td class="fw-bold text-success">₹<?php echo number_format((float)$order["total_price"], 2); ?></td>
            <td class="small text-white-50"><?php echo date("d M Y, h:i A", strtotime($order["order_date"])); ?></td>
            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($allowed_statuses[$current_status]); ?></span><?php if (!empty($order["admin_note"])): ?><div class="small text-white-50 mt-1"><i class="fa-regular fa-note-sticky me-1"></i><?php echo htmlspecialchars($order["admin_note"]); ?></div><?php endif; ?></td>
            <td><form method="post" class="order-status-form"><input type="hidden" name="update_order_status" value="1"><input type="hidden" name="order_id" value="<?php echo (int)$order["id"]; ?>"><div class="d-flex gap-2"><select name="order_status" required><?php foreach ($allowed_statuses as $status_key => $status_label): ?><option value="<?php echo $status_key; ?>" <?php echo $status_key === $current_status ? "selected" : ""; ?>><?php echo $status_label; ?></option><?php endforeach; ?></select><button type="submit" class="btn btn-sm btn-success fw-bold"><i class="fa-solid fa-check"></i></button></div><input class="w-100 mt-2" type="text" name="admin_note" maxlength="255" placeholder="Optional admin note" value="<?php echo htmlspecialchars($order["admin_note"] ?? ""); ?>"></form></td>
        </tr>
    <?php }
        } else { echo '<tr><td colspan="7" class="text-center text-white-50 py-4">No orders available yet.</td></tr>'; }
    ?>
    </tbody></table></div>

    <div class="d-flex justify-content-between align-items-end gap-3 mb-3"><div><div class="admin-eyebrow mb-1">People and participation</div><h5 class="table-section-heading">Student Node Management Environment</h5></div><span class="text-white-50 small"><i class="fa-solid fa-clock me-1"></i> Status window: 5 minutes</span></div>
    <div class="table-responsive table-shell"><table class="table table-glass"><thead><tr><th>ID</th><th>Student Name</th><th>Email Address</th><th class="text-center">Orders Placed</th><th>Green Balance</th><th>Status</th><th>Action</th></tr></thead><tbody>
    <?php
        $user_res = $link->query("select id, full_name, email, reward_points, last_activity from users where is_admin = 0");
        if ($user_res) while ($u_row = $user_res->fetch_assoc()) {
            $u_id_curr = (int)$u_row["id"];
            $order_cnt_res = $link->query("select count(*) as count from orders where user_id = " . $u_id_curr);
            $order_count = $order_cnt_res ? (int)$order_cnt_res->fetch_assoc()["count"] : 0;
            $is_active = !empty($u_row["last_activity"]) && (time() - strtotime($u_row["last_activity"])) <= 300;
    ?>
        <tr><td>#<?php echo $u_id_curr; ?></td><td class="fw-bold text-white"><?php echo htmlspecialchars($u_row["full_name"]); ?></td><td><?php echo htmlspecialchars($u_row["email"]); ?></td><td class="text-center fw-bold text-info"><?php echo $order_count; ?></td><td class="fw-bold text-success"><i class="fa-solid fa-seedling me-1"></i><?php echo (int)$u_row["reward_points"]; ?></td><td><?php if ($is_active): ?><span class="badge bg-success bg-opacity-25 text-success rounded-pill px-3">Active Now</span><?php else: ?><span class="badge bg-secondary bg-opacity-25 text-white-50 rounded-pill px-3">Offline</span><?php endif; ?></td><td><a href="admin_dashboard.php?remove_user_id=<?php echo $u_id_curr; ?>" class="btn btn-sm btn-outline-danger admin-danger-btn px-3 rounded-pill" onclick="return confirm('Are you sure you want to remove this user?');"><i class="fa-solid fa-trash-can me-1"></i>Remove</a></td></tr>
    <?php } ?>
    </tbody></table></div>
</div>
<?php include "footer.php"; ?>
