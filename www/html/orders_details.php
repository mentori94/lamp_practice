<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();

if(is_logined() === false){
  redirect_to(HOME_URL);
}

$db = get_db_connect();
$user = get_login_user($db);

$order_id = get_post('order_id');

$order_details = get_user_order_details($db, $order_id);
$orders = get_user_orders_info($db, $order_id);

include_once VIEW_PATH . 'orders_details_view.php';