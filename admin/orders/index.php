<?php
require('../../includes/init.php');
require('../../classes/UserFormController.php');
require_once('../../models/Dishes.php');
require_once('../../models/Orders.php');
session_start();

$user = new User();
$dishes = new Dishes();
$orders = new Orders();

$dishesId = [];

if (!empty($_POST) && array_key_exists('logout', $_POST) && !empty($_POST['logout'])) {
    $user = new User;
    $user->logout();
}

if (empty($_SESSION['logged_in']) || $_SESSION['logged_in']['role'] !== 'admin') {
    redirect('/login');
}

$dishesData = $dishes->getDishesById(); //összes termék
$ordersData = $orders->getOrdersById(); //rendelés alapadatok

$userId = [];
$orderIds = [];
foreach ($ordersData as $order) {
    $orderIds[] = $order['order_id'];
    $userId[] = $order['user_id'];
}

$customerDataItem = $user->getByUserId($userId); //megrendelő címe
$customerData = $user->getById($userId);  // megrendelő adatai
$orderIdData = $user->getByOrderItem($orderIds); // rendelés részletei


//pd($dishesData);
//pd($ordersData); //rendelés alapadatok
//pd($orderIdData); //rendelés részletei
//pd($customerData); // megrendelő adatai
//pd($customerDataItem); // megrendelő címe
//pd($userId); // 
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/profil.css">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <title>Admin felület</title>
    <style>
        .accordion-button {
            display: flex;
            flex-direction: row;
            justify-content: space-around !important;
        }
    </style>
</head>

<body>

    <header>
        <nav class="navbar navbar-expand-lg bg-dark">
            <div class="container-fluid ">
                <a class="navbar-brand  text-light" href="#">ADMIN FELÜLET</a>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active  text-light" aria-current="page" href="/admin/orders/">Rendelések</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link  text-light" href="/admin/dishes/">Termékek szerkesztése</a>
                        </li>
                    </ul>
                    <form method="POST" class="d-flex row justify-content-end me-2">
                        <input type="hidden" name="logout" value="true">
                        <input type="submit" class="btn btn-dark btn-lg" value="Kijelentkezés" />
                    </form>

                </div>
            </div>
        </nav>
    </header>

    <a href="#" class="btn btn-outline-dark container bg-warning text-dark mt-3 container card">Új rendelés létrehozása</a>
    <div class="card container bg-dark text-light mt-3">
        <table class="m-3">
            <thead>
                <tr class="text-center text-uppercase text-light">
                    <th class="pb-2">Megrendelő neve </th>
                    <th class="pb-2">Teljes összeg</th>
                    <th class="pb-2">Rendelés állapota</th>
                    <th class="pb-2">rendelés dátuma</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordersData as $order) : ?>
                    <tr class="text-center">
                        <td><?php echo htmlspecialchars($order['user_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['total_price'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['order_status'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php
                    $orderItems = $user->getByOrderItem([$order['order_id']]);
                    ?>
                    <tr class="bg-light text-dark">
                        <td>
                            <?php if (!empty($orderItems)) : ?>
                                <ul>
                                    <?php foreach ($orderItems as $item) : ?>
                                        <li>
                                            <span class="fw-italic"><?php echo htmlspecialchars($dishes->getDishNameById($item['product_id']), ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="fst-italic"> | (Mennyiség: <?php echo htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8'); ?> db)</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>

                        <?php
                        $customerItems = $user->getById([$order['user_id']]);
                        ?>
                        <td>
                            <?php if (!empty($customerItems)) : ?>
                                <ul>
                                    <?php foreach ($customerItems as $item) : ?>
                                        <li>
                                            <span class="fw-italic"><?php echo htmlspecialchars($item['first_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="fst-italic"><?php echo htmlspecialchars($item['last_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </li>
                                        <li>
                                            <span class="fst-italic"><?php echo htmlspecialchars($item['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                        <?php
                        $customerDataItems = $user->getByUserIdOrder([$order['user_id']]);
                        ?>
                        <td>
                        <?php if (!empty($customerDataItems)) : ?>
                                <ul>
                                    <?php foreach ($customerDataItems as $item) : ?>
                                        <li>
                                            <span class="fw-italic"><?php echo htmlspecialchars($item['phone'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </li>
                                        <li>
                                            <span class="fst-italic"><?php echo htmlspecialchars($item['address'], ENT_QUOTES, 'UTF-8'); ?></span>
                                            <span class="fst-italic"><?php echo htmlspecialchars($item['number'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </li>  
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button class="btn bg-info btn-sm ">Teljesítve</button>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</body>

</html>