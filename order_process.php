<?php
    session_start();
    if(!isset($_SESSION["loggedin"])) { 
        header("location: login_page.php"); 
        exit; 
    }
    require_once "config.php";

    if (isset($_REQUEST["cart_data"]) && !empty($_REQUEST["cart_data"])) {
        $user_id = $_SESSION["user_id"];
        $cart_json = $_REQUEST["cart_data"];
        $payment_mode = in_array($_REQUEST["payment_mode"] ?? "", ["cash", "upi", "card"], true) ? $_REQUEST["payment_mode"] : "cash";
        $redeem_points = isset($_REQUEST['redeem_points']) ? 1 : 0;
        $zero_waste = isset($_REQUEST['zero_waste']) ? 1 : 0;

        $user_res = $link->query("select reward_points from users where id = " . $user_id);
        $user_data = $user_res->fetch_assoc();
        $current_points = $user_data['reward_points'];

        $cart_array = json_decode($cart_json, true);
        $subtotal = 0;
        $items_summary_list = array();

        if (is_array($cart_array)) {
            foreach ($cart_array as $item) {
                $p_id = (int)$item['id'];
                $p_qty = (int)$item['qty'];
                
                $price_res = $link->query("select item_name, price from menu_items where id = " . $p_id);
                if ($price_res && $price_res->num_rows > 0) {
                    $price_row = $price_res->fetch_assoc();
                    $subtotal += ($price_row['price'] * $p_qty);
                    $items_summary_list[] = $price_row['item_name'] . " (x" . $p_qty . ")";
                }
            }
        }

        $item_list_string = implode(", ", $items_summary_list);

        if ($zero_waste == 1) {
            $subtotal = $subtotal - ($subtotal * 0.05);
        }

        $points_used_in_this_order = 0;

        if ($redeem_points == 1 && $current_points > 0) {
            if ($current_points >= $subtotal) {
                $points_used_in_this_order = (int)$subtotal;
                $subtotal = 0;
            } else {
                $points_used_in_this_order = $current_points;
                $subtotal = $subtotal - $current_points;
            }
            $link->query("update users set reward_points = reward_points - " . $points_used_in_this_order . " where id = " . $user_id);
        }

        if ($zero_waste == 1) {
            $link->query("update users set reward_points = reward_points + 10 where id = " . $user_id);
        }

        $str_insert = "insert into orders (user_id, item_list, total_price, points_redeemed, payment_mode, order_status, zero_waste_pledge) 
                       values (" . $user_id . ", '" . $link->real_escape_string($item_list_string) . "', " . $subtotal . ", " . $points_used_in_this_order . ", '" . $link->real_escape_string($payment_mode) . "', 'pending', " . $zero_waste . ")";
                       
        if ($link->query($str_insert) === TRUE) {
            $new_order_id = $link->insert_id;
            
            header("location: dashboard.php?status=success&order_id=" . $new_order_id);
            exit();
        }
    }
    header("location: dashboard.php");
    exit;
?>
