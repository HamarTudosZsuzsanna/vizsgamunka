<?php
require('../../includes/init.php');
require('../../classes/DishesFormController.php');
require_once('../../models/Dishes.php');

session_start();

$definitionDishes = FormController::getDefinition('dishes');
$user = new User();
$dishes = new Dishes();

//pd($dishes);


if (!empty($_POST) && array_key_exists('logout', $_POST) && !empty($_POST['logout'])) {
    $user = new User;
    $user->logout();
}

if (empty($_SESSION['logged_in']['id'])) {
    redirect('/login');
}

$userId = $_SESSION['logged_in']['id'];
//$loggedUser = $user->filterFillables($_SESSION['logged_in']);
$dishesData = $dishes->getDishesById();

//$dishesData = $dishes->filterFillablesDishes($dishesId);


?>


<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
</head>

<body style="display: flex; flex-wrap: wrap; gap: 15px;">
    <div style="display: flex; flex-direction: column; gap: 15px; width: 250px; margin-left:20px">
        <!-- Alapadatok megjelenítése -->
        <h3>Termékek</h3>
        <div style="display: flex; flex-direction: column; flex-wrap: wrap; height: 100vh; gap:10px">
            <?php foreach ($dishesData as $dish) : ?>

                    <div style="display: flex; flex-direction: column; border: thin solid black; gap: 10px; ">
                        <div><strong>Termék neve:</strong> <?php echo htmlspecialchars($dish['dishes_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div><strong>Leírás:</strong> <?php echo htmlspecialchars($dish['description'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div><strong>Ár:</strong> <?php echo htmlspecialchars($dish['price'], ENT_QUOTES, 'UTF-8'); ?> Ft</div>
                        <div><strong>Kategória:</strong> <?php echo htmlspecialchars($dish['categories'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php if (!empty($dish['dishes_image'])): ?>
                            <div><strong>Kép:</strong> <img src="<?php echo htmlspecialchars($dish['dishes_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Dishes Image" style="max-width: 100px;"></div>
                        <?php else: ?>
                            <div><strong>Kép:</strong> Nincs kép</div>
                        <?php endif; ?>
                    </div>

            <?php endforeach; ?>
        </div>
        <form method="POST">
            <input type="hidden" name="logout" value="true">
            <input type="submit" value="Kijelentkezés">
        </form>
    </div>


</body>

</html>