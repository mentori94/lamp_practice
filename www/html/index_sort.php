<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';
require_once MODEL_PATH . 'cart.php';

session_start();

$token = get_csrf_token();
$token = is_valid_csrf_token($token);

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();


redirect_to(HOME_URL);