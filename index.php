<?php

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
    <style>
        .title {
            font-size: 36px;
        }

        body {
            margin-top: 20px;
            background-image: url('/assets/img/bcg3.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
        }

        .leftText {
            display: flex;
            flex-direction: column;
            justify-content: space-around;
            align-items: end;
            height: 100%;
        }
        .textContainer{
            height: 100vh;
        }
    </style>
</head>



<body>
    <header>
        <div class="bg-primary-subtle">
            <nav class="navbar navbar-expand-md ">
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
    <div class="leftText">
        <div class="text-center row justify-content-center col-5 bg-body-tertiary textContainer me-5">
            <div class="row justify-content-center">
                <h2 class="fw-bold m-3">Édes élet, friss élmény</h2>
                <p class="col-sm-6 fst-italic m-2">Fedezd fel a frissen sült fánkok és prémium kávék varázslatos világát! Rendelj otthonod kényelméből és élvezd a tökéletes párosítást minden nap. Frissen készítve, szeretettel szállítva.</p>
                <h3 class="fw-bold m-2">Fánk & Kávé – amikor az ízek találkoznak!</h3>
                <div class="col-sm-4">
                    <img src="/assets/img/ff.png" class="img-fluid" alt="" style="height: 300px; width: auto;">
                </div>
                <div class="col-sm-4 text-center">
                    <img src="/assets/img/cc.png" class="img-fluid" alt="" style="height: 300px; width: auto;">
                </div>
            </div>
            <a href="/order/" class="btn btn-dark m-3" style="width: 200px; height: 40px">Minden termék</a>
        </div>
    </div>

</body>

</html>