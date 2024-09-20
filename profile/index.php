<?php
require('../includes/init.php');
require('../classes/UserFormController.php');
require('../classes/OrdersController.php');
require('../models/Orders.php');
session_start();
$errors = [];
$user = new User();
$order = new Orders();
$definitions = FormController::getDefinition('userUpdate');
$definitionDatas = FormController::getDefinition('userData');

if (!empty($_POST)) {

    if (isset($_POST['form_type']) && $_POST['form_type'] === 'user') {
        $errors = UserFormController::update(array_merge($_POST, $_FILES), $definitions);
    } elseif (isset($_POST['form_type']) && $_POST['form_type'] === 'user_data') {
        $errors = UserFormController::updateData(array_merge($_POST, $_FILES), $definitionDatas);
    }
}

if (!empty($_POST) && array_key_exists('logout', $_POST) && !empty($_POST['logout'])) {
    $user = new User;
    $order = new Orders;
    $user->logout();
}

if (empty($_SESSION['logged_in']['id'])) {
    redirect('/login');
}

$userId = $_SESSION['logged_in']['id'];
$loggedUser = $user->filterFillables($_SESSION['logged_in']);
$userData = $user->getByUserId($userId);
$userDataOrders = $user->getByUserOrder($userId);
$loggedUserData = $user->filterFillablesData($userData);
$loggedUserOrders = $order->filterFillablesOrders($userDataOrders);


//pd($loggedUser);
//pd($userData);
//pd($loggedUserData);
//pd($userDataOrders);
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
</head>

<body style="display: flex; flex-wrap: wrap; gap: 15px;">
    <div style="display: flex; flex-direction: column; gap: 15px; width: 250px; margin-left:20px">
        <!-- Alapadatok megjelenítése -->
        <h3>Alapadatok</h3>
        <?php foreach ($definitions as $definition) :
            if (!array_key_exists($definition['key'], $loggedUser) && (!isset($definition['force_show']) || $definition['force_show'] !== true)) {
                continue;
            }
        ?>
            <div style="display: flex; flex-direction: column;">
                <label style="font-weight: bold"><?php echo htmlspecialchars($definition['label'], ENT_QUOTES, 'UTF-8'); ?></label>
                <div><?php echo htmlspecialchars($loggedUser[$definition['key']] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endforeach; ?>

        <!-- Szállítási adatok megjelenítése -->
        <?php foreach ($definitionDatas as $definitionData) : ?>
            <div style="display: flex; flex-direction: column;">
                <div style="font-weight: bold"><?php echo htmlspecialchars($definitionData['label'], ENT_QUOTES, 'UTF-8'); ?></div>
                <div><?php echo htmlspecialchars($loggedUserData[$definitionData['key']] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        <?php endforeach; ?>
        <form method="POST">
            <input type="hidden" name="logout" value="true">
            <input type="submit" value="Kijelentkezés">
        </form>
    </div>
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
            <div style="display: flex; flex-direction: column; gap: 10px;">
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
            <div style="display: flex; flex-direction: column; gap: 10px;">
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

        <!-- Rendelések megjelenítése -->
        <h3>Eddigi rendelések</h3>
        
            <table>
            <thead>
                <tr>
                    <th>Rendelés ID</th>
                    <th>Étel ID</th>
                    <th>Mennyiség</th>
                    <th>Ár</th>
                    <th>Dátum</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($userDataOrders as $order) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['order_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['dish_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['price'], ENT_QUOTES, 'UTF-8'); ?> Ft</td>
                        <td><?php echo htmlspecialchars($order['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>


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