<?php
require_once(CLASSES_DIR . '/FormController.php');

class CartController extends FormController
{
    public static function addToCart(array $data, array $definitions)
    {
        // Adatok validálása a definíciók alapján
        $errors = parent::validate($data, $definitions);

        if (!empty($errors)) {
            return $errors;
        }

        // Ellenőrizzük, hogy a szükséges mezők megvannak-e
        if (empty($data['cart_product_id']) || empty($data['cart_price']) || empty($data['total_price'])) {
            $errors['form_errors'] = 'Hiányzó kötelező mezők a kosárhoz való hozzáadásnál.';
            return $errors;
        }

        // Ellenőrizd, hogy a felhasználó be van-e jelentkezve és érvényes azonosítója van
        if (!isset($_SESSION['logged_in']['id']) || !is_numeric($_SESSION['logged_in']['id']) || (int)$_SESSION['logged_in']['id'] <= 0) {
            $errors['form_errors'] = 'Hibás felhasználói azonosító.';
            return $errors;
        }

        $cart = new Cart();
        $userId = $_SESSION['logged_in']['id'];

        // A kosár adatainak összeállítása
        $cartData = [
            'id' => $data['cart_product_id'],
            'cart_user_id' => $userId,
            'cart_product_id' => $data['cart_product_id'],
            'cart_quantity' => isset($data['cart_quantity']) ? $data['cart_quantity'] : 1, // Default érték 1, ha nincs megadva
            'cart_price' => $data['cart_price'],
            'total_price' => $data['total_price'],
        ];

        // A kosár elem létrehozása
        $result = $cart->createCart($cartData, $userId);

        // Hibaellenőrzés adatbázis művelet után
        if (!$result) {
            $errors['form_errors'] = 'Hiba történt az adatbázis frissítése során.';
            return $errors;
        }

        // Sikeres kosárba tétel után átirányítás
        redirect('../order');
    }
}
