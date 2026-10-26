<?php
    session_start();
    require_once "config.php";
    include "header.php";
?>
<style>
    :root {
        --eco-night: #061b1b;
        --eco-deep: #082b2b;
        --eco-blue: #0f4c81;
        --eco-green: #10b981;
        --eco-mint: #7ef0c0;
        --eco-text: #f8fafc;
        --eco-muted: #a7c3c1;
        --eco-line: rgba(164, 255, 219, 0.16);
    }

    html, body { min-height: 100%; margin: 0; }

    body {
        background: #061b1b !important;
        color: var(--eco-text) !important;
    }

    .master-wrapper-node {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        background:
            radial-gradient(circle at 9% 8%, rgba(16, 185, 129, .27) 0, rgba(16, 185, 129, 0) 26%),
            radial-gradient(circle at 92% 20%, rgba(56, 189, 248, .18) 0, rgba(56, 189, 248, 0) 28%),
            radial-gradient(circle at 53% 78%, rgba(34, 197, 94, .12) 0, rgba(34, 197, 94, 0) 30%),
            linear-gradient(135deg, var(--eco-night) 0%, var(--eco-deep) 44%, #0a3046 100%) !important;
        color: var(--eco-text) !important;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        width: 100%;
        box-sizing: border-box;
    }

    .master-wrapper-node::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: -2;
        pointer-events: none;
        opacity: .42;
        background-image:
            linear-gradient(115deg, transparent 0 48%, rgba(126, 240, 192, .07) 49%, transparent 50%),
            linear-gradient(35deg, transparent 0 48%, rgba(126, 240, 192, .055) 49%, transparent 50%),
            radial-gradient(circle, rgba(255,255,255,.18) 0 1px, transparent 1.5px);
        background-size: 170px 170px, 210px 210px, 34px 34px;
        mask-image: linear-gradient(to bottom, rgba(0,0,0,.7), transparent 92%);
    }

    .master-wrapper-node::after {
        content: "";
        position: absolute;
        z-index: -1;
        width: 620px;
        height: 620px;
        right: -220px;
        top: 115px;
        border: 1px solid rgba(126, 240, 192, .18);
        border-radius: 48% 52% 64% 36% / 42% 38% 62% 58%;
        box-shadow:
            0 0 0 22px rgba(16,185,129,.035),
            0 0 0 58px rgba(16,185,129,.025),
            inset 0 0 90px rgba(16,185,129,.1);
        transform: rotate(20deg);
        pointer-events: none;
    }

    .master-wrapper-node > * { position: relative; z-index: 1; }

    .pre-hero-panel {
        max-width: 1200px;
        margin: 40px auto;
        padding: 40px 20px;
        width: 100%;
        text-align: center;
        border-bottom: 1px solid var(--eco-line);
    }

    .pre-hero-panel h4 { color: var(--eco-mint) !important; }

    .btn-contact-ui {
        background: rgba(16, 185, 129, .12);
        color: var(--eco-mint);
        border: 1px solid rgba(126, 240, 192, .58);
        padding: 10px 24px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all .25s ease;
        box-shadow: 0 0 22px rgba(16,185,129,.1);
    }
    .btn-contact-ui:hover { background: var(--eco-green); color: #04251d; border-color: var(--eco-green); transform: translateY(-2px); }

    .main-hero-panel {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        width: 100%;
        box-sizing: border-box;
    }

    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 22px;
        padding: 8px 14px;
        color: var(--eco-mint);
        background: rgba(16,185,129,.1);
        border: 1px solid rgba(126,240,192,.2);
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 1.1px;
        text-transform: uppercase;
    }
    .hero-kicker-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--eco-green); box-shadow: 0 0 13px var(--eco-green); }

    .giant-headline-text {
        max-width: 720px;
        font-size: clamp(44px, 6vw, 76px);
        font-weight: 800;
        line-height: .98;
        letter-spacing: -2.8px;
        margin-bottom: 28px;
        color: var(--eco-text);
        text-align: left;
        text-shadow: 0 8px 35px rgba(0,0,0,.25);
    }
    .headline-highlight { color: var(--eco-mint); }

    .hero-support-copy {
        max-width: 560px;
        margin: 0 0 34px;
        color: var(--eco-muted);
        font-size: 16px;
        line-height: 1.7;
    }

    .auth-trigger-container {
        display: flex;
        gap: 15px;
        margin-bottom: 60px;
        justify-content: flex-start;
    }
    .btn-auth-simple {
        padding: 14px 35px;
        font-size: 15px;
        font-weight: 700;
        border-radius: 8px;
        text-decoration: none;
        transition: all .25s ease;
        text-transform: uppercase;
        letter-spacing: .5px;
    }
    .btn-auth-login { background: var(--eco-green); color: #04251d; box-shadow: 0 10px 30px rgba(16,185,129,.2); }
    .btn-auth-login:hover { background: #34d399; color: #04251d; transform: translateY(-2px); box-shadow: 0 14px 32px rgba(16,185,129,.32); }
    .btn-auth-register { background: rgba(255,255,255,.04); color: var(--eco-text); border: 1px solid rgba(255,255,255,.3); }
    .btn-auth-register:hover { background: rgba(255,255,255,.12); color: #fff; border-color: var(--eco-mint); transform: translateY(-2px); }

    .cafeteria-features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 22px;
        margin-bottom: 80px;
        text-align: left;
    }
    .feature-node-cell {
        position: relative;
        min-height: 180px;
        overflow: hidden;
        border: 1px solid rgba(164,255,219,.14);
        border-radius: 20px;
        padding: 24px;
        background: linear-gradient(145deg, rgba(255,255,255,.085), rgba(255,255,255,.025));
        box-shadow: inset 0 1px 0 rgba(255,255,255,.06), 0 18px 50px rgba(0,0,0,.12);
        backdrop-filter: blur(12px);
        transition: transform .25s ease, border-color .25s ease, background .25s ease;
    }
    .feature-node-cell::after {
        content: "";
        position: absolute;
        width: 115px;
        height: 115px;
        right: -42px;
        bottom: -42px;
        border: 1px solid rgba(126,240,192,.16);
        border-radius: 50%;
    }
    .feature-node-cell:hover { transform: translateY(-5px); border-color: rgba(126,240,192,.4); background: linear-gradient(145deg, rgba(16,185,129,.14), rgba(255,255,255,.035)); }
    .feature-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        margin-bottom: 20px;
        color: var(--eco-mint);
        background: rgba(16,185,129,.14);
        border: 1px solid rgba(126,240,192,.2);
        border-radius: 11px;
        font-size: 16px;
    }
    .feature-node-cell h6 { font-size: 16px; font-weight: 800; color: var(--eco-mint); margin: 0 0 10px; }
    .feature-node-cell p { font-size: 13px; color: var(--eco-muted); line-height: 1.65; margin: 0; }

    .premium-corporate-footer {
        background: rgba(3, 22, 28, .62) !important;
        border-top: 1px solid var(--eco-line);
        padding: 60px 40px 30px;
        margin-top: auto;
        font-size: 14px;
        width: 100% !important;
        display: block !important;
        box-sizing: border-box;
        backdrop-filter: blur(18px);
    }
    .footer-flex-row { display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; justify-content: space-between !important; align-items: flex-start !important; gap: 30px; max-width: 1200px; margin: 0 auto; }
    .footer-flex-col { flex: 1; min-width: 200px; text-align: left !important; }
    .footer-brand-sig { font-size: 16px; font-weight: 700; color: var(--eco-mint); margin-bottom: 15px; }
    .footer-brand-desc { font-size: 13px; color: #82a5a1; line-height: 1.6; }
    .footer-input-minimal { background: rgba(255,255,255,.04) !important; border: 1px solid rgba(255,255,255,.14) !important; border-radius: 8px !important; padding: 10px 12px !important; color: #fff !important; font-size: 14px; margin-bottom: 12px; width: 100%; }
    .footer-input-minimal:focus { border-color: var(--eco-green) !important; box-shadow: 0 0 0 3px rgba(16,185,129,.1) !important; outline: none; }
    .btn-minimal-submit { background: var(--eco-green); color: #04251d; font-weight: 800; font-size: 13px; border-radius: 8px; padding: 10px 16px; border: none; width: 100%; text-align: center; transition: transform .2s ease, background .2s ease; }
    .btn-minimal-submit:hover { background: #34d399; transform: translateY(-2px); }
    .footer-col-header { font-size: 12px; font-weight: 800; text-transform: uppercase; color: var(--eco-mint); letter-spacing: 1px; margin-bottom: 20px; }
    .footer-menu-links { list-style: none; padding: 0; margin: 0; }
    .footer-menu-links li { margin-bottom: 12px; text-align: left !important; }
    .footer-menu-links a { color: #9bb7b5; text-decoration: none; transition: color .2s; font-size: 13px; display: inline-block; }
    .footer-menu-links a:hover { color: var(--eco-mint); }
    .footer-baseline-bar { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; max-width: 1200px; margin: 50px auto 0; padding-top: 25px; border-top: 1px solid rgba(164,255,219,.1); font-size: 13px; color: #6f918e; }
    .footer-baseline-bar a { color: #8eb0ac !important; }
    .footer-baseline-bar a:hover { color: var(--eco-mint) !important; }

    @media (max-width: 700px) {
        .pre-hero-panel { margin: 18px auto; padding: 28px 18px; }
        .pre-hero-panel h4 { font-size: 18px !important; }
        .main-hero-panel { margin: 30px auto; padding: 0 18px; }
        .giant-headline-text { letter-spacing: -1.5px; }
        .hero-support-copy { font-size: 14px; }
        .auth-trigger-container { margin-bottom: 42px; }
        .btn-auth-simple { padding: 12px 24px; }
        .cafeteria-features-grid { grid-template-columns: 1fr; margin-bottom: 50px; }
        .premium-corporate-footer { padding: 42px 22px 25px; }
        .footer-flex-row { gap: 26px; }
        .footer-flex-col { min-width: 145px; }
        .footer-baseline-bar { margin-top: 34px; }
    }
</style>

<div class="master-wrapper-node">
    <div class="pre-hero-panel">
        <h4 class="text-white mb-3" style="font-weight: 400; font-size: 24px;">Do you want to learn more about the project?</h4>
        <a href="mailto:hello@smartcafeteria.com" class="btn-contact-ui">Contact us →</a>
    </div>

    <div class="main-hero-panel animate-fade">
        <div class="hero-kicker"><span class="hero-kicker-dot"></span> Pure Veg · Zero Waste · Campus Fresh</div>
        <h1 class="giant-headline-text">Aim high.<br><span class="headline-highlight">Eat healthy.</span></h1>
        <p class="hero-support-copy">A smarter campus cafeteria experience with fresh vegetarian meals, sustainable choices, and rewards for every responsible order.</p>

        <div class="auth-trigger-container">
            <a href="login_page.php" class="btn-auth-simple btn-auth-login">Login</a>
            <a href="register.php" class="btn-auth-simple btn-auth-register">Register</a>
        </div>

        <div class="cafeteria-features-grid">
            <div class="feature-node-cell">
                <span class="feature-icon"><i class="fa-solid fa-leaf"></i></span>
                <h6>100% Pure Veg & Hygiene</h6>
                <p>Every item is processed under strict quality control guidelines using high-grade automated eco terminals to secure maximum sanitation parameters.</p>
            </div>
            <div class="feature-node-cell">
                <span class="feature-icon"><i class="fa-solid fa-recycle"></i></span>
                <h6>Zero-Waste Rewards Ledger</h6>
                <p>Take the carbon-offset pledge on your self-service screen to get instant resource discount points credited straight into your integrated digital wallet.</p>
            </div>
            <div class="feature-node-cell">
                <span class="feature-icon"><i class="fa-solid fa-chart-line"></i></span>
                <h6>Live Campus Cashflow Node</h6>
                <p>A fully centralized back-office ecosystem dashboard allows real-time collection metrics and student node analytics tracking parameter values.</p>
            </div>
        </div>
    </div>

    <footer class="premium-corporate-footer">
        <div class="footer-flex-row">
            <div class="footer-flex-col" style="flex: 1.5;">
                <div class="footer-brand-sig">Smart Cafeteria ®</div>
                <div class="footer-brand-desc">Creating meaningful campus cafeteria experiences since 2026. Always striving, never settling.</div>
            </div>

            <div class="footer-flex-col">
                <div class="footer-col-header">Food for thought</div>
                <form action="" method="post" onsubmit="event.preventDefault();">
                    <input type="email" class="footer-input-minimal" placeholder="Your e-mail" required />
                    <button type="submit" class="btn-minimal-submit">Get My Insights →</button>
                </form>
            </div>

            <div class="footer-flex-col">
                <div class="footer-col-header">Company</div>
                <ul class="footer-menu-links">
                    <li><a href="#">Services Platform</a></li>
                    <li><a href="#">Case Studies Node</a></li>
                    <li><a href="#">Ecosystem Projects</a></li>
                    <li><a href="#">Contact Root</a></li>
                </ul>
            </div>

            <div class="footer-flex-col">
                <div class="footer-col-header">Resources</div>
                <ul class="footer-menu-links">
                    <li><a href="#">Public Platforms</a></li>
                    <li><a href="#">E-Learning Academy</a></li>
                    <li><a href="#">Digital Tech 101</a></li>
                    <li><a href="#">System Blog Hub</a></li>
                </ul>
            </div>

            <div class="footer-flex-col">
                <div class="footer-col-header">Case Studies</div>
                <ul class="footer-menu-links">
                    <li><a href="#">Veggie Kiosk</a></li>
                    <li><a href="#">Green Wallet</a></li>
                    <li><a href="#">Central Panel</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-baseline-bar">
            <div>
                <span class="me-3">WeWork, Surat, Gujarat, India</span>
                <span>hello@smartcafeteria.com</span>
            </div>
            <div>
                <a href="#" class="text-muted text-decoration-none me-3">Privacy Policy</a>
                <a href="#" class="text-muted text-decoration-none">Cookie Settings</a>
            </div>
        </div>
    </footer>
</div>

</body>
</html>
