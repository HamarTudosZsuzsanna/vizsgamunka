<?php

use function PHPSTORM_META\type;

require('../../../includes/init.php');
require('../../../classes/DishesFormController.php');
require_once('../../../models/Dishes.php');

session_start();
$errors = [];
$dishes = new Dishes();
$definitions = FormController::getDefinition('dishes');

if (empty($_SESSION['logged_in']) || $_SESSION['logged_in']['role'] !== 'admin') {
    redirect('/login');
}

$userId = $_SESSION['logged_in']['id'];
$dishesSlug = $_GET['id'];  // A slugot lekérjük az URL-ből
$dishesData = $dishes->getDishesBySlug($dishesSlug);  // Lekérjük a slug alapján a termék adatait
$dishesImage = $dishes->getDishesImageBySlug($dishesSlug);

$errors = DishesFormController::updateDishes(array_merge($_POST, $_FILES), $definitions);
pd($dishesData)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termék szerkesztése</title>
</head>

<body>
    <form action="" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; width: 400px; gap: 10px">
        <?php foreach ($definitions as $definition) : ?>
            <div style="display: flex; flex-direction: column; gap: 10px">
                <label for="<?php echo htmlspecialchars($definition['key']); ?>">
                    <?php echo htmlspecialchars($definition['label']); ?>
                </label>

                <?php if ($definition['type'] != 'file') : ?>
                    <!-- Alap mezők kezelése, mint pl. szöveg, szám -->
                    <input
                        type="<?php echo htmlspecialchars($definition['type'] ?? 'text'); ?>"
                        id="<?php echo htmlspecialchars($definition['key']); ?>"
                        name="<?php echo htmlspecialchars($definition['key']); ?>"
                        value="<?php echo htmlspecialchars($dishesData[$definition['key']] ?? ''); ?>"
                        required />
                <?php else : ?>
                    <!-- Kép megjelenítése és fájlfeltöltés -->
                    <?php if (!empty($dishesData[$definition['key']])) : ?>
                        <img src="<?php echo htmlspecialchars($dishesData[$definition['key']]); ?>" alt="Kép" style="max-width: 100px;" />
                        <!-- Eltároljuk a jelenlegi kép URL-jét, hogy új feltöltés hiányában ezt használjuk -->
                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($dishesData[$definition['key']]); ?>" />
                    <?php else : ?>
                        <p>Nincs feltöltött kép</p>
                    <?php endif; ?>
                    <input type="file" name="<?php echo htmlspecialchars($definition['key']); ?>" />
                <?php endif; ?>

                <!-- Hibaüzenetek megjelenítése -->
                <?php if (is_array($errors) && array_key_exists($definition['key'], $errors) && !empty($errors[$definition['key']])) : ?>
                    <div style="color: red;"><?php echo htmlspecialchars($errors[$definition['key']]); ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Általános hibakezelés -->
        <?php if (is_array($errors) && array_key_exists('form_errors', $errors)) : ?>
            <div style="color: red;"><?php echo htmlspecialchars($errors['form_errors']); ?></div>
        <?php endif; ?>

        <!-- Beküldés gomb -->
        <input type="submit" value="Frissítés" />
    </form>

    <br>
    <a href="/admin/dishes/">Vissza</a>
</body>

</html>