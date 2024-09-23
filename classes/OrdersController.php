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
            'id' => $order_id,
            'user_id' => $userId,
            'total_price' => $data['total_price'],
        ];

        
        $result = $orders->createOrders($ordersData, $userId);

        if (!$result) {
            $errors['form_errors'] = 'Hiba történt az adatbázis frissítése során.';
            return $errors;
        }

        redirect('/profile');
    }
}