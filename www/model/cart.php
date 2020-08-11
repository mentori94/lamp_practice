<?php 
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'db.php';

function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql, array($user_id));
}

function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql, array($user_id, $item_id));

}

function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";

  return execute_query($db, $sql, array($item_id, $user_id, $amount));
}

function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql, array($amount, $cart_id));
}

function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql, array($cart_id) );
}

// 購入時におけるカート処理
function purchase_carts($db, $carts){
  
  $db->beginTransaction();
  try {

    if(validate_cart_purchase($carts) === false){
      return false;
    }
    foreach($carts as $cart){
      if(update_item_stock(
          $db, 
          $cart['item_id'], 
          $cart['stock'] - $cart['amount']
        ) === false){
        set_error($cart['name'] . 'の購入に失敗しました。');
      }
    }
    
    insert_orders_table($db, $carts[0]['user_id']);
    $order_id = $db->lastInsertId();
    foreach($carts as $cart) {
      insert_orders_detail_table($db, $order_id, $cart['item_id'], $cart['price'], $cart['amount']);
    }

    delete_user_carts($db, $carts[0]['user_id']);
    $db->commit();
  } catch(PDOException $e) {
      $dbh -> rollback();
      throw $e;
  }
}

// 購入履歴テーブルにデータ挿入
function insert_orders_table($db, $user_id) {
  $sql = "INSERT INTO `orders` (`user_id`, `created`) VALUES (?, NOW())";

  execute_query($db, $sql,  array($user_id));
}

// 購入明細テーブルにデータ挿入
function insert_orders_detail_table($db, $order_id, $item_id, $price, $amount) {
  $sql = "INSERT INTO `order_details` (`order_id`, `item_id`, `buy_price`, `amount`) VALUES (?, ?, ?, ?)";

  execute_query($db, $sql,  array($order_id, $item_id, $price, $amount));
}


// 購入完了のカート情報削除
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql,  array($user_id));
}


function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

// 購入履歴データ抽出
function get_user_orders_history($db, $user_id) {
  $sql = 'SELECT 
  orders.order_id, 
  created,
  SUM(order_details.amount * order_details.buy_price) as total_price
  FROM
  orders
  
  JOIN
  order_details
  ON
  orders.order_id = order_details.order_id
  WHERE 
  user_id = ?
  GROUP BY
  orders.order_id
  ORDER BY
  orders.order_id desc';

  return fetch_all_query($db, $sql, array($user_id));
}

// 購入履歴データ抽出(admin)
function get_full_orders_history($db) {
  $sql = 'SELECT 
  orders.order_id, 
  created,
  SUM(order_details.amount * order_details.buy_price) as total_price
  FROM
  orders
  
  JOIN
  order_details
  ON
  orders.order_id = order_details.order_id
  GROUP BY
  orders.order_id
  ORDER BY
  orders.order_id desc';

  return fetch_all_query($db, $sql);
}




// 購入明細データ抽出
function get_user_order_details($db, $order_id) {
  $sql = 
  'SELECT
  order_id,
  items.name,
  items.item_id,
  buy_price,
  amount,
  order_details.amount * order_details.buy_price as total_price
  FROM
  order_details
  JOIN
  items
  ON
  order_details.item_id = items.item_id
  WHERE
  order_id = ?';

  return fetch_all_query($db, $sql, array($order_id));
}

// 購入データ抽出
function get_user_orders_info($db, $order_id) {
  $sql = 'SELECT 
  orders.order_id, 
  created,
  SUM(order_details.amount * order_details.buy_price) as total_price
  FROM
  orders
  JOIN
  order_details
  ON
  orders.order_id = order_details.order_id
  WHERE 
  orders.order_id = ?
  GROUP BY
  orders.order_id';

  return fetch_all_query($db, $sql, array($order_id));
}

