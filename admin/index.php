<?php

require('../includes/init.php');
require(CLASSES_DIR . '/UserFormController.php');

session_start();

$definitions = FormController::getDefinition('adminLogin');
$errors = UserFormController::loginAdmin($_POST, $definitions);


if (!empty($_SESSION['logged_in'])) {
    redirect('/admin/dishes');
}

?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
</head>

<body style="display: flex; max-width: 500px;">
    <form method="POST">
        <?php foreach ($definitions as $definition) : ?>
            <?php if ($definition['key'] === 'email' || $definition['key'] === 'password') : ?>
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
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (is_array($errors) && array_key_exists('form_errors', $errors)): ?>
            <div style="color: red;"><?php echo $errors['form_errors']; ?></div>
        <?php endif; ?>

        <input type="submit" value="Beküldés" />
        <a style="background-color: red; color: white; padding: 2px 5px; border-radius:3px;" href="/">Vissza a főoldalra</a>
    </form>
</body>

</html>