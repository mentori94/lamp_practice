<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <!-- <link rel="stylesheet" href="<?php print(STYLESHEET_PATH . 'cart.css'); ?>"> -->
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>注文番号</th>
        <th>購入日時</th>
        <th>注文の合計金額</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($orders as $order){ ?>
      <tr>
        <td><?php print($order['order_id']); ?></td>
        <td><?php print($order['created']); ?></td>
        <td><?php print($order['total_price']); ?>円</td>
      </tr>
      <?php } ?>
    </tbody>
  </table>

  <h1>購入明細</h1>
  <?php if(count($order_details) > 0){ ?>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>商品名</th>
        <th>購入時の商品価格</th>
        <th>購入数</th>
        <th>小計</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($order_details as $detail){ ?>
      <tr>
        <td><?php print($detail['name']); ?></td>
        <td><?php print($detail['buy_price']); ?></td>
        <td><?php print($detail['amount']); ?></td>
        <td><?php print($detail['total_price']); ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php } else { ?>
      <p>購入した商品はありません。</p>
  <?php } ?> 
</body>
</html>