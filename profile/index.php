<?php
require('../includes/init.php');
require('../classes/UserFormController.php');
session_start();
$errors = [];
$user = new User();
$definitions = FormController::getDefinition('userUpdate');

if (!empty($_POST)) {
    $errors = UserFormController::update(array_merge($_POST, $_FILES), $definitions);
}

if (!empty($_POST) && array_key_exists('logout', $_POST) && !empty($_POST['logout'])) {
    $user = new User();
    $user->logout();
}

if (empty($_SESSION['logged_in'])) {
    redirect('/login');
}


$userId = $_SESSION['logged_in']['id'];
$loggedUser = $user->filterFillables($_SESSION['logged_in']);

$keyMapData = ['phone' => 'E-mail cím', 'address' => 'Létrehozási idő', 'number' => 'number'];
//dd($loggedUser);
?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
</head>

<body style="display: flex; flex-wrap: wrap; gap: 15px; max-width: 800px;">
    
    <?php foreach ($definitions as $definition) : //alapadatok
        if (!array_key_exists($definition['key'], $loggedUser) && $definition['force_show'] !== true) {
            continue;
        }
    ?>
        <div style="display: flex; flex-direction: column;">
            <label><?php echo $definition['label']; ?></label>
            <div><?php echo $loggedUser[$definition['key']] ?? ''; ?></div>
        </div>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
        <?php foreach ($definitions as $definition) : // szerkesztés
            if (!array_key_exists($definition['key'], $loggedUser) && $definition['force_show'] !== true) {
                continue;
            }
        ?>
            <div style="display: flex; flex-direction: column;">
                <label><?php echo $definition['label']; ?></label>
                <input
                    type="<?php echo $definition['type'] ?? 'text'; ?>"
                    name="<?php echo $definition['key']; ?>"
                    value="<?php echo $loggedUser[$definition['key']] ?? ''; ?>" />
                <?php if (is_array($errors) && array_key_exists($definition['key'], $errors) && !empty($errors[$definition['key']])): ?>
                    <div style="color: red;"><?php echo $errors[$definition['key']]; ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>



        <?php if (is_array($errors) && array_key_exists('form_errors', $errors)): ?>
            <div style="color: red;"><?php echo $errors['form_errors']; ?></div>
        <?php endif; ?>

        <div>
            <input type="submit" value="Módosítás" />
            <a style="background-color: red; color: white; padding: 2px 5px; border-radius:3px;" href="/">Vissza a főoldalra</a>
        </div>
        <form method="POST" style="flex: 1 0 100%;">
            <input type="hidden" name="logout" value="true" />
            <input type="submit" value="Kijelentkezés" />
        </form>
    </form>
</body>

</html>