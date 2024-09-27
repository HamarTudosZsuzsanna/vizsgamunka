<?php
require('../../includes/init.php');
require('../../classes/DishesFormController.php');
require_once('../../models/Dishes.php');

session_start();

$definitionDishes = FormController::getDefinition('dishes');
$user = new User();
$dishes = new Dishes();
$dishesId = [];


if (!empty($_POST) && array_key_exists('logout', $_POST) && !empty($_POST['logout'])) {
    $user = new User;
    $user->logout();
}

if (empty($_SESSION['logged_in']) || $_SESSION['logged_in']['role'] !== 'admin') {
    redirect('/login');
}


$userId = $_SESSION['logged_in']['id'];
$dishesData = $dishes->getDishesById();

if (isset($_SESSION['logged_in']['id']) && is_numeric($_SESSION['logged_in']['id'])) {
    $userId = (int)$_SESSION['logged_in']['id'];

    if (isset($_POST['delete']) && isset($_POST['dishes_id']) && is_numeric($_POST['dishes_id'])) {
        $dishesId = (int)$_POST['dishes_id'];
        $deleteSuccess = $dishes->deleteDishes($dishesId);
        header('Location: /admin/dishes/?delete=' . ($deleteSuccess ? 'success' : 'error'));
        redirect('/admin/dishes');
        exit;
    }

    $dishes = $dishes->getDishesById($dishesId);
} else {
    echo "<p>Bejelentkezési hiba. Kérlek, jelentkezz be.</p>";
    exit;
}

if (!is_array($dishesData[0])) {
    $dishesData = [$dishesData];
}

?>


<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/profil.css">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script>
        function confirmDelete() {
            return confirm("Biztosan törölni szeretnéd ezt a terméket?");
        }
    </script>
    <title>Termékek</title>
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

    <a href="/admin/dishes/create/" class="btn btn-outline-dark container bg-success text-light mt-3 container card">Új termék létrehozása</a>
    <div class="card container bg-dark text-light mt-3">
        <table class="m-3">
            <thead>
                <tr class="text-center text-uppercase">
                    <th class="pb-2">Termék képe</th>
                    <th class="pb-2">Termék neve</th>
                    <th class="pb-2">Leírás</th>
                    <th class="pb-2">Ár</th>
                    <th class="pb-2">Termékkategória</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dishesData as $dish) : ?>
                    <tr class="text-center">
                        <td><img src="<?php echo htmlspecialchars($dish['dishes_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Dishes Image" style="max-width: 25px;"></td>
                        <td><?php echo htmlspecialchars($dish['dishes_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($dish['description'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($dish['price'], ENT_QUOTES, 'UTF-8'); ?> Ft</td>
                        <td><?php echo htmlspecialchars($dish['categories'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <form action="" method="POST" onsubmit="return confirmDelete()">
                                <button class="btn btn-sm btn-light"><a href="/admin/dishes/update/?id=<?php echo urlencode($dish['slug']); ?>" class="text-decoration-none text-dark">Termék szerkesztése</a></button>
                                <input type="hidden" name="dishes_id" value="<?php echo htmlspecialchars($dish['id']) ?>" />
                                <input type="submit" name="delete" value="Termék törlése" class="btn btn-sm btn-outline-warning" />
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <hr>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>




</body>

</html>