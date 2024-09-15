<?php
require('../includes/init.php');
require('../classes/UserFormController.php');
session_start();
$errors = [];
$user = new User();
$definitions = FormController::getDefinition('userUpdate');
$definitionDatas = FormController::getDefinition('userData');

if (!empty($_POST)) {

    if (isset($_POST['form_type']) && $_POST['form_type'] === 'user') {
        $errors = UserFormController::update(array_merge($_POST, $_FILES), $definitions);
    } elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'user_data') {
        $errors = UserFormController::updateData(array_merge($_POST, $_FILES), $definitionDatas);
    }
}

if (empty($_SESSION['logged_in']['id'])) {
    redirect('/login');
}

$userId = $_SESSION['logged_in']['id'];
$loggedUser = $user->filterFillables($_SESSION['logged_in']);
$userData = $user->getByUserId($userId);
$loggedUserData = $user->filterFillablesData($userData);


//pd($loggedUser);
//pd($userData);
//pd($loggedUserData);
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
</head>

<body style="display: flex; flex-wrap: wrap; gap: 15px;">

    <!-- Alapadatok megjelenítése -->
    <?php foreach ($definitions as $definition) :
        if (!array_key_exists($definition['key'], $loggedUser) && (!isset($definition['force_show']) || $definition['force_show'] !== true)) {
            continue;
        }
    ?>
        <div style="display: flex; flex-direction: column;">
            <label><?php echo htmlspecialchars($definition['label'], ENT_QUOTES, 'UTF-8'); ?></label>
            <div><?php echo htmlspecialchars($loggedUser[$definition['key']] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
        </div>
    <?php endforeach; ?>

    <!-- Szállítási adatok megjelenítése -->
    <?php foreach ($definitionDatas as $definitionData) : ?>
            <div style="display: flex; flex-direction: column;">
                <div><?php echo htmlspecialchars($definitionData['label'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div><?php echo htmlspecialchars($loggedUserData[$definitionData['key']] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endforeach; ?>

    <!-- Űrlap szerkesztése -->

    <form method="POST">
        <h3>Alapadatok szerkesztése</h3>
        <input type="hidden" name="form_type" value="user" />
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($loggedUser['id'], ENT_QUOTES, 'UTF-8'); ?>" />
        <?php foreach ($definitions as $definition) :
            if (!array_key_exists($definition['key'], $loggedUser) && (!isset($definition['force_show']) || $definition['force_show'] !== true)) {
                continue;
            }
        ?>
            <div style="display: flex; flex-direction: column;">
                <label><?php echo htmlspecialchars($definition['label'], ENT_QUOTES, 'UTF-8'); ?></label>
                <input
                    type="<?php echo htmlspecialchars($definition['type'] ?? 'text', ENT_QUOTES, 'UTF-8'); ?>"
                    name="<?php echo htmlspecialchars($definition['key'], ENT_QUOTES, 'UTF-8'); ?>"
                    value="<?php echo htmlspecialchars($loggedUser[$definition['key']] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                <?php if (isset($errors[$definition['key']]) && !empty($errors[$definition['key']])): ?>
                    <div style="color: red;"><?php echo htmlspecialchars($errors[$definition['key']], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Általános űrlaphiba megjelenítése -->
        <?php if (isset($errors['form_errors'])): ?>
            <div style="color: red;"><?php echo htmlspecialchars($errors['form_errors'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div>
            <input type="submit" value="Módosítás" />
            <a style="background-color: red; color: white; padding: 2px 5px; border-radius:3px;" href="/">Vissza a főoldalra</a>
        </div>
    </form>

    <!-- Szállítási cím szerkesztése -->
    <form method="POST">
        <h3>Szállítási cím szerkesztése</h3>
        <input type="hidden" name="form_type" value="user_data" />
        <?php foreach ($definitionDatas as $definitionData) : ?>
            <div style="display: flex; flex-direction: column;">
                <label><?php echo htmlspecialchars($definitionData['label'], ENT_QUOTES, 'UTF-8'); ?></label>
                <input
                    type="<?php echo htmlspecialchars($definitionData['type'] ?? 'text', ENT_QUOTES, 'UTF-8'); ?>"
                    name="<?php echo htmlspecialchars($definitionData['key'], ENT_QUOTES, 'UTF-8'); ?>"
                    value="<?php echo htmlspecialchars($loggedUserData[$definitionData['key']] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
                <?php if (isset($errors[$definitionData['key']]) && !empty($errors[$definitionData['key']])): ?>
                    <div style="color: red;"><?php echo htmlspecialchars($errors[$definitionData['key']], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <!-- Általános űrlaphiba megjelenítése -->
        <?php if (isset($errors['form_errors'])): ?>
            <div style="color: red;"><?php echo htmlspecialchars($errors['form_errors'], ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <div>
            <input type="submit" value="Módosítás" />
            <a style="background-color: red; color: white; padding: 2px 5px; border-radius:3px;" href="/">Vissza a főoldalra</a>
        </div>
    </form>

</body>

</html>