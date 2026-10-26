<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
    if(!isset($_SESSION["loggedin"])) {
        header("location: login_page.php");
        exit;
    }
    require_once "config.php";

    if(!isset($_GET['order_id']) || empty($_GET['order_id'])) {
        header("location: dashboard.php");
        exit;
    }

    $order_id = (int)$_GET['order_id'];
    $query_str = "select orders.*, users.full_name, users.mobile_number from orders
                  join users on orders.user_id = users.id
                  where orders.id = " . $order_id;
    $result = $link->query($query_str);

    if(!$result || $result->num_rows == 0) {
        die("Critical Error: Order invoice node parameter not found inside framework ecosystem.");
    }

    $invoice = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Cafeteria Invoice #<?php echo $invoice['id']; ?></title>
    <style>
        :root { --green:#10b981; --mint:#7ef0c0; --deep:#061b1b; --ink:#d8eeea; --muted:#7f9f9c; --line:rgba(126,240,192,.2); }
        * { box-sizing:border-box; }
        body { min-height:100vh; margin:0; padding:30px 16px; background:radial-gradient(circle at 10% 0,rgba(16,185,129,.24),transparent 30%),linear-gradient(135deg,#061b1b,#082b2b 48%,#0a3046); color:var(--ink); font-family:'Segoe UI',system-ui,sans-serif; font-size:14px; line-height:1.45; }
        .receipt-ticket-box { position:relative; overflow:hidden; max-width:430px; margin:0 auto; padding:30px; border:1px solid rgba(164,255,219,.25); border-radius:24px; background:linear-gradient(145deg,rgba(255,255,255,.12),rgba(255,255,255,.045)); backdrop-filter:blur(22px); box-shadow:0 25px 70px rgba(0,0,0,.28),inset 0 1px rgba(255,255,255,.08); }
        .receipt-ticket-box::after { content:""; position:absolute; width:180px; height:180px; right:-90px; top:-75px; border:1px solid rgba(126,240,192,.18); border-radius:50%; pointer-events:none; }
        .text-center { text-align:center; } .text-right { text-align:right; }
        .brand-mark { display:inline-flex; align-items:center; justify-content:center; width:48px; height:48px; margin-bottom:12px; color:var(--deep); background:var(--green); border-radius:15px; font-size:21px; box-shadow:0 10px 22px rgba(16,185,129,.24); }
        .brand-header-title { color:#f8fafc; font-size:21px; font-weight:800; margin-bottom:2px; letter-spacing:-.4px; }
        .brand-subtitle { color:var(--mint); font-size:12px; font-weight:700; letter-spacing:.6px; }
        .brand-note { color:var(--muted); font-size:11px; margin-top:3px; }
        .divider-line { border-top:1px dashed var(--line); margin:18px 0; }
        .meta-info-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:7px; font-size:13px; }
        .meta-label { color:var(--muted); } .text-success { color:var(--mint) !important; }
        .items-billing-table { width:100%; border-collapse:collapse; margin-top:15px; }
        .items-billing-table th { color:var(--mint); text-align:left; border-bottom:1px dashed var(--line); padding-bottom:8px; font-size:11px; text-transform:uppercase; letter-spacing:.8px; }
        .items-billing-table td { color:#d4e8e4; padding:10px 0; font-size:13px; vertical-align:top; }
        .total-highlight-row { color:#f8fafc; font-size:17px; font-weight:800; border-top:1px dashed var(--line); padding-top:12px; margin-top:15px; }
        .thank-you { color:var(--muted); font-size:11px; margin-top:12px; letter-spacing:.4px; }
        .btn-print-action { display:block; width:100%; max-width:430px; margin:20px auto 0; background:var(--green); color:#04251d; border:1px solid rgba(126,240,192,.6); padding:13px; border-radius:12px; font-weight:800; cursor:pointer; font-family:inherit; text-align:center; box-shadow:0 8px 20px rgba(16,185,129,.18); transition:transform .2s ease,background .2s ease; }
        .btn-print-action:hover { background:#34d399; transform:translateY(-2px); }
        @media print { .btn-print-action { display:none; } body { background:#fff; color:#1e293b; padding:0; } .receipt-ticket-box { border:none; box-shadow:none; background:#fff; color:#1e293b; padding:0; } .brand-header-title,.items-billing-table td,.meta-info-grid { color:#1e293b; } .brand-subtitle { color:#047857; } .receipt-ticket-box::after { display:none; } }
    </style>
</head>
<body>
<div class="receipt-ticket-box">
    <div class="text-center">
        <div class="brand-mark">🌱</div>
        <div class="brand-header-title">SMART CAFETERIA</div>
        <div class="brand-subtitle">Fresh · Pure Veg · Zero Waste</div>
        <div class="brand-note">Digital order invoice</div>
    </div>

    <div class="divider-line"></div>

    <div class="meta-info-grid"><span class="meta-label">Order ID:</span><span class="text-right">#<?php echo $invoice['id']; ?></span></div>
    <div class="meta-info-grid"><span class="meta-label">Customer:</span><span class="text-right"><?php echo htmlspecialchars($invoice['full_name']); ?></span></div>
    <div class="meta-info-grid"><span class="meta-label">Contact:</span><span class="text-right"><?php echo htmlspecialchars($invoice['mobile_number']); ?></span></div>
    <div class="meta-info-grid"><span class="meta-label">Date/Time:</span><span class="text-right" style="font-size:11px;"><?php echo date('d-M-Y H:i', strtotime($invoice['order_date'])); ?></span></div>

    <div class="divider-line"></div>

    <table class="items-billing-table">
        <thead><tr><th>Ordered food items</th></tr></thead>
        <tbody><tr><td style="white-space:pre-line;letter-spacing:.5px;"><?php echo str_replace(", ", "\n", htmlspecialchars($invoice['item_list'])); ?></td></tr></tbody>
    </table>

    <div class="divider-line"></div>

    <div class="meta-info-grid"><span class="meta-label">Payment Mode:</span><span class="text-right" style="text-transform:uppercase;font-weight:700;"><?php echo htmlspecialchars($invoice['payment_mode']); ?></span></div>
    <div class="meta-info-grid"><span class="meta-label">Points Redeemed:</span><span class="text-right">-₹<?php echo number_format($invoice['points_redeemed'], 2); ?></span></div>
    <div class="meta-info-grid"><span class="meta-label">Zero-Waste Pledge:</span><span class="text-right"><?php echo ($invoice['zero_waste_pledge'] == 1) ? 'Applied (-5%)' : 'None'; ?></span></div>

    <div class="total-highlight-row meta-info-grid"><span>TOTAL PAID</span><span class="text-right text-success">₹<?php echo number_format($invoice['total_price'], 2); ?></span></div>

    <div class="divider-line" style="margin-bottom:5px;"></div>
    <div class="text-center thank-you">Thank you for choosing fresh campus food.<br>🌱 Green points credited to your wallet.</div>
</div>

<button class="btn-print-action" onclick="window.print()">🖨 Print Invoice</button>
<script>
    window.addEventListener('DOMContentLoaded', () => { setTimeout(() => { window.print(); }, 500); });
</script>
</body>
</html>
