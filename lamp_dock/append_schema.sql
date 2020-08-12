CREATE TABLE `orders` (
 `order_id` int(11) NOT NULL AUTO_INCREMENT,
 `user_id` int(11) NOT NULL,
 `created` datetime NOT NULL,
 PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `order_details` (
 `order_id` int(11) NOT NULL,
 `item_id` int(11) NOT NULL,
 `buy_price` int(11) NOT NULL,
 `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8