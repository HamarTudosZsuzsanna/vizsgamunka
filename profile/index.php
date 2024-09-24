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
//$loggedUserOrders = $order->filterFillablesOrders($userDataOrders);


//pd($loggedUser);
//pd($userData);
//pd($loggedUserData);
//pd($userDataOrders);
?>

<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/profil.css">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <title>Profil</title>
</head>

<body>

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

    <div class="container">
        <div class="main-body">
            <div class="row col-sm">
                <div class="col-md-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">
                                <div class="mt-3">

                                    <h4><?php echo htmlspecialchars($loggedUser['last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($loggedUser['first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <p class="text-secondary mb-1">E-mail</p>
                                    <p class="text-muted font-size-sm"><?php echo htmlspecialchars($loggedUser['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="text-secondary mb-1">Telefonszám</p>
                                    <p class="text-muted font-size-sm"><?php echo htmlspecialchars($loggedUserData['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="text-secondary mb-1">Utca</p>
                                    <p class="text-muted font-size-sm"><?php echo htmlspecialchars($loggedUserData['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="text-secondary mb-1">Házszám</p>
                                    <p class="text-muted font-size-sm"><?php echo htmlspecialchars($loggedUserData['number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>

                                </div>
                            </div>
                        </div>
                    </div>
                    <form method="POST" class="mt-3">
                        <input type="hidden" name="logout" value="true">
                        <div class="d-grid">
                            <input type="submit" class="btn btn-dark btn-lg" value="Kijelentkezés" />
                        </div>
                    </form>

                </div>
                <div class="col-md-8">
                    <div class="row col-sm">
                        <div class="col-sm-6 mb-3">
                            <div class="card h-100">
                                <form method="POST" class="card-body m-2">
                                    <input type="hidden" name="form_type" value="user" />
                                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($loggedUser['id'], ENT_QUOTES, 'UTF-8'); ?>" />
                                    <?php foreach ($definitions as $definition) :
                                        if (in_array($definition['key'], ['password', 'password_repeat'])) {
                                            continue;
                                        }
                                        if (!array_key_exists($definition['key'], $loggedUser) && (!isset($definition['force_show']) || $definition['force_show'] !== true)) {
                                            continue;
                                        }
                                    ?>
                                        <div class="row">
                                            <label class="col-sm-4"><?php echo htmlspecialchars($definition['label'], ENT_QUOTES, 'UTF-8'); ?></label>
                                            <input
                                                type="<?php echo htmlspecialchars($definition['type'] ?? 'text', ENT_QUOTES, 'UTF-8'); ?>"
                                                name="<?php echo htmlspecialchars($definition['key'], ENT_QUOTES, 'UTF-8'); ?>"
                                                value="<?php echo htmlspecialchars($loggedUser[$definition['key']] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                class="col-sm-8 text-secondary" />
                                            <?php if (isset($errors[$definition['key']]) && !empty($errors[$definition['key']])): ?>
                                                <div style="color: red;"><?php echo htmlspecialchars($errors[$definition['key']], ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <hr>
                                    <?php endforeach; ?>

                                    <!-- Általános űrlaphiba megjelenítése -->
                                    <?php if (isset($errors['form_errors'])): ?>
                                        <div style="color: red;"><?php echo htmlspecialchars($errors['form_errors'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php endif; ?>

                                    <div>
                                        <input type="submit" class="btn btn-outline-secondary" value="Módosítás" />
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <div class="card h-100">
                                <form method="POST" class="card-body m-2">
                                    <input type="hidden" name="form_type" value="user_data" />
                                    <?php foreach ($definitionDatas as $definitionData) : ?>
                                        <div class="row">
                                            <label class="col-sm-4"><?php echo htmlspecialchars($definitionData['label'], ENT_QUOTES, 'UTF-8'); ?></label>
                                            <input
                                                type="<?php echo htmlspecialchars($definitionData['type'] ?? 'text', ENT_QUOTES, 'UTF-8'); ?>"
                                                name="<?php echo htmlspecialchars($definitionData['key'], ENT_QUOTES, 'UTF-8'); ?>"
                                                value="<?php echo htmlspecialchars($loggedUserData[$definitionData['key']] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                class="col-sm-8 text-secondary" />
                                            <?php if (isset($errors[$definitionData['key']]) && !empty($errors[$definitionData['key']])): ?>
                                                <div style="color: red;"><?php echo htmlspecialchars($errors[$definitionData['key']], ENT_QUOTES, 'UTF-8'); ?></div>
                                            <?php endif; ?>
                                        </div>
                                        <hr>
                                    <?php endforeach; ?>
                                    <?php if (isset($errors['form_errors'])): ?>
                                        <div style="color: red;"><?php echo htmlspecialchars($errors['form_errors'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php endif; ?>

                                    <div>
                                        <input type="submit" class="btn btn-outline-secondary" value="Módosítás" />
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="row col-sm">
                        <div class="col-sm ">
                            <div class="card h-100 bg-secondary text-light">
                                <table class="m-3">
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
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
</body>

</html>