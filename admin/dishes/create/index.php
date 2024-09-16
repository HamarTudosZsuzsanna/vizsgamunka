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

if (!empty($_POST)) {
    $errors = DishesFormController::saveDishes($_POST, $definitions);
}
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cikk</title>
</head>

<body>
    <form action="" method="POST" style="display: flex; flex-direction: column; width: 400px; gap: 10px">

        <?php foreach ($definitions as $definition) : ?>
            <div style="display: flex; flex-direction: column; gap: 10px">
                <label for="<?php echo htmlspecialchars($definition['key']); ?>"><?php echo htmlspecialchars($definition['label']); ?></label>
                
                    <input
                        type="text"
                        id="<?php echo htmlspecialchars($definition['key']); ?>"
                        name="<?php echo htmlspecialchars($definition['key']); ?>"
                        value="<?php echo htmlspecialchars($_POST[$definition['key']] ?? ''); ?>"
                        required />
                <?php if (is_array($errors) && array_key_exists($definition['key'], $errors) && !empty($errors[$definition['key']])): ?>
                    <div style="color: red;"><?php echo htmlspecialchars($errors[$definition['key']]); ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Ellenőrzés a cikk mentése során felmerült hibákra -->
        <?php if (is_array($errors) && array_key_exists('form_errors', $errors)): ?>
            <div style="color: red;"><?php echo htmlspecialchars($errors['form_errors']); ?></div>
        <?php endif; ?>

        <input type="submit" value="Beküldés" />



    </form>
    <br>
    <a href="/profile">Vissza</a>
</body>

</html>
