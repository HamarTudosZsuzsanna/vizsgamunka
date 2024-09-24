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
    $order_id = time() . '-' . $userId;

    $ordersData = [
        'id' => 'id',
        'user_id' => $userId,
        'total_price' => $data['total_price'],
        'order_status' => 'függőben',
    ];

    // Az ordersMetaData így tartalmazni fogja az order_id-t is
    $ordersMetaData = [
        'order_id' => $order_id, // Itt adjuk hozzá az order_id-t
        'product_id' => $data['product_id'], // A termékek ID-jai
        'quantity' => $data['quantity'], // Mennyiségek
        'price' => $data['price'], // Árak
    ];

    $orderId = $order->createOrders($ordersData, $userId);
    
    // A createMetaOrders metódushoz csak egyszer hívjuk meg a termékek tömbjét
    foreach ($ordersMetaData['product_id'] as $index => $productId) {
        $order->createMetaOrders([
            'order_id' => $order_id, // Az order_id most már itt van
            'product_id' => $productId,
            'quantity' => $ordersMetaData['quantity'][$index],
            'price' => $ordersMetaData['price'][$index],
        ], $userId);
    }

    $cart = new Cart();  // Feltételezve, hogy a Cart osztály kezelik a kosarat
    $cart->clearCartByUserId($userId);

    redirect('/profile');
}

}
