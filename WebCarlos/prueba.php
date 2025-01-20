<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <!-- Chosen CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
    <!-- Chosen JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
</head>

<body>
    <?php
    // Obtener el nombre del archivo actual
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    <nav class="navbar navbar-expand-lg bg-light" data-bs-theme="light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor03"
                aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon">Menú</span>
            </button>
            <div class="collapse navbar-collapse" id="navbarColor03">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link display-6 <?php echo $current_page == 'index.php' ? 'active' : ''; ?>" href="index.php">Inicio
                            <span class="visually-hidden">(current)</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link display-6 <?php echo $current_page == 'clientes.php' ? 'active' : ''; ?>" href="clientes.php">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link display-6 <?php echo $current_page == 'prestamos.php' ? 'active' : ''; ?>" href="prestamos.php">Préstamos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link display-6 <?php echo $current_page == 'inversiones.php' ? 'active' : ''; ?>" href="inversiones.php">Inversiones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link display-6 <?php echo $current_page == 'pagos.php' ? 'active' : ''; ?>" href="pagos.php">Pagos</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="row">
            <form class="col-md-4">
                <label>Select</label>
                <select class='form-select form-select-lg text-uppercase chosen-select' aria-label='Large select example' name='buscarCliente' required id="select-enlace">
                    <option selected>Seleccionar..</option>
                    <option value="enlace1.php">Select</option>
                    <option value="enlace2.php">Car</option>
                    <option value="enlace3.php">Bike</option>
                    <option value="enlace4.php">Scooter</option>
                    <option value="enlace5.php">Cycle</option>
                    <option value="enlace6.php">Horse</option>
                </select>
                <button class='btn btn-secondary' type='submit'>
                    <i class='bi bi-search'></i>
                </button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('.chosen-select').chosen({
                width: '100%',
                no_results_text: 'No se encontraron resultados'
            });

            // Redirigir al usuario cuando se selecciona una opción
            $('#select-enlace').on('change', function () {
                var enlace = $(this).val();
                if (enlace) {
                    window.location.href = enlace;
                }
            });
        });
    </script>
</body>
</html>