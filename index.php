<?php

/*require('../vizsgamunka/includes/init.php');
require('../vizsgamunka/classes/OrdersController.php');
require_once('../vizsgamunka/models/Dishes.php');

session_start();

$errors = [];
$definitionDishes = FormController::getDefinition('dishes');

$dishes = new Dishes();
$dishesId = [];

$dishesData = $dishes->getDishesById();*/

?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>DONUTS</title>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <link href="/assets/css/main.css" rel="stylesheet">
    <style>
        .title {
            font-size: 36px;
        }
    </style>
</head>

<header>
    <div class="container">
        <nav class="navbar navbar-expand-md fixed-top">
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
                        <a class="navbar-brand fw-bold d-none d-md-block title" href="#">
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

<body>
    <div id="carouselExample" class="carousel slide">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="/assets/img/carousel.jpg" class="d-block w-100" alt="...">
            </div>
        </div>
    </div>
    <section class="text-center py-5" id="best-selling">
        <h2 class="fw-bold">TERMÉKEINK</h2>
        <p>Válassz fánkot meg egy kávét, és biztos jól indul a nap!</p>
    </section>

    <div class="text-center row justify-content-center">
        <div class="row justify-content-center">
            <div class="col-sm-4">
                <img src="/assets/img/ff.png" class="img-fluid" alt="" style="height: 300px; width: auto;">
                <h3>DONUTS</h3>
                <p>Különbőző fánkok</p>
            </div>
            <div class="col-sm-4">
                <img src="/assets/img/cc.png" class="img-fluid" alt="" style="height: 300px; width: auto;">
                <h3>COFFEE</h3>
                <p>Különbőző kávék</p>
            </div>
        </div>
        <a href="/order/" class="btn btn-outline-dark rounded-0 px-3 py-2 btn-lg mt-3 mb-3" style="width: 200px;">Minden termék</a>
    </div>



</body>

</html>