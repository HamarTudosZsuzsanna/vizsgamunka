<?php
require_once(CLASSES_DIR . '/FormController.php');


class OrdersController extends FormController
{
    public static function saveOrders(array $data, array $definitions)
    {
        $errors = parent::validate($data, $definitions);

        if (!empty($errors)) {
            return $errors;
        }


        if (!isset($_SESSION['logged_in']['id']) || !is_numeric($_SESSION['logged_in']['id']) || (int)$_SESSION['logged_in']['id'] <= 0) {
            $errors['form_errors'] = 'Hibás felhasználói azonosító.';
            return $errors;
        }

        $orders = new Orders();
        $userId = $_SESSION['logged_in']['id'];
        $order_id = time() . '-' . $userId;
        

        $ordersData = [
            'id' => $data['dishes_name'],
            'user_id' => $userId,
            'order_id' => $order_id,
            'dish_id' => $data['dish_id'],
            'quantity' => isset($data['quantity']) ? $data['quantity'] : null,
            'price' => $data['price'],
        ];

        
        $result = $orders->createOrders($ordersData, $userId);

        if (!$result) {
            $errors['form_errors'] = 'Hiba történt az adatbázis frissítése során.';
            return $errors;
        }

        redirect('/profile');
    }
}