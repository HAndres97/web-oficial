<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- Link de Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Link de Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
    <!-- CSS Registrar Cliente -->
    <link rel="stylesheet" href="css/registrar_cliente.css">
    <!-- CSS Clientes -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />
    <link href="https://cdn.datatables.net/v/dt/dt-2.1.8/datatables.min.css" rel="stylesheet">
    <!-- CSS Personalizado -->
    <style>
        .nav-link:hover {
            color: rgb(7, 13, 19);
            /* Cambia el color del texto al pasar el ratón */
            text-decoration: underline;
            /* Añade subrayado al pasar el ratón */
        }

        .nav-link.active {
            color: #000 !important;
            /* Cambia el color del texto a negro */
        }
    </style>
    <!-- JavaScript General -->
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <!-- JavaScript Registrar Cliente -->
    <script defer src="js/registrar_cliente.js"></script>
    <script defer src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- <script defer src="js/clientes.js"></script> -->
    <!-- Links de select2 -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="js/clientes.js" async defer></script>
</head>

<body>
    <?php
    // Obtener el nombre del archivo actual
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor03"
                aria-controls="navbarColor03" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarColor03">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link display-6 <?php echo $current_page == 'index.php' ? 'active' : ''; ?>"
                            href="index.php">Inicio
                            <span class="visually-hidden">(current)</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link display-6 <?php echo $current_page == 'clientes.php' ? 'active' : ''; ?>"
                            href="clientes.php">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link display-6 <?php echo $current_page == 'prestamos.php' ? 'active' : ''; ?>"
                            href="prestamos.php">Préstamos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link display-6 <?php echo $current_page == 'inversiones.php' ? 'active' : ''; ?>"
                            href="inversiones.php">Inversiones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link display-6 <?php echo $current_page == 'pagos.php' ? 'active' : ''; ?>"
                            href="pagos.php">Gastos</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link display-6" href="outlogin.php">
                            <i class="bi bi-box-arrow-right"></i> Salir
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>