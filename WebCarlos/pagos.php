<?php
include 'template/sesion.php';
include 'template/cabecera.php';

// Función para eliminar un gasto
function eliminar_gasto($conn, $delete_id, $cantidad) {
    $sql = "DELETE FROM gastos WHERE id = '$delete_id'";
    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success text-center' role='alert'>
                Gasto Eliminado Correctamente
            </div>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    actualizar_patrimonio($conn, $cantidad, 'sumar');
}

// Función para registrar un gasto
function registrar_gasto($conn, $fname, $cantidad, $date) {
    $sql = "INSERT INTO gastos (id_patrimonio, nombre, cantidad, fecha) VALUES ('1','$fname', '$cantidad', '$date')";
    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success text-center' role='alert'>
                Gasto Creado Correctamente
            </div>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    actualizar_patrimonio($conn, $cantidad, 'restar');
}

// Función para actualizar el patrimonio
function actualizar_patrimonio($conn, $cantidad, $accion) {
    $patrimonio = mysqli_query($conn, "SELECT * FROM patrimonios WHERE id = 1");
    $row = mysqli_fetch_array($patrimonio);
    if ($accion == 'sumar') {
        $patrimonio_cantidad = $row['cantidad'] + $cantidad;
    } else {
        $patrimonio_cantidad = $row['cantidad'] - $cantidad;
    }
    $sql = 'UPDATE patrimonios SET cantidad = ' . $patrimonio_cantidad . ' WHERE id = 1';
    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success text-center' role='alert'>
                Cantidad Actualizada Correctamente : " . $row['cantidad'] . " a " . $patrimonio_cantidad . " €
            </div>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}

// Eliminar gasto si se recibe el parámetro Borrarid
if (isset($_GET['Borrarid'])) {
    eliminar_gasto($conn, $_GET['Borrarid'], $_GET['cantidad']);
}

// Registrar gasto si se envía el formulario
if (isset($_POST['submit'])) {
    registrar_gasto($conn, $_POST['fname'], $_POST['cantidad'], $_POST['date']);
}

// Obtener lista de meses y años para el filtro
$lista_meses_anos = mysqli_query($conn, "SELECT DISTINCT MONTH(fecha) AS MES, YEAR(fecha) AS ANO FROM gastos ORDER BY ANO DESC;");
$mensaje_error = "No hay pagos registrados";

/// Búsqueda de pagos
$where = " WHERE 1=1 ";
$buscarNombre = "";
$buscarFecha = "";
$total_gastos = 0;
$order_by = " ORDER BY fecha DESC";

// Buscamos por nombre del gasto
if (!empty($_GET['buscarNombre'])) {
    $buscarNombre = $_GET['buscarNombre'];
    $where .= " AND nombre LIKE '%$buscarNombre%'";
}

// Buscamos por fecha
if (!empty($_GET['buscarFecha'])) {
    $buscarFecha = $_GET['buscarFecha'];
    $where .= ' AND fecha LIKE "' . $buscarFecha . '%"';
}

$sql = "SELECT * FROM gastos $where $order_by";
$lista_pagos = mysqli_query($conn, $sql);

// Variables para la paginación
$num_pagos = mysqli_num_rows($lista_pagos); // Contamos el número de pagos
$pagos_por_pagina = 10; // Número de pagos por página
$paginas = ceil($num_pagos / $pagos_por_pagina); // Calculamos el número de páginas

// Determinar la página actual
$nume = isset($_REQUEST["nume"]) ? $_REQUEST["nume"] : '1';
if ($nume == "" || !is_numeric($nume) || $nume < 1) {
    $nume = 1;
}
$pagina = (int)$nume;
$inicio = ($pagina - 1) * $pagos_por_pagina;

// Realizar la consulta con límite y desplazamiento
$sql_paginado = "SELECT * FROM gastos $where $order_by LIMIT $inicio, $pagos_por_pagina";
$lista_pagos_paginados = mysqli_query($conn, $sql_paginado);
?>

<<!-- Formulario de registro de gastos -->
<div class="body">
    <div class="container-xxl px-1 py-5 mx-auto">
        <div class="row d-flex justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
                <h3>Registrar Gasto</h3>
                <div class="card">
                    <h5 class="text-center mb-4">Rellenar todos los campos</h5>
                    <form method="POST" action="pagos.php" class="form-card">
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Nombre del gasto<span class="text-danger"> *</span></label>
                                <input type="text" id="fname" name="fname" placeholder="Gasto" required>
                            </div>
                        </div>
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Cantidad<span class="text-danger"> *</span></label>
                                <input type="number" id="lname" name="cantidad" placeholder="1000.56" step="0.01" min="0" max="1000000" required>
                            </div>
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Fecha<span class="text-danger"> *</span></label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="fecha" name="date" value="<?php echo date('Y-m-d'); ?>" required />
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="form-group col-sm-6">
                                <button type="submit" class="btn-block btn-primary" name="submit">Registrar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de pagos -->
<div class="container mt-5">
    <h3 class="text-center">Lista de Pagos</h3>
    <form method="GET" action="pagos.php">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mx-4 my-4 align-middle" id="tablaPagos" style="min-width: 800px;">
                <caption>Lista de Pagos</caption>
                <thead>
                    <tr class="text-center text-uppercase table-light">
                        <th></th>
                        <th scope="col">
                            <div class="input-group">
                                <input type="text" class="form-control" name="buscarNombre" placeholder="Buscar Nombre" value="<?php echo htmlspecialchars($buscarNombre); ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-secondary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th scope="col" class="text-center text-uppercase">
                            <div class="input-group">
                                <select class="form-select" name="buscarFecha" id="busquedaFecha">
                                    <?php if (!empty($buscarFecha)): ?>
                                        <option selected class="text-center text-uppercase" name="buscarFecha" value="<?php echo htmlspecialchars($buscarFecha); ?>"><?php echo htmlspecialchars($buscarFecha); ?></option>
                                    <?php else: ?>
                                        <option selected class="text-center text-uppercase" name="buscarFecha" value="">Ordenar por mes y año</option>
                                    <?php endif; ?>
                                    <?php while ($row = mysqli_fetch_array($lista_meses_anos)): ?>
                                        <?php $mes = controladorFecha($row['MES']); ?>
                                        <?php $ano = $row['ANO']; ?>
                                        <option class="text-center text-uppercase" value="<?php echo $ano . '-' . $mes; ?>"><?php echo regresar_mes($mes) . " - $ano"; ?></option>
                                    <?php endwhile; ?>
                                </select>
                                <div>
                                    <button class="btn btn-secondary" type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </div>
                        </th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                    <tr class="text-center text-uppercase table-light">
                        <th>#</th>
                        <th scope="col">NOMBRE DEL GASTO</th>
                        <th scope="col">CANTIDAD</th>
                        <th scope="col">FECHA</th>
                        <th scope="col">ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $contador = $inicio + 1;
                    while ($row = mysqli_fetch_array($lista_pagos_paginados)): ?>
                        <tr class="text-center text-uppercase">
                            <td><?php echo $contador++; ?></td>
                            <td><?php echo $row['nombre']; ?></td>
                            <td><?php echo $row['cantidad']; ?> €</td>
                            <td><?php echo formatear_fecha($row['fecha']); ?></td>
                            <td>
                                <a href="modificar_pago.php?id=<?php echo $row['id']; ?>&cantidad=<?php echo $row['cantidad']; ?>"><i class="fa fa-edit mr-2"></i></a>
                                <a href="pagos.php?Borrarid=<?php echo $row['id']; ?>&cantidad=<?php echo $row['cantidad']; ?>"><i class="fa fa-trash text-danger"></i></a>
                            </td>
                        </tr>
                        <?php $total_gastos += $row['cantidad']; ?>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr class="text-center text-uppercase table-light">
                        <th>#</th>
                        <th scope="col">Total Gastos</th>
                        <th scope="col"><?php echo $total_gastos; ?> €</th>
                        <th scope="col"></th>
                        <th scope="col">ACCIONES</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </form>

    <!-- Paginación -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php
            // Mantener los parámetros de búsqueda en los enlaces de paginación
            $query_string = http_build_query([
                'buscarNombre' => $buscarNombre,
                'buscarFecha' => $buscarFecha
            ]);
            for ($i = 1; $i <= $paginas; $i++): ?>
                <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                    <a class="page-link" href="?nume=<?php echo $i; ?>&<?php echo $query_string; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<?php include 'template/footer.php'; ?>