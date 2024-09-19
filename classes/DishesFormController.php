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
        $slug = $dishes->slugify($data['dishes_name']);

        $dishesData = [
            'dishes_name' => trim($data['dishes_name']),
            'description' => trim($data['description']),
            'price' => trim($data['price']),
            'categories' => trim($data['categories']),
            'slug' => trim($slug),
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

    public static function updateDishes(array $data, array $definitions)
    {
        if (empty($data['dishes_name']) || empty($data['description']) || empty($data['price']) || empty($data['categories'])) {
            $errors['form_errors'] = 'Minden mezőt ki kell tölteni!';
            return $errors;
        }
        // Adatok validálása
        $errors = parent::validate($data, $definitions);

        if (!empty($errors)) {
            return $errors;
        }

        // Felhasználói azonosító ellenőrzése
        if (!isset($_SESSION['logged_in']['id']) || !is_numeric($_SESSION['logged_in']['id']) || (int)$_SESSION['logged_in']['id'] <= 0) {
            $errors['form_errors'] = 'Hibás felhasználói azonosító.';
            return $errors;
        }

        // Az étel slug alapján történő lekérése
        $dishesSlug = $_GET['id'];
        $dishes = new Dishes();
        $existingDishesData = $dishes->getDishesBySlug($dishesSlug);

        if (!$existingDishesData) {
            $errors['form_errors'] = 'A kért étel nem található.';
            return $errors;
        }

        // Alapvető adatfeldolgozás
        $adminId = (int)$_SESSION['logged_in']['id'];
        $slug = $dishes->slugify($data['dishes_name']);

        $price = trim($data['price']);
        if (!is_numeric($price) || (float)$price <= 0) {
            $errors['price'] = 'Az árnak egy pozitív számnak kell lennie!';
            return $errors;
        }

        $dishesData = [
            'dishes_name' => trim($data['dishes_name']),
            'description' => trim($data['description']),
            'price' => $price,
            'categories' => trim($data['categories']),
            'slug' => trim($slug),
            'admin_id' => $adminId
        ];

        // Kép feltöltésének ellenőrzése
        if (isset($_FILES['dishes_image']) && $_FILES['dishes_image']['error'] === UPLOAD_ERR_OK) {
            $dishesImage = $_FILES['dishes_image'];
            $fileName = uniqid() . '.png';
            $filePathName = sprintf('%s/images/%s', PUBLIC_DISHES_DIR, $fileName);

            $result = move_uploaded_file($dishesImage['tmp_name'], $filePathName);

            if ($result) {
                $dishesData['dishes_image'] = '/public/dishes/images/' . $fileName;
            } else {
                $errors['form_errors'] = 'Hiba történt a kép áthelyezése során.';
                return $errors;
            }
        } else {
            // Ha nincs új kép feltöltve, használjuk a meglévő képet
            $dishesData['dishes_image'] = $existingDishesData['dishes_image'];
        }

        // Adatok frissítése az adatbázisban
        $result = $dishes->updateDishes($dishesData, $dishesSlug);

        if (!$result) {
            $errors['form_errors'] = 'Hiba történt az adatbázis frissítése során.';
            return $errors;
        }

        // Sikeres frissítés után visszairányítás
        redirect('/admin/dishes');
    }
}
