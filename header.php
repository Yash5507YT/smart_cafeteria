<?php
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zero-Waste Smart Cafeteria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        :root {
            --eco-night: #061b1b;
            --eco-deep: #082b2b;
            --eco-blue: #0f4c81;
            --eco-green: #10b981;
            --eco-mint: #7ef0c0;
            --eco-white: #f8fafc;
            --eco-muted: #a7c3c1;
            --eco-border: rgba(164, 255, 219, 0.16);
            --glass-bg: rgba(255, 255, 255, 0.07);
            --glass-border: rgba(164, 255, 219, 0.18);
            --text-white: #ffffff;
        }

        *, *::before, *::after { box-sizing: border-box; }
        html, body { min-height: 100%; margin: 0; }
        body {
            background:
                radial-gradient(circle at 8% 5%, rgba(16,185,129,.23), transparent 28%),
                radial-gradient(circle at 92% 18%, rgba(56,189,248,.17), transparent 30%),
                linear-gradient(135deg, var(--eco-night), var(--eco-deep) 45%, #0a3046);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: var(--eco-white);
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
            position: relative;
        }
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            opacity: .28;
            background-image: radial-gradient(circle, rgba(255,255,255,.17) 0 1px, transparent 1.4px);
            background-size: 34px 34px;
            mask-image: linear-gradient(to bottom, rgba(0,0,0,.65), transparent 92%);
            z-index: -3;
        }

        .abstract-shape-1, .abstract-shape-2 {
            position: fixed;
            pointer-events: none;
            z-index: -2;
            filter: blur(58px);
            opacity: .24;
        }
        .abstract-shape-1 {
            width: 390px; height: 390px;
            background: linear-gradient(45deg, #10b981, #34d399);
            border-radius: 43% 57% 70% 30% / 45% 45% 55% 55%;
            top: 6%; left: -12%;
            transform: rotate(-18deg);
        }
        .abstract-shape-2 {
            width: 460px; height: 460px;
            background: linear-gradient(135deg, #0e7490, #2563eb);
            border-radius: 50% 50% 30% 70% / 50% 60% 40% 50%;
            bottom: -10%; right: -12%;
        }

        a { color: var(--eco-mint); }
        a:hover { color: #b5ffe1; }
        .text-white-50 { color: rgba(226, 245, 241, .62) !important; }
        .text-muted { color: rgba(185, 214, 210, .62) !important; }
        .text-success { color: var(--eco-mint) !important; }
        .bg-dark { background-color: rgba(1, 20, 28, .75) !important; }
        .border-secondary { border-color: rgba(164,255,219,.2) !important; }

        .glass-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            min-height: calc(100vh - 80px);
            padding: 46px 20px;
        }
        .glass-card {
            position: relative;
            overflow: hidden;
            background: linear-gradient(145deg, rgba(255,255,255,.105), rgba(255,255,255,.035));
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 20, 23, .28), inset 0 1px 0 rgba(255,255,255,.07);
            padding: 40px;
            width: 100%;
            max-width: 420px;
        }
        .glass-card::after {
            content: "";
            position: absolute;
            width: 130px; height: 130px;
            right: -54px; bottom: -58px;
            border: 1px solid rgba(126,240,192,.16);
            border-radius: 50%;
            pointer-events: none;
        }
        .brand-logo-img {
            width: 85px; height: 85px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(126, 240, 192, .55);
            box-shadow: 0 0 25px rgba(16,185,129,.2);
            margin: 0 auto 15px;
            display: block;
            background-color: #0b2528;
        }
        .form-glass-group { margin-bottom: 20px; text-align: left; }
        .form-glass-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
            color: rgba(234, 250, 246, .8);
        }
        .form-glass-input {
            width: 100% !important;
            background: rgba(3, 25, 30, .48) !important;
            border: 1px solid rgba(164,255,219,.2) !important;
            border-radius: 12px !important;
            padding: 12px 16px !important;
            color: #ffffff !important;
            font-size: 15px !important;
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }
        .form-glass-input::placeholder { color: rgba(210,235,229,.42); }
        .form-glass-input:focus {
            outline: none;
            background: rgba(3, 25, 30, .7) !important;
            border-color: var(--eco-green) !important;
            box-shadow: 0 0 0 3px rgba(16,185,129,.12) !important;
        }
        .form-glass-input:invalid:not(:placeholder-shown) { border-color: #fb7185 !important; box-shadow: 0 0 0 3px rgba(244,63,94,.1) !important; }
        .form-glass-input:valid:not(:placeholder-shown) { border-color: rgba(126,240,192,.62) !important; }
        .validation-hint { display:none; color:#fda4af; font-size:11px; margin-top:6px; }
        .form-glass-input:invalid:not(:placeholder-shown) + .validation-hint { display:block; }
        .btn-glass-submit {
            background: var(--eco-green);
            color: #04251d;
            border: 1px solid rgba(126,240,192,.6);
            border-radius: 12px;
            padding: 12px;
            font-weight: 800;
            width: 100%;
            transition: all .25s ease;
        }
        .btn-glass-submit:hover { background: #34d399; color: #04251d; transform: translateY(-2px); box-shadow: 0 12px 25px rgba(16,185,129,.2); }
        .btn-glass-submit:disabled { opacity: .55; transform: none; }
        .animate-fade { animation: fadeEffect .5s ease-out both; }
        @keyframes fadeEffect { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        .glass-navbar {
            background: rgba(3, 29, 34, .58);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            border-bottom: 1px solid var(--eco-border);
            padding: 15px 0;
        }
        .btn-logout-custom {
            background: rgba(239,68,68,.12);
            color: #fecaca;
            border: 1px solid rgba(239,68,68,.35);
            border-radius: 10px;
            padding: 8px 18px;
            font-weight: 700;
            transition: all .2s;
            text-decoration: none;
        }
        .btn-logout-custom:hover { background: rgba(239,68,68,.35); color: #fff; transform: translateY(-1px); }
        .site-footer { background: rgba(3, 22, 28, .62); border-top: 1px solid var(--eco-border); padding: 16px 12px; backdrop-filter: blur(18px); }
        .alert { backdrop-filter: blur(12px); }
        .form-check-input:focus { box-shadow: 0 0 0 3px rgba(16,185,129,.14); }
        .form-select { color-scheme: dark; }

        @media (max-width: 560px) {
            .glass-wrapper { padding: 26px 14px; }
            .glass-card { padding: 28px 22px; border-radius: 20px; }
        }
    </style>
</head>
<body>
<div class="abstract-shape-1"></div>
<div class="abstract-shape-2"></div>
