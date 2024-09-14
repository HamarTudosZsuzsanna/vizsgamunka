<?php
require('../includes/init.php');
require(CLASSES_DIR . '/UserFormController.php');
$definitions = FormController::getDefinition('user');
$errors = UserFormController::save($_POST, $definitions);
?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
</head>

<body style="display: flex; max-width: 500px;">
    <form method="POST">
        <?php foreach ($definitions as $definition) : ?>
            <div style="display: flex; flex-direction: column;">
                <label><?php echo $definition['label']; ?></label>
                <input
                    type="<?php echo $definition['type'] ?? 'text'; ?>"
                    name="<?php echo $definition['key']; ?>"
                    value="<?php echo FormController::getFieldValue($definition['key']); ?>" />
                <?php if (is_array($errors) && array_key_exists($definition['key'], $errors) && !empty($errors[$definition['key']])): ?>
                    <div style="color: red;"><?php echo $errors[$definition['key']]; ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (is_array($errors) && array_key_exists('form_errors', $errors)): ?>
            <div style="color: red;"><?php echo $errors['form_errors']; ?></div>
        <?php endif; ?>

        <input type="checkbox" id="accepted" name="accepted" value="accepted">
        <label for="accepted">Elfogadom a felhasználási feltételeket</label><br>

        <input type="submit" value="Beküldés" />
        <a style="background-color: red; color: white; padding: 2px 5px; border-radius:3px;" href="/">Vissza a főoldalra</a>
    </form>
</body>

</html>

<?php
