<?php
    session_start();
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: 0");
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        header("location: login_page.php");
        exit;
    }

    require_once "config.php";

    $u_id = (int) $_SESSION["user_id"];
    $check_role = $link->query("SELECT is_admin, reward_points FROM users WHERE id = " . $u_id);
    $role_row = $check_role ? $check_role->fetch_assoc() : [];

    if (!empty($role_row["is_admin"]) && (int) $role_row["is_admin"] === 1) {
        header("location: admin_dashboard.php");
        exit;
    }

    $current_points = isset($role_row['reward_points']) ? (int) $role_row['reward_points'] : 0;
    $link->query("UPDATE users SET last_activity = NOW() WHERE id = " . $u_id);
    include "header.php";
?>
<style>
    :root {
        --kiosk-blue: #0f4c81;
        --kiosk-green: #10b981;
        --kiosk-glass: rgba(255, 255, 255, 0.06);
        --kiosk-border: rgba(255, 255, 255, 0.1);
    }

    * { box-sizing: border-box; }

    html, body {
        min-height: 100%;
        margin: 0;
        overflow-x: hidden;
    }

    body {
        background-color: var(--kiosk-blue) !important;
        color: #ffffff !important;
        font-family: 'Segoe UI', system-ui, sans-serif;
    }

    .kiosk-global-frame {
        width: 100%;
        min-height: 100vh;
        padding: 24px 32px;
        display: grid;
        grid-template-columns: 250px minmax(0, 1fr);
        gap: 28px;
        position: relative;
    }

    .kiosk-sidebar-fixed {
        position: sticky;
        top: 24px;
        width: 250px;
        max-height: calc(100vh - 48px);
        overflow-y: auto;
        align-self: start;
        background: var(--kiosk-glass) !important;
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border: 1px solid var(--kiosk-border);
        border-radius: 20px;
        padding: 18px 14px;
        z-index: 20;
    }

    .kiosk-logo-area {
        padding: 5px 0 20px;
        text-align: center;
        font-size: 20px;
        font-weight: 900;
        color: var(--kiosk-green);
        letter-spacing: -0.5px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }

    .kiosk-search-box { position: relative; margin-bottom: 18px; width: 100%; }
    .kiosk-search-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid var(--kiosk-border);
        border-radius: 14px;
        padding: 10px 14px 10px 38px;
        font-size: 14px;
        color: #ffffff;
    }
    .kiosk-search-input::placeholder { color: rgba(255, 255, 255, 0.4); }
    .kiosk-search-input:focus { outline: none; border-color: var(--kiosk-green); }
    .kiosk-search-icon { position: absolute; left: 14px; top: 12px; color: rgba(255, 255, 255, 0.4); }

    .category-btn {
        width: 100%;
        text-align: left;
        background: transparent;
        border: none;
        padding: 11px 15px;
        border-radius: 16px;
        color: #ffffff;
        font-weight: 700;
        margin-bottom: 6px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 13px;
        font-size: 14px;
        cursor: pointer;
    }
    .category-btn i { font-size: 17px; width: 22px; color: #ffffff; }
    .category-btn:hover,
    .category-btn.active {
        background: var(--kiosk-green) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.25);
    }

    .kiosk-center-feed {
        min-width: 0;
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .kiosk-feed-header {
        min-height: 52px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
    }

    .kiosk-feed-heading h2 { margin: 0; font-size: clamp(22px, 2vw, 30px); font-weight: 900; }
    .kiosk-feed-heading p { margin: 4px 0 0; color: rgba(255,255,255,.6); font-size: 13px; }

    .btn-open-cart {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        border: 1px solid rgba(255,255,255,.2);
        background: rgba(255,255,255,.1);
        color: #fff;
        border-radius: 14px;
        padding: 11px 16px;
        font-weight: 800;
        cursor: pointer;
        transition: all .2s ease;
        white-space: nowrap;
    }
    .btn-open-cart:hover { background: var(--kiosk-green); transform: translateY(-2px); }
    .cart-count-badge {
        min-width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        border-radius: 999px;
        background: var(--kiosk-green);
        color: #fff;
        font-size: 11px;
    }

    .kiosk-grid-matrix {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
        width: 100%;
        align-items: stretch;
    }

    .kiosk-food-card {
        min-width: 0;
        min-height: 240px;
        background: var(--kiosk-glass) !important;
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--kiosk-border);
        border-radius: 20px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        padding: 15px;
        position: relative;
        transition: transform .2s ease, opacity .35s ease, max-height .35s ease;
    }
    .kiosk-food-card:hover { transform: translateY(-5px); }
    .kiosk-food-card.item-collapsed {
        display: flex;
        opacity: 0;
        visibility: hidden;
        max-height: 0;
        min-height: 0;
        padding-top: 0;
        padding-bottom: 0;
        margin-top: 0;
        margin-bottom: 0;
        border-width: 0;
        transform: translateY(18px) scale(.98);
        pointer-events: none;
        overflow: hidden;
    }
    .kiosk-food-card.item-revealed {
        opacity: 1;
        visibility: visible;
        max-height: 500px;
        transform: translateY(0) scale(1);
        pointer-events: auto;
    }

    .kiosk-bestseller-tag {
        position: absolute;
        top: 12px;
        left: 12px;
        background: var(--kiosk-green);
        color: white;
        font-size: 9px;
        font-weight: 800;
        padding: 4px 9px;
        border-radius: 8px;
        text-transform: uppercase;
        z-index: 2;
    }
    .kiosk-img-banner {
        height: 112px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 52px;
        background: rgba(255,255,255,.03);
        border-radius: 16px;
        margin-bottom: 13px;
        overflow: hidden;
    }
    .kiosk-img-banner img { width: 100%; height: 100%; object-fit: cover; }
    .kiosk-title-text { font-size: 14px; font-weight: 800; color: #ffffff; margin-bottom: 4px; line-height: 1.3; }
    .kiosk-description-text { color: rgba(255,255,255,.55); font-size: 11px; line-height: 1.35; min-height: 29px; margin-bottom: 4px; }
    .kiosk-price-text { font-size: 16px; font-weight: 900; color: var(--kiosk-green); margin-top: auto; }

    .btn-add-basket {
        background: transparent;
        color: #ffffff;
        font-weight: 700;
        border: 1px solid rgba(255,255,255,.3);
        border-radius: 12px;
        padding: 8px;
        width: 100%;
        margin-top: 12px;
        transition: all .2s;
        cursor: pointer;
        display: block;
        text-align: center;
        font-size: 12px;
    }
    .btn-add-basket:hover { background: #ffffff; color: #000000; }

    .view-more-container { text-align: center; margin: 26px 0 4px; width: 100%; }
    .btn-view-more-glass {
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .2);
        color: #ffffff;
        font-weight: 700;
        padding: 11px 34px;
        border-radius: 16px;
        cursor: pointer;
        transition: all .2s;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .btn-view-more-glass:hover { background: rgba(255,255,255,.15); transform: translateY(-2px); }

    .kiosk-cart-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        opacity: 0;
        visibility: hidden;
        transition: opacity .3s ease, visibility .3s ease;
        z-index: 998;
    }
    .kiosk-cart-overlay.is-open { opacity: 1; visibility: visible; }

    .kiosk-cart-fixed {
        position: fixed;
        right: 0;
        top: 0;
        width: min(390px, 94vw);
        height: 100vh;
        overflow-y: auto;
        background: rgba(15, 42, 73, .96) !important;
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        border-left: 1px solid var(--kiosk-border);
        border-radius: 22px 0 0 22px;
        padding: 24px;
        color: white !important;
        z-index: 999;
        transform: translateX(105%);
        transition: transform .35s cubic-bezier(.22,1,.36,1);
        box-shadow: -20px 0 50px rgba(0,0,0,.25);
    }
    .kiosk-cart-fixed.is-open { transform: translateX(0); }
    .cart-drawer-header { display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .btn-close-cart { background: rgba(255,255,255,.08); color:#fff; border:1px solid rgba(255,255,255,.15); width:34px; height:34px; border-radius:10px; cursor:pointer; }
    .cart-item-row { display: flex; justify-content: space-between; align-items: center; gap: 10px; background: rgba(0,0,0,.15); padding: 12px; border-radius: 12px; margin-bottom: 10px; font-size: 14px; border: 1px solid rgba(255, 255, 255, .04); }
    .form-check-input:checked { background-color: var(--kiosk-green) !important; border-color: var(--kiosk-green) !important; }
    .btn-checkout-master { background: #ffffff !important; color: var(--kiosk-blue) !important; font-weight: 800; font-size: 16px; width: 100%; border: none; border-radius: 16px; padding: 15px; transition: all .2s; cursor: pointer; }
    .btn-checkout-master:hover { background: #e2e8f0 !important; transform: translateY(-2px); }
    .toast-popup-window { position: fixed; bottom: 25px; left: 25px; width: 320px; background: rgba(15,23,42,.95); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,.1); border-radius: 16px; padding: 20px; color: white; box-shadow: 0 20px 40px rgba(0,0,0,.4); z-index: 9999; display: none; }
    .btn-bk-logout { background: rgba(239,68,68,.2); color: #ef4444; border: 1px solid rgba(239,68,68,.3); padding: 4px 9px; border-radius: 8px; font-size: 10px; font-weight: 700; text-decoration: none; white-space: nowrap; }
    .btn-bk-logout:hover { background: #ef4444; color: white; }
    .bk-copyright-footer { font-size: 11px; color: rgba(255,255,255,.4); text-align: center; margin-top: 25px; line-height: 1.4; border-top: 1px dashed rgba(255,255,255,.1); padding-top: 15px; }
    .empty-menu-state { grid-column: 1 / -1; text-align:center; padding:60px 20px; color:rgba(255,255,255,.65); background:rgba(255,255,255,.05); border:1px solid var(--kiosk-border); border-radius:20px; }

    @media (max-width: 1250px) {
        .kiosk-global-frame { grid-template-columns: 220px minmax(0, 1fr); gap: 20px; padding: 20px; }
        .kiosk-sidebar-fixed { width: 220px; }
        .kiosk-grid-matrix { grid-template-columns: repeat(3, minmax(0,1fr)); }
    }
    @media (max-width: 900px) {
        .kiosk-global-frame { display:block; padding: 16px; }
        .kiosk-sidebar-fixed { position:relative; top:auto; width:100%; max-height:none; margin-bottom:18px; }
        .kiosk-sidebar-fixed .category-btn { display:inline-flex; width:auto; margin-right:5px; }
        .kiosk-sidebar-fixed .category-btn i { width:auto; }
        .kiosk-grid-matrix { grid-template-columns: repeat(2, minmax(0,1fr)); }
    }
    @media (max-width: 560px) {
        .kiosk-feed-header { align-items:flex-start; }
        .kiosk-feed-heading p { display:none; }
        .btn-open-cart { padding:10px 12px; }
        .kiosk-grid-matrix { grid-template-columns: 1fr; }
        .kiosk-sidebar-fixed .category-btn { padding: 9px 11px; font-size: 12px; }
        .toast-popup-window { left: 16px; right: 16px; bottom: 16px; width:auto; }
    }
</style>

<div class="kiosk-global-frame animate-fade">
    <aside class="kiosk-sidebar-fixed">
        <div class="kiosk-logo-area">
            <span><span style="color:#10b981;">●</span> VEG CAFETERIA</span>
            <a href="logout.php" class="btn-bk-logout"><i class="fa-solid fa-power-off"></i> Exit</a>
        </div>
        <div class="kiosk-search-box">
            <i class="fa-solid fa-magnifying-glass kiosk-search-icon"></i>
            <input type="text" id="kioskGlobalSearch" class="kiosk-search-input" placeholder="Search delicious veg..." autocomplete="off">
        </div>
        <button type="button" class="category-btn active" data-category="all" onclick="filterMenu('all', this)"><i class="fa-solid fa-leaf"></i> All Veg Items</button>
        <button type="button" class="category-btn" data-category="New!" onclick="filterMenu('New!', this)"><i class="fa-solid fa-star text-warning"></i> New!</button>
        <button type="button" class="category-btn" data-category="Coupons" onclick="filterMenu('Coupons', this)"><i class="fa-solid fa-ticket"></i> Coupons</button>
        <button type="button" class="category-btn" data-category="Meals" onclick="filterMenu('Meals', this)"><i class="fa-solid fa-box"></i> Meals</button>
        <button type="button" class="category-btn" data-category="Burgers" onclick="filterMenu('Burgers', this)"><i class="fa-solid fa-hamburger"></i> Burgers</button>
        <button type="button" class="category-btn" data-category="Pocket" onclick="filterMenu('Pocket', this)"><i class="fa-solid fa-sheet-plastic"></i> Pocket</button>
        <button type="button" class="category-btn" data-category="Green Menu" onclick="filterMenu('Green Menu', this)"><i class="fa-solid fa-seedling"></i> Green Menu</button>
        <button type="button" class="category-btn" data-category="Salads" onclick="filterMenu('Salads', this)"><i class="fa-solid fa-plate-wheat"></i> Salads</button>
        <button type="button" class="category-btn" data-category="Sides" onclick="filterMenu('Sides', this)"><i class="fa-solid fa-cookie"></i> Sides</button>
        <button type="button" class="category-btn" data-category="Desserts" onclick="filterMenu('Desserts', this)"><i class="fa-solid fa-ice-cream"></i> Desserts</button>
        <button type="button" class="category-btn" data-category="Drinks" onclick="filterMenu('Drinks', this)"><i class="fa-solid fa-glass-water"></i> Drinks</button>
    </aside>

    <main class="kiosk-center-feed">
        <div class="kiosk-feed-header">
            <div class="kiosk-feed-heading">
                <h2>Fresh Veg Menu</h2>
                <p>Choose your favourite meal and add it to your basket.</p>
            </div>
            <button type="button" class="btn-open-cart" onclick="openCartDrawer()" aria-label="Open basket">
                <i class="fa-solid fa-basket-shopping"></i> My Basket
                <span class="cart-count-badge" id="bkCartCount">0</span>
            </button>
        </div>

        <div class="kiosk-grid-matrix" id="kioskGridContainerBox">
            <?php
                $menu_result = $link->query("SELECT * FROM menu_items");
                $graphics = [
                    'New!' => '✨', 'Coupons' => '🎟️', 'Meals' => '🍱', 'Burgers' => '🍔',
                    'Pocket' => '🌯', 'Green Menu' => '🥗', 'Salads' => '🥗', 'Sides' => '🍟',
                    'Sweets' => '🍦', 'Desserts' => '🍦', 'Drinks' => '🥤'
                ];
                $local_food_images = [
                    'New!' => 'assets/menu/special.jpg',
                    'Coupons' => 'assets/menu/special.jpg',
                    'Meals' => 'assets/menu/meal.jpg',
                    'Burgers' => 'assets/menu/burger.jpg',
                    'Pocket' => 'assets/menu/pocket.jpg',
                    'Green Menu' => 'assets/menu/green_menu.jpg',
                    'Salads' => 'assets/menu/salad.jpg',
                    'Sides' => 'assets/menu/sides.jpg',
                    'Sweets' => 'assets/menu/dessert.jpg',
                    'Desserts' => 'assets/menu/dessert.jpg',
                    'Drinks' => 'assets/menu/drinks.jpg'
                ];
                $name_food_images = [
                    'virgin mojito' => 'assets/menu/mojito.jpg',
                    'mint mojito' => 'assets/menu/mojito.jpg',
                    'mojito' => 'assets/menu/mojito.jpg',
                    'milk shake' => 'assets/menu/milkshake.jpg',
                    'milkshake' => 'assets/menu/milkshake.jpg',
                    'vegetable pizza' => 'assets/menu/pizza.jpg',
                    'pizza' => 'assets/menu/pizza.jpg',
                    'pasta' => 'assets/menu/pasta.jpg',
                    'macaroni' => 'assets/menu/pasta.jpg',
                    'noodles' => 'assets/menu/noodles.jpg',
                    'hakka' => 'assets/menu/noodles.jpg',
                    'chowmein' => 'assets/menu/noodles.jpg',
                    'sandwich' => 'assets/menu/sandwich.jpg',
                    'toast' => 'assets/menu/sandwich.jpg',
                    'dosa' => 'assets/menu/dosa.jpg',
                    'masala dosa' => 'assets/menu/dosa.jpg',
                    'idli' => 'assets/menu/meal.jpg',
                    'samosa' => 'assets/menu/sides.jpg',
                    'paneer tikka' => 'assets/menu/special.jpg',
                    'paneer' => 'assets/menu/special.jpg',
                    'burger' => 'assets/menu/burger.jpg',
                    'wrap' => 'assets/menu/pocket.jpg',
                    'roll' => 'assets/menu/pocket.jpg',
                    'frankie' => 'assets/menu/pocket.jpg',
                    'salad' => 'assets/menu/salad.jpg',
                    'green bowl' => 'assets/menu/green_menu.jpg',
                    'quinoa' => 'assets/menu/green_menu.jpg',
                    'wedge' => 'assets/menu/sides.jpg',
                    'fries' => 'assets/menu/sides.jpg',
                    'dessert' => 'assets/menu/dessert.jpg',
                    'ice cream' => 'assets/menu/dessert.jpg',
                    'coffee' => 'assets/menu/drinks.jpg',
                    'cappuccino' => 'assets/menu/drinks.jpg',
                    'juice' => 'assets/menu/drinks.jpg',
                    'smoothie' => 'assets/menu/drinks.jpg',
                    'cooler' => 'assets/menu/drinks.jpg'
                ];
                $item_counter = 0;

                if ($menu_result && $menu_result->num_rows > 0) {
                    while ($item = $menu_result->fetch_assoc()) {
                        $item_id = (int) ($item['id'] ?? 0);
                        $item_name = (string) ($item['name'] ?? $item['item_name'] ?? 'Veg Item');
                        $item_price = (float) ($item['price'] ?? 0);
                        $item_category = (string) ($item['category'] ?? $item['cat'] ?? 'All Veg Items');
                        $item_description = (string) ($item['description'] ?? $item['details'] ?? 'Freshly prepared vegetarian delight.');
                        $item_image = (string) ($item['image'] ?? $item['image_url'] ?? '');
                        $item_name_key = strtolower(trim($item_name));
                        $item_name_key = preg_replace('/[^a-z0-9]+/', ' ', $item_name_key);
                        $item_name_key = trim($item_name_key);
                        foreach ($name_food_images as $name_key => $name_image) {
                            if (strpos($item_name_key, $name_key) !== false) {
                                $item_image = $name_image;
                                break;
                            }
                        }
                        if ($item_image === '') {
                            $item_slug = trim(preg_replace('/[^a-z0-9]+/', '_', $item_name_key), '_');
                            $custom_item_image = 'assets/menu/' . $item_slug . '.jpg';
                            if (is_file(__DIR__ . '/' . $custom_item_image)) {
                                $item_image = $custom_item_image;
                            }
                        }
                        if ($item_image === '' && isset($local_food_images[$item_category])) {
                            $item_image = $local_food_images[$item_category];
                        }
                        $is_collapsed = $item_counter >= 12;
                        $safe_name = htmlspecialchars($item_name, ENT_QUOTES, 'UTF-8');
                        $safe_category = htmlspecialchars($item_category, ENT_QUOTES, 'UTF-8');
                        $safe_description = htmlspecialchars($item_description, ENT_QUOTES, 'UTF-8');
                        $name_for_js = htmlspecialchars(json_encode($item_name, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP), ENT_QUOTES, 'UTF-8');
                        $icon = $graphics[$item_category] ?? '🥗';
            ?>
                <article class="kiosk-food-card bk-food-card <?php echo $is_collapsed ? 'item-collapsed' : 'item-revealed'; ?>" data-name="<?php echo strtolower($safe_name); ?>" data-category="<?php echo $safe_category; ?>">
                    <?php if (in_array($item_category, ['New!', 'Bestseller'], true)) { ?><span class="kiosk-bestseller-tag"><?php echo $safe_category; ?></span><?php } ?>
                    <div class="kiosk-img-banner">
                        <?php if ($item_image !== '') { ?>
                            <img src="<?php echo htmlspecialchars($item_image, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $safe_name; ?>" loading="lazy">
                        <?php } else { echo $icon; } ?>
                    </div>
                    <div class="kiosk-title-text"><?php echo $safe_name; ?></div>
                    <div class="kiosk-description-text"><?php echo $safe_description; ?></div>
                    <div class="kiosk-price-text">₹<?php echo number_format($item_price, 2); ?></div>
                    <button type="button" class="btn-add-basket" onclick="addNodeItem(<?php echo $item_id; ?>, <?php echo $name_for_js; ?>, <?php echo $item_price; ?>)"><i class="fa-solid fa-plus me-1"></i> Add to Basket</button>
                </article>
            <?php
                        $item_counter++;
                    }
                } else {
                    echo '<div class="empty-menu-state">No menu items are available right now.</div>';
                }
            ?>
        </div>

        <div class="view-more-container" id="viewMoreTriggerBox" <?php echo $item_counter > 12 ? '' : 'style="display:none;"'; ?>>
            <button type="button" class="btn-view-more-glass" onclick="revealAllKioskItems()">
                <i class="fa-solid fa-layer-group"></i> View More Items <i class="fa-solid fa-chevron-down"></i>
            </button>
        </div>
    </main>
</div>

<div class="kiosk-cart-overlay" id="bkCartOverlay" onclick="closeCartDrawer()"></div>
<aside class="kiosk-cart-fixed" id="bkCartDrawer" aria-hidden="true">
    <div class="cart-drawer-header">
        <div>
            <h5 class="fw-bold mb-1">My Basket</h5>
            <p class="small text-white-50 mb-0">🌱 Green Wallet Points: <span class="text-success fw-bold"><?php echo $current_points; ?></span> Pts</p>
        </div>
        <button type="button" class="btn-close-cart" onclick="closeCartDrawer()" aria-label="Close basket"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <hr style="border-color: rgba(255,255,255,.15);">

    <form action="order_process.php" method="post" id="bkFormCheckout">
        <input type="hidden" name="cart_data" id="bkCartInputJson" required>
        <div id="bkCartListContainer" style="min-height:120px; max-height:260px; overflow-y:auto; margin-bottom:20px;"><p class="text-white-50 small text-center my-4">Your basket is empty.</p></div>

        <div class="mb-3 bg-black bg-opacity-20 p-3 rounded-3 border border-secondary border-opacity-20">
            <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="redeem_points" id="bkRedeemSwitch" value="1" onchange="calcBkTotal()"><label class="form-check-label small text-white-50" for="bkRedeemSwitch">Redeem Points (1 Pt = ₹1)</label></div>
            <div class="form-check m-0"><input class="form-check-input" type="checkbox" name="zero_waste" id="bkZwPledge" value="1" checked onchange="calcBkTotal()"><label class="form-check-label small text-white-50" for="bkZwPledge">Zero-Waste Pledge (-5%)</label></div>
        </div>

        <div class="mb-4">
            <label class="small text-white-50 mb-2">Payment Strategy</label>
            <select name="payment_mode" class="form-select bg-dark text-white border-secondary border-opacity-30 small" style="border-radius:10px;" required>
                <option value="Cash">Cash at Counter</option>
                <option value="Online">Online Digital UPI</option>
            </select>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 px-1"><span class="fw-semibold text-white-50">Order Total:</span><span class="fs-3 fw-bold text-success" id="bkTotalDisplay">₹0.00</span></div>
        <button type="submit" class="btn btn-checkout-master" id="bkSubmitBtn" disabled>Confirm Order</button>
    </form>

    <div class="bk-copyright-footer">© 2026 Glassmorphism Ecosystem Panel | Built by BCA Node Group 3</div>
</aside>

<div class="toast-popup-window animate-fade" id="bottomLeftToastBox">
    <div class="fw-bold fs-5 text-white mb-1">Order Confirmed!</div>
    <p class="small text-white-50 mb-3">Your campus terminal deployment request was executed successfully. Your carbon offset points are updated.</p>
    <?php $p_id = (int) ($_GET['order_id'] ?? 0); ?>
    <div class="d-flex gap-2">
        <a href="receipt_print.php?order_id=<?php echo $p_id; ?>" target="_blank" class="btn btn-sm btn-success flex-grow-1 fw-bold rounded-2"><i class="fa-solid fa-print me-1"></i> Print Slip</a>
        <button type="button" class="btn btn-sm btn-outline-light small px-3" onclick="document.getElementById('bottomLeftToastBox').style.display='none'">Close</button>
    </div>
</div>

<script>
    let basket = [];
    let ptsBalance = <?php echo (int) $current_points; ?>;
    let activeCategory = 'all';
    let showAllItems = false;

    function openCartDrawer() {
        document.getElementById('bkCartDrawer').classList.add('is-open');
        document.getElementById('bkCartOverlay').classList.add('is-open');
        document.getElementById('bkCartDrawer').setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeCartDrawer() {
        document.getElementById('bkCartDrawer').classList.remove('is-open');
        document.getElementById('bkCartOverlay').classList.remove('is-open');
        document.getElementById('bkCartDrawer').setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function revealAllKioskItems() {
        showAllItems = true;
        const cards = document.querySelectorAll('#kioskGridContainerBox .bk-food-card');
        cards.forEach((card, index) => {
            if (index >= 12) {
                card.classList.remove('item-collapsed');
                requestAnimationFrame(() => card.classList.add('item-revealed'));
            }
        });
        document.getElementById('viewMoreTriggerBox').style.display = 'none';
    }

    function applyMenuVisibility() {
        const keyword = document.getElementById('kioskGlobalSearch').value.toLowerCase().trim();
        const cards = document.querySelectorAll('#kioskGridContainerBox .bk-food-card');
        let matchingHiddenItems = false;

        cards.forEach((card, index) => {
            const itemName = (card.getAttribute('data-name') || '').toLowerCase();
            const itemCategory = card.getAttribute('data-category') || '';
            const categoryMatch = activeCategory === 'all' || (activeCategory === 'Desserts' && ['Desserts', 'Sweets'].includes(itemCategory)) || itemCategory === activeCategory;
            const searchMatch = keyword === '' || itemName.includes(keyword);
            const matches = categoryMatch && searchMatch;
            const isBeyondInitialGrid = index >= 12;

            if (!matches) {
                card.style.display = 'none';
                return;
            }

            card.style.display = 'flex';
            if (isBeyondInitialGrid && !showAllItems && activeCategory === 'all' && keyword === '') {
                card.classList.add('item-collapsed');
                matchingHiddenItems = true;
            } else {
                card.classList.remove('item-collapsed');
                card.classList.add('item-revealed');
            }
        });

        const viewMore = document.getElementById('viewMoreTriggerBox');
        viewMore.style.display = matchingHiddenItems ? 'block' : 'none';
    }

    document.getElementById('kioskGlobalSearch').addEventListener('input', applyMenuVisibility);

    function filterMenu(category, btn) {
        activeCategory = category;
        showAllItems = category !== 'all';
        document.querySelectorAll('.category-btn').forEach(button => button.classList.remove('active'));
        btn.classList.add('active');
        applyMenuVisibility();
    }

    function addNodeItem(id, name, price) {
        const itemId = parseInt(id, 10);
        const itemPrice = parseFloat(price);
        const found = basket.find(item => item.id === itemId);
        if (found) found.qty++;
        else basket.push({ id: itemId, name: name, price: itemPrice, qty: 1 });
        renderBasketUi();
        openCartDrawer();
    }

    function tweakQty(id, delta) {
        const itemId = parseInt(id, 10);
        const item = basket.find(entry => entry.id === itemId);
        if (item) {
            item.qty += delta;
            if (item.qty <= 0) basket = basket.filter(entry => entry.id !== itemId);
        }
        renderBasketUi();
    }

    function renderBasketUi() {
        const list = document.getElementById('bkCartListContainer');
        const submitBtn = document.getElementById('bkSubmitBtn');
        const count = basket.reduce((sum, item) => sum + item.qty, 0);
        document.getElementById('bkCartCount').innerText = count;
        document.getElementById('bkCartInputJson').value = JSON.stringify(basket);

        if (basket.length === 0) {
            list.innerHTML = '<p class="text-white-50 small text-center my-4">Your basket is empty.</p>';
            submitBtn.disabled = true;
            document.getElementById('bkTotalDisplay').innerText = '₹0.00';
            return;
        }

        submitBtn.disabled = false;
        list.innerHTML = '';
        basket.forEach(item => {
            list.innerHTML += '<div class="cart-item-row">' +
                '<div style="max-width:180px;min-width:0;">' +
                    '<div class="fw-bold text-white text-truncate" style="font-size:13px;">' + escapeHtml(item.name) + '</div>' +
                    '<div class="small text-success fw-bold">₹' + item.price.toFixed(2) + ' × ' + item.qty + '</div>' +
                '</div>' +
                '<div class="d-flex align-items-center gap-2">' +
                    '<button type="button" class="btn btn-sm btn-dark p-1 py-0 border-secondary text-white" onclick="tweakQty(' + item.id + ', -1)">−</button>' +
                    '<span class="fw-bold small">' + item.qty + '</span>' +
                    '<button type="button" class="btn btn-sm btn-dark p-1 py-0 border-secondary text-white" onclick="tweakQty(' + item.id + ', 1)">+</button>' +
                '</div>' +
            '</div>';
        });
        calcBkTotal();
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>'"]/g, character => ({ '&':'&amp;', '<':'&lt;', '>':'&gt;', "'":'&#39;', '"':'&quot;' }[character]));
    }

    function calcBkTotal() {
        if (basket.length === 0) return;
        let grand = basket.reduce((sum, item) => sum + (item.price * item.qty), 0);
        if (document.getElementById('bkZwPledge').checked) grand -= grand * 0.05;
        if (document.getElementById('bkRedeemSwitch').checked && ptsBalance > 0) grand = Math.max(0, grand - ptsBalance);
        document.getElementById('bkTotalDisplay').innerText = '₹' + grand.toFixed(2);
    }

    window.addEventListener('DOMContentLoaded', () => {
        applyMenuVisibility();
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'success' && urlParams.get('order_id')) {
            document.getElementById('bottomLeftToastBox').style.display = 'block';
        }
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeCartDrawer();
        });
    });
</script>
<?php include "footer.php"; ?>
