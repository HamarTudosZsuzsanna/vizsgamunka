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

$totalPrice = 0;

if (empty($userDataCart)) {
    echo "";
} else {

    if (!is_array($userDataCart[0])) {
        $userDataCart = [$userDataCart];
    }

    foreach ($userDataCart as $order) {
        if (isset($order['cart_quantity'], $order['cart_price'])) {
            $calculatedPrice = $order['cart_quantity'] * $order['cart_price'];
            $totalPrice += $calculatedPrice;
        }
    }
}

$dishesData = $dishes->getDishesById();
$order_id = time();
$userId = $_SESSION['logged_in']['id'];

//pd($dishesData);
//pd($_SESSION['logged_in']);

if (!empty($_POST)) {
    $errors = CartController::addToCart($_POST, $definitionDishes);
}

if (!empty($_POST) && isset($_POST['createOrder'])) {

    $errors = OrdersController::saveOrders($_POST, $definitionDishes);
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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>DONAUTS termékek</title>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function confirmDelete() {
            return confirm("Biztosan törölni szeretnéd ezt a terméket?");
        };
    </script>
    <style>
        .fontsize {
            font-size: 24px;
        }
        body {
            background-image: url('/assets/img/order-bcg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .product-img {
            width: 250px;
            height: 250px;
        }
    </style>
</head>

<body class="container">
    <header>
        <div class="container">
            <nav class="navbar navbar-expand-md">
                <div class="container">
                    <a class="navbar-brand fw-bold d-md-none" href="#">
                        DONUTS & COFFEE
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mx-auto align-items-center">
                            <li class="nav-item">
                                <a class="nav-link active" aria-current="page" href="/order/">Termékek</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/">Kapcsolat</a>
                            </li>
                            <a class="navbar-brand fw-bold d-none d-md-block" href="/">
                                DONUTS & COFFEE
                            </a>
                            <li class="nav-item">
                                <a class="nav-link" href="/profile/">Profil</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/admin/">Admin</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <div>
        <div>
            <h3 class="text-center m-3 fw-bold">Termékek</h3>
            <?php
            if (isset($_SESSION['logged_in']) && isset($_SESSION['logged_in']['first_name'])) {
                $customerName = 'Üdvözlünk ' . htmlspecialchars($_SESSION['logged_in']['first_name'], ENT_QUOTES, 'UTF-8') . '!';
            } else {
                $customerName = 'Rendelés leadásához <a href="/login/" class="text-decoration-none text-danger">jelentkezz be!</a>';
            }
            ?>
            <h5 class="text-center m-3 fst-italic"><?php echo ($customerName); ?></h5>
            <div class="text-center">
                <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#cartModal">Kosár megnyitása</button>
                <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cartModalLabel">Kosár tartalma</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" style="width: auto;">
                                <div class="col-12 bg-secondary text-light">
                                    <div class="body d-flex flex-row justify-content-around fw-bold align-items-center p-2">
                                        <p class="text m-0">Termék</p>
                                        <p class="text m-0">Mennyiség</p>
                                        <p class="text m-0">Egységár</p>
                                        <p class="text m-0">Ár</p>
                                        <p class="text m-0">Törlés</p>
                                    </div>
                                </div>
                                <?php
                                foreach ($userDataCart as $order) :
                                    $calculatedPrice = $order['cart_quantity'] * $order['cart_price'];
                                ?>
                                    <div class="d-flex g-0">
                                        <div class="col-12">
                                            <div class="body d-flex flex-row justify-content-around align-items-center">
                                                <p class="text m-0"><?php echo htmlspecialchars($dishes->getDishNameById($order['cart_product_id']), ENT_QUOTES, 'UTF-8'); ?></p>
                                                <p class="text m-0"><?php echo htmlspecialchars($order['cart_quantity'], ENT_QUOTES, 'UTF-8'); ?> db</p>
                                                <p class="text m-0"><?php echo htmlspecialchars($order['cart_price'], ENT_QUOTES, 'UTF-8'); ?> Ft</p>
                                                <p class="text m-0"><?php echo htmlspecialchars($calculatedPrice, ENT_QUOTES, 'UTF-8'); ?> Ft</p>
                                                <form action="" method="POST" onsubmit="return confirmDelete()" class="mt-2">
                                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($order['id']) ?>" />
                                                    <input type="submit" name="deleteItem" value="x" class="btn btn-outline-danger btn-sm" />
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                <?php endforeach; ?>
                                <div class="col-12 bg-secondary text-light">
                                    <div class="body d-flex flex-row justify-content-between fw-bold align-items-center p-2">
                                        <p class="text m-0">Összesen</p>
                                        <p class="text m-0"><?php echo htmlspecialchars($totalPrice, ENT_QUOTES, 'UTF-8'); ?> Ft </p>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Bezárás</button>
                                <form action="" method="POST" class="cart-form">
                                    <?php
                                    $totalPrice = 0;
                                    foreach ($userDataCart as $order) :
                                        $calculatedPrice = $order['cart_quantity'] * $order['cart_price'];
                                        $totalPrice += $calculatedPrice;
                                        $order_id = time();
                                    ?>
                                        <input type="hidden" name="product_id[]" value="<?php echo $order['cart_product_id']; ?>" />
                                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
                                        <input type="hidden" name="user_id" value="<?php echo $userId; ?>" />
                                        <input type="hidden" name="quantity[]" value="<?php echo $order['cart_quantity']; ?>" />
                                        <input type="hidden" name="price[]" value="<?php echo $order['cart_price']; ?>" />
                                    <?php endforeach; ?>
                                    <input type="hidden" name="total_price" value="<?php echo htmlspecialchars($totalPrice, ENT_QUOTES, 'UTF-8'); ?>" />

                                    <div class="d-grid gap-2">
                                        <button class="btn btn-dark mt-2" name="createOrder" type="submit">Megrendelés</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-around p-2 m-2 flex-wrap">
                <?php foreach ($dishesData as $dish) : ?>
                    <div class="card m-2" style="width: 18rem;">
                        <?php if (!empty($dish['dishes_image'])): ?>
                            <img src="<?php echo htmlspecialchars($dish['dishes_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Dishes Image" class="card-img-top p-4 product-img"><br>
                        <?php else: ?>
                            <strong>Kép:</strong> Nincs kép<br>
                        <?php endif; ?>
                        <div class="card-body">
                            <h5 class="card-title text-center fw-bold"><?php echo htmlspecialchars($dish['dishes_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($dish['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="card-text fw-bold text-end fontsize"><?php echo htmlspecialchars($dish['price'], ENT_QUOTES, 'UTF-8'); ?> Ft</p>
                            <p class="card-text fst-italic">kategória: <?php echo htmlspecialchars($dish['categories'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <form action="" method="POST" class="cart-form">
                                <input type="hidden" name="cart_product_id" value="<?php echo $dish['id']; ?>" />
                                <input type="hidden" name="cart_user_id" value="<?php echo $userId; ?>" />
                                <input type="number" name="cart_quantity" value="1" min="1" />
                                <input type="hidden" name="cart_price" value="<?php echo $dish['price']; ?>" />
                                <input type="hidden" name="total_price" value="<?php echo $dish['price']; ?>" />
                                <div class="d-grid gap-2">
                                    <button class="btn btn-dark mt-2" type="submit">Kosárba</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        </div>

    </div>

</body>


</html>