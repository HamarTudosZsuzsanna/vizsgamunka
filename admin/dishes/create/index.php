<?php
require('../../../includes/init.php');
require('../../../classes/DishesFormController.php');
require_once('../../../models/Dishes.php');

session_start();
$errors = [];
$dishes = new Dishes();
$definitions = FormController::getDefinition('dishes');

if (empty($_SESSION['logged_in'])) {
    redirect('/login');
}

$userId = $_SESSION['logged_in']['id'];
$loggedUser = $dishes->filterFillablesDishes($_SESSION['logged_in']);

pd($dishes);
pd($loggedUser['dishes_image']);


if (!empty($_POST)) {
    $errors = DishesFormController::saveDishes($_POST, $definitions);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feltöltés</title>
</head>

<body>
    <form action="" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; width: 400px; gap: 10px">

        <?php foreach ($definitions as $definition) : ?>
            <div style="display: flex; flex-direction: column; gap: 10px">
                <label for="<?php echo htmlspecialchars($definition['key']); ?>"><?php echo htmlspecialchars($definition['label']); ?></label>
                <?php if ($definition['type'] == '') : ?>
                    <input
                        type="<?php echo htmlspecialchars($definition['label']) ?>"
                        id="<?php echo htmlspecialchars($definition['key']); ?>"
                        name="<?php echo htmlspecialchars($definition['key']); ?>"
                        value="<?php echo htmlspecialchars($_POST[$definition['key']] ?? ''); ?>"
                        required />
                <?php else : ?>
                    <img src='<?php echo $dishes->getDishesImage($userId); ?>' />
                    <input type='file' name='dishes_image' value='<?php echo $loggedUser['dishes_image']; ?>' />
                    <input type='hidden' name='id' value='<?php echo $userId; ?>' />
                <?php endif; ?>
                <?php if (is_array($errors) && array_key_exists($definition['key'], $errors) && !empty($errors[$definition['key']])): ?>
                    <div style="color: red;"><?php echo htmlspecialchars($errors[$definition['key']]); ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        


        <?php if (is_array($errors) && array_key_exists('form_errors', $errors)): ?>
            <div style="color: red;"><?php echo htmlspecialchars($errors['form_errors']); ?></div>
        <?php endif; ?>

        <input type="submit" value="Beküldés" />



    </form>
    <br>
    <a href="/profile">Vissza</a>
</body>

</html>