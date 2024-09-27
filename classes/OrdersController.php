<?php
require_once(CLASSES_DIR . '/FormController.php');
require_once('../models/Cart.php');

class OrdersController extends FormController
{
    public static function saveOrders(array $data, array $definitions)
{
    $errors = parent::validate($data, $definitions);

    if (!empty($errors)) {
        return $errors;
    }

    $order = new Orders();
    $userId = $_SESSION['logged_in']['id'];
    $order_id = time();

    $ordersData = [
        'id' => 'id',
        'user_id' => $userId,
        'order_id' => $order_id,
        'total_price' => $data['total_price'],
        'order_status' => 'függőben',
    ];


    $ordersMetaData = [
        'order_id' => $order_id,
        'product_id' => $data['product_id'],
        'quantity' => $data['quantity'],
        'price' => $data['price'],
    ];

    $orderId = $order->createOrders($ordersData, $userId);

    foreach ($ordersMetaData['product_id'] as $index => $productId) {
        $order->createMetaOrders([
            'order_id' => $order_id,
            'product_id' => $productId,
            'quantity' => $ordersMetaData['quantity'][$index],
            'price' => $ordersMetaData['price'][$index],
        ], $userId);
    }

    $cart = new Cart();
    $cart->clearCartByUserId($userId);

    redirect('/profile');
}

}
