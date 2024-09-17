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


    if (!isset($_SESSION['logged_in']['id']) || !is_numeric($_SESSION['logged_in']['id']) || (int)$_SESSION['logged_in']['id'] <= 0) {
        $errors['form_errors'] = 'Hibás felhasználói azonosító.';
        return $errors;
    }

    if (!isset($_FILES['dishes_image']) || $_FILES['dishes_image']['error'] !== UPLOAD_ERR_OK) {
        $errors['form_errors'] = 'Hiba történt a képfeltöltés során.';
        return $errors;
    }


    $dishesImage = $_FILES['dishes_image'];
    $dishes = new Dishes();
    $adminId = (int)$_SESSION['logged_in']['id'];

    $dishesData = [
        'dishes_name' => trim($data['dishes_name']),
        'description' => trim($data['description']),
        'price' => trim($data['price']),
        'categories' => trim($data['categories']),
        'admin_id' => $adminId
    ];

    $fileName = uniqid() . '.png'; 
    $filePathName = sprintf('%s/images/%s', PUBLIC_DISHES_DIR, $fileName);

    $result = move_uploaded_file($dishesImage['tmp_name'], $filePathName);

    if ($result) {
    
        $dishesData['dishes_image'] = '/public/dishes/images/' . $fileName;
    } else {
        $errors['form_errors'] = 'Hiba történt a kép áthelyezése során.';
        return $errors;
    }

    $result = $dishes->createDishes($dishesData, $adminId);

    if (!$result) {
        $errors['form_errors'] = 'Hiba történt az adatbázis frissítése során.';
        return $errors;
    }

    redirect('/admin/dishes');
}



}
