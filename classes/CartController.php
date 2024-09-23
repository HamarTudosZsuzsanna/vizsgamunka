<?php
require_once(CLASSES_DIR . '/FormController.php');

class CartController extends FormController
{
    public static function addToCart(array $data, array $definitions)
    {

        $errors = parent::validate($data, $definitions);

        if (!empty($errors)) {
            return $errors;
        }


        if (empty($data['cart_product_id']) || empty($data['cart_price']) || empty($data['total_price'])) {
            $errors['form_errors'] = 'Hiányzó kötelező mezők a kosárhoz való hozzáadásnál.';
            return $errors;
        }


        if (!isset($_SESSION['logged_in']['id']) || !is_numeric($_SESSION['logged_in']['id']) || (int)$_SESSION['logged_in']['id'] <= 0) {
            $errors['form_errors'] = 'Hibás felhasználói azonosító.';
            return $errors;
        }

        $cart = new Cart();
        $userId = $_SESSION['logged_in']['id'];

        $quantity = isset($data['cart_quantity']) ? (int)$data['cart_quantity'] : 1;
        $price = (float)$data['cart_price'];


        $totalPrice = $quantity * $price;


        $cartData = [
            'id' => $data['cart_product_id'],
            'cart_user_id' => $userId,
            'cart_product_id' => $data['cart_product_id'],
            'cart_quantity' => $quantity,
            'cart_price' => $price,
            'total_price' => $totalPrice,
        ];


        $result = $cart->createCart($cartData, $userId);


        if (!$result) {
            $errors['form_errors'] = 'Hiba történt az adatbázis frissítése során.';
            return $errors;
        }

        redirect('../order');
    }
}
