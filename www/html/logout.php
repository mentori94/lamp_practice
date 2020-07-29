<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';

session_start();

$token = get_csrf_token();
$token = is_valid_csrf_token($token);

$_SESSION = array();
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 42000,
  $params["path"], 
  $params["domain"],
  $params["secure"], 
  $params["httponly"]
);
session_destroy();

redirect_to(LOGIN_URL);

