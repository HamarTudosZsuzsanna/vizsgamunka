<?php

use function PHPSTORM_META\type;

require('../../../includes/init.php');
require('../../../classes/DishesFormController.php');
require_once('../../../models/Dishes.php');

session_start();
$errors = [];
$dishes = new Dishes();
$definitions = FormController::getDefinition('dishes');

if (empty($_SESSION['logged_in']) || $_SESSION['logged_in']['role'] !== 'admin') {
    redirect('/login');
}

$userId = $_SESSION['logged_in']['id'];
$dishesSlug = $_GET['id'];
$dishesData = $dishes->getDishesBySlug($dishesSlug);
$dishesImage = $dishes->getDishesImageBySlug($dishesSlug);

$errors = DishesFormController::updateDishes(array_merge($_POST, $_FILES), $definitions);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/profil.css">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <title>Termék szerkesztése</title>
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
    <div class="card container bg-dark text-light mt-3">
        <form action="" method="POST" enctype="multipart/form-data" classs="d-flex flex-column">
            
            <?php foreach ($definitions as $definition) : ?>
                <div class="d-flex flex-column gap-2 mt-2">
                    <label for="<?php echo htmlspecialchars($definition['key']); ?>">
                        <?php echo htmlspecialchars($definition['label']); ?>
                    </label>

                    <?php if ($definition['type'] != 'file') : ?>
                        <input
                            type="<?php echo htmlspecialchars($definition['type'] ?? 'text'); ?>"
                            id="<?php echo htmlspecialchars($definition['key']); ?>"
                            name="<?php echo htmlspecialchars($definition['key']); ?>"
                            value="<?php echo htmlspecialchars($dishesData[$definition['key']] ?? ''); ?>"
                            required />
                    <?php else : ?>

                        <?php if (!empty($dishesData[$definition['key']])) : ?>
                            <img src="<?php echo htmlspecialchars($dishesData[$definition['key']]); ?>" alt="Kép" style="max-width: 100px;" />
                            <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($dishesData[$definition['key']]); ?>" />
                        <?php else : ?>
                            <p>Nincs feltöltött kép</p>
                        <?php endif; ?>
                        <input type="file" name="<?php echo htmlspecialchars($definition['key']); ?>" />
                    <?php endif; ?>

                    <?php if (is_array($errors) && array_key_exists($definition['key'], $errors) && !empty($errors[$definition['key']])) : ?>
                        <div style="color: red;"><?php echo htmlspecialchars($errors[$definition['key']]); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if (is_array($errors) && array_key_exists('form_errors', $errors)) : ?>
                <div style="color: red;"><?php echo htmlspecialchars($errors['form_errors']); ?></div>
            <?php endif; ?>

            <input type="submit" value="Termék frissítése" class="btn btn-outline-dark container bg-warning text-dark mt-3 mb-3 container card" />
        </form>
    </div>
    <a href="/admin/dishes/" class="btn btn-outline-dark container container card mt-3">Vissza</a>
</body>

</html>