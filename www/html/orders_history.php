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

if ($user['user_id'] !== 4) {
  $orders = get_user_orders_history($db, $user['user_id']);
} else {
  $orders = get_full_orders_history($db);
}

include_once VIEW_PATH . 'orders_history_view.php';