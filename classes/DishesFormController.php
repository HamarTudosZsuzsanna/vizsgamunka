<?php
require_once(CLASSES_DIR . '/FormController.php');

class DishesFormController extends FormController
{

    public static function saveDishes(array $data, array $definitions)
    {
        $errors = parent::validate($data, $definitions);

        if (!empty($errors)) {
            return $errors;
        }

        if ($errors === false) {
            return;
        }

        if (!isset($_SESSION['logged_in']['id']) || !is_numeric($_SESSION['logged_in']['id']) || (int)$_SESSION['logged_in']['id'] <= 0) {
            $errors['form_errors'] = 'Hibás felhasználói azonosító.';
            return $errors;
        }

        
        $dishes = new Dishes();
        $adminId = (int)$_SESSION['logged_in']['id'];

        $dishesData = [
            'dishes_name' => trim($data['dishes_name']),
            'description' => trim($data['description']),
            'price' => trim($data['price']),
            'dishes_image' => trim($data['dishes_image']),
            'categories' => trim($data['categories']),
            'admin_id' => $adminId // Felhasználói azonosítót is hozzáadjuk
        ];

        $result = $dishes->createDishes($dishesData, $adminId);

        if (!$result) {
            $errors['form_errors'] = 'Hiba történt az adatbázis frissítése során.';
            return $errors;
        }


        redirect('/admin/dishes');
    }
    
}
