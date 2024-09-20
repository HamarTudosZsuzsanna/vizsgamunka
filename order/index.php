<?php
require('../includes/init.php');
require('../classes/OrdersController.php');
require('../classes/CartController.php');
require_once('../models/Orders.php');
require_once('../models/Dishes.php');
require_once('../models/Cart.php');
require_once('../models/User.php');

session_start();

$errors = [];
$definitionDishes = FormController::getDefinition('dishes');
$userId = $_SESSION['logged_in']['id'];

$order = new Orders();
$dishes = new Dishes();
$user = new User();
$cart = new Cart();
$dishesId = [];

$userDataCart = $user->getByUserOrderCart($userId);

$totalPrice = 0; // Végösszeg inicializálása

foreach ($userDataCart as $order) {
    $calculatedPrice = $order['cart_quantity'] * $order['cart_price'];
    $totalPrice += $calculatedPrice; // Hozzáadjuk a kalkulált árat a végösszeghez
}

$dishesData = $dishes->getDishesById();

//pd($dishesData);
//pd($userDataCart);

if (!empty($_POST)) {
    $errors = CartController::addToCart($_POST, $definitionDishes);
}

if (isset($_POST['deleteItem']) && isset($_POST['id'])) {
    $cartItemId = (int)$_POST['id'];
    $deleteSuccess = $cart->deleteCartItem($cartItemId);
    header('Location: /order/cart/?delete=' . ($deleteSuccess ? 'success' : 'error'));
    redirect('/order');
    exit;
}


?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <title>Rendelések</title>
    <script>
        function confirmDelete() {
            return confirm("Biztosan törölni szeretnéd ezt a terméket?");
        }
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h3 {
            color: #333;
        }

        .product,
        .order {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>

    <div style="display: flex; flex-wrap: wrap; gap: 15px;">
        <div style="flex: 1; min-width: 250px;">
            <h3>Termékek</h3>
            <div>
                <?php foreach ($dishesData as $dish) : ?>
                    <div class="product">
                        <strong>Termék neve:</strong> <?php echo htmlspecialchars($dish['dishes_name'], ENT_QUOTES, 'UTF-8'); ?><br>
                        <strong>Leírás:</strong> <?php echo htmlspecialchars($dish['description'], ENT_QUOTES, 'UTF-8'); ?><br>
                        <strong>Ár:</strong> <?php echo htmlspecialchars($dish['price'], ENT_QUOTES, 'UTF-8'); ?> Ft<br>
                        <strong>Kategória:</strong> <?php echo htmlspecialchars($dish['categories'], ENT_QUOTES, 'UTF-8'); ?><br>
                        <?php if (!empty($dish['dishes_image'])): ?>
                            <strong>Kép:</strong> <img src="<?php echo htmlspecialchars($dish['dishes_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Dishes Image" style="max-width: 100px;"><br>
                        <?php else: ?>
                            <strong>Kép:</strong> Nincs kép<br>
                        <?php endif; ?>

                        <form action="" method="POST">
                            <input type="hidden" name="cart_product_id" value="<?php echo $dish['id']; ?>" />
                            <input type="hidden" name="cart_user_id" value="<?php echo $userId; ?>" />
                            <input type="number" name="cart_quantity" value="1" min="1" />
                            <input type="hidden" name="cart_price" value="<?php echo $dish['price']; ?>" />
                            <input type="hidden" name="total_price" value="<?php echo $dish['price']; ?>" />
                            <input type="submit" value="Kosárba">
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div style="flex: 1; min-width: 250px;">
            <h3>Kosár tartalma</h3>
            <table>
                <thead>
                    <tr>
                        <th>Rendelés ID</th>
                        <th>Felhasználó ID</th>
                        <th>Mennyiség</th>
                        <th>Ár</th>
                        <th>Egyedi Id</th>
                        <th>Tétel összesen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userDataCart as $order) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dishes->getDishNameById($order['cart_product_id']), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($order['cart_user_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($order['cart_quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($order['cart_price'], ENT_QUOTES, 'UTF-8'); ?> Ft</td>
                            <td><?php echo htmlspecialchars($order['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <?php
                                echo htmlspecialchars($calculatedPrice, ENT_QUOTES, 'UTF-8');
                                ?> Ft
                            </td>
                            <td>
                                <form action="" method="POST" onsubmit="return confirmDelete()">
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($order['id']) ?>" />
                                    <input type="submit" name="deleteItem" value="termék törlése" />
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td style="font-weight: bold;">Összesen</td>
                        <td style="font-weight: bold;"><?php
                                echo htmlspecialchars($totalPrice, ENT_QUOTES, 'UTF-8');
                                ?> Ft</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>