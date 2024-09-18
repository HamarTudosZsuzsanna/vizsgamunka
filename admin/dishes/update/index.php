<?php

use function PHPSTORM_META\type;

require('../../../includes/init.php');
require('../../../classes/DishesFormController.php');
require_once('../../../models/Dishes.php');

session_start();
$errors = [];
$dishes = new Dishes();
$definitions = FormController::getDefinition('dishes');

// Ha nincs bejelentkezve a felhasználó, irányítsuk át a login oldalra
if (empty($_SESSION['logged_in']['id'])) {
    redirect('/login');
}

$userId = $_SESSION['logged_in']['id'];
$dishesSlug = $_GET['id'];  // A slugot lekérjük az URL-ből
$dishesData = $dishes->getDishesBySlug($dishesSlug);  // Lekérjük a slug alapján a termék adatait
$dishesImage = $dishes->getDishesImageBySlug($dishesSlug);

$errors = DishesFormController::updateDishes(array_merge($_POST, $_FILES), $definitions);
pd($dishesImage);
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
                        type="<?php echo htmlspecialchars($definition['type']); ?>"
                        id="<?php echo htmlspecialchars($definition['key']); ?>"
                        name="<?php echo htmlspecialchars($definition['key']); ?>"
                        value="<?php echo htmlspecialchars($dishesData[$definition['key']] ?? ''); ?>"
                        required />
                <?php else : ?>
                    <!-- Kép megjelenítése és fájlfeltöltés -->
                    <img src="<?php echo $dishesImage;?>" alt="<?php echo htmlspecialchars($dishesData['dishes_image']); ?>" style="width:100px;height:100px;" />
                    <input type="file" name="dishes_image" />
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