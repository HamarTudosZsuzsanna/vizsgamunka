<?php
require('../includes/init.php');
require(CLASSES_DIR . '/UserFormController.php');
$definitions = FormController::getDefinition('user');
$errors = UserFormController::save($_POST, $definitions);

?>
<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <title>Regisztráció</title>
</head>

<body>
    <section class="bg-light p-3 p-md-4 p-xl-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-xxl-11">
                    <div class="card border-light-subtle shadow-sm">
                        <div class="row g-0">
                            <div class="col-12 col-md-6">
                                <img class="img-fluid rounded-start w-100 h-100 object-fit-cover" loading="lazy" src="../assets/img/register.jpg" alt="">
                            </div>
                            <div class="col-12 col-md-6 d-flex align-items-center justify-content-center">
                                <div class="col-12 col-lg-11 col-xl-10">
                                    <div class="card-body p-3 p-md-4 p-xl-5">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="mb-5">
                                                    <h4 class="text-center">A regisztrációhoz kérlek töltsd ki a mezőket!</h4>
                                                </div>
                                            </div>
                                        </div>
                                        <form method="POST">

                                            <div class="row gy-3 overflow-hidden">
                                                <?php foreach ($definitions as $definition) : ?>
                                                    <div class="col-12">
                                                        <div class="form-floating mb-1">
                                                            <input type="<?php echo $definition['type'] ?? 'text'; ?>" class="form-control" name="<?php echo $definition['key']; ?>" id="<?php echo $definition['key']; ?>" placeholder="name@example.com" value="<?php echo FormController::getFieldValue($definition['key']); ?>" />
                                                            <label for="<?php echo $definition['key']; ?>" class="form-label"><?php echo $definition['label']; ?></label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php if (is_array($errors) && array_key_exists('form_errors', $errors)): ?>
                                                    <div style="color: red;"><?php echo $errors['form_errors']; ?></div>
                                                <?php endif; ?>
                                                <div class="column">
                                                    <input type="checkbox" id="accepted" name="accepted" value="accepted">
                                                    <label for="accepted">Elfogadom a felhasználási feltételeket</label>
                                                </div>
                                                <div class="col-12">
                                                    <div class="d-grid">
                                                        <input type="submit" class="btn btn-dark btn-lg" value="Regisztráció" />
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="d-flex gap-2 gap-md-4 flex-column flex-md-row justify-content-md-center mt-5">
                                                    <a href="/login/" class="link-secondary text-decoration-none">Bejelentkezés</a>
                                                    <a href="/" class="link-secondary text-decoration-none">Vissza a kezdőoldalra</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>



</html>

<?php
