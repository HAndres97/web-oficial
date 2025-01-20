<?php
include 'template/sesion.php';
include 'template/cabecera.php';

// Función para registrar una inversión
function registrar_inversion($conn, $fname, $estado_inversion, $cantidad, $date, $ans) {
    $sql = "INSERT INTO inversiones (id_patrimonio, nombre, cantidad, fecha_registro, estado, comentario) VALUES ('1', '$fname', '$cantidad', '$date', '$estado_inversion', '$ans')";
    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success text-center' role='alert'>
                Inversión Creada Correctamente
            </div>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    actualizar_patrimonio($conn, $cantidad, 'restar');
}

// Función para eliminar una inversión
function eliminar_inversion($conn, $delete_id, $cantidad) {
    $sql = "DELETE FROM inversiones WHERE id = '$delete_id'";
    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success text-center' role='alert'>
                Inversión Borrada Correctamente
            </div>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    actualizar_patrimonio($conn, $cantidad, 'sumar');
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

// Registrar inversión si se envía el formulario
if (isset($_POST['submit'])) {
    registrar_inversion($conn, $_POST['fname'], $_POST['inversion'], $_POST['cantidad'], $_POST['date'], $_POST['ans']);
}

// Eliminar inversión si se recibe el parámetro Borrarid
if (isset($_GET['Borrarid'])) {
    eliminar_inversion($conn, $_GET['Borrarid'], $_GET['cantidad']);
}

// Búsqueda de inversiones
$lista_meses_anos = mysqli_query($conn, "SELECT DISTINCT MONTH(fecha_registro) AS MES, YEAR(fecha_registro) AS ANO FROM inversiones ORDER BY ANO DESC;");
$mensaje_error = "No hay inversiones registradas";
$where = " WHERE 1=1 ";
$buscarInversion = "";
$buscarFecha = "";
$buscarEstado = "";
$total_gastos = 0;
$order_by = " ORDER BY fecha_registro DESC";

// Buscamos por nombre de la inversión
if (!empty($_GET['buscarInversion'])) {
    $buscarInversion = $_GET['buscarInversion'];
    $where .= " AND nombre LIKE '%$buscarInversion%'";
}

// Buscamos por fecha
if (!empty($_GET['buscarFecha'])) {
    $buscarFecha = $_GET['buscarFecha'];
    $where .= ' AND fecha_registro LIKE "' . $buscarFecha . '%"';
}

// Buscamos por estado
if (!empty($_GET['buscarEstado'])) {
    $buscarEstado = $_GET['buscarEstado'];
    if ($buscarEstado != 'Seleccionar...') {
        $where .= " AND estado = '$buscarEstado'";
    }
}

$sql = "SELECT * FROM inversiones $where $order_by";
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
$sql_paginado = "SELECT * FROM inversiones $where $order_by LIMIT $inicio, $pagos_por_pagina";
$lista_pagos_paginados = mysqli_query($conn, $sql_paginado);
?>
<!-- Formulario de registro de Inversiones -->
<div class="body">
    <div class="container-xxl px-1 py-5 mx-auto">
        <div class="row d-flex justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
                <h3>Registro de Inversiones</h3>
                <div class="card">
                    <h5 class="text-center mb-4">Rellenar todos los campos</h5>
                    <form method="POST" action="inversiones.php" class="form-card">
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Nombre de la inversión<span class="text-danger">
                                        *</span></label>
                                <input type="text" id="fname" name="fname" placeholder="Gasto" onblur="validate(1)"
                                    required>
                            </div>
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Estado de inversion<span class="text-danger">
                                        *</span></label>
                                <select class="form-select form-select-lg" aria-label="Large select example" name="inversion" required>
                                    <option selected>Seleccionar...</option>
                                    <option value="activo">Inversion Activa</option>
                                    <option value="pendiente">Inversion Pendiente</option>
                                    <option value="desactivado">Inversion Finalizada</option>
                                </select>

                            </div>
                        </div>
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Cantidad<span class="text-danger">
                                        *</span></label>
                                <input type="number" id="lname" name="cantidad" placeholder="1000.56" step="0.01"
                                    min="0" max="1000000" onblur="validate(2)" required>
                            </div>
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Fecha<span class="text-danger"> *</span></label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="fecha" name="date" value="<?php echo date('Y-m-d'); ?>" required />
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-between text-left">
                                <div class="form-group col-12 flex-column d-flex"> 
                                    <label class="form-control-label px-3">Observaciones<span class="text-danger"> *</span></label>
                                     <textarea  id="ans" name="ans" placeholder="Alguna observacion sobre el cliente" onblur="validate(6)"> </textarea>
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
<!-- Lista de inversiones -->
<div class="container-fluid mt-5">
    <h3 class="text-center">Lista de Inversiones</h3>
    <form method="GET" action="inversiones.php">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mx-4 my-4 align-middle" id="tablaClientes" style="min-width: 800px;">
                <caption>Lista de Inversiones</caption>
                <thead>
                    <tr class="text-center text-uppercase table-light">
                        <th></th>
                        <th scope="col">
                            <div class="input-group">
                                <input type="text" class="form-control" name="buscarInversion" placeholder="Buscar Inversión" id="busquedaGasto" value="<?php echo htmlspecialchars($buscarInversion); ?>">
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
                                        <option class="text-center text-uppercase" name="buscarFecha" value="">Todas las fechas</option>
                                        <?php else: ?>
                                        <option selected class="text-center text-uppercase" name="buscarFecha" value="">Fecha</option>
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
                        <th scope="col">
                            <div class="input-group">
                                <select class="form-select" name="buscarEstado" id="busquedaEstado">
                                    <?php if (!empty($buscarEstado)): ?>
                                        <option selected class="text-center text-uppercase" name="estado" value="<?php echo htmlspecialchars($buscarEstado); ?>"><?php echo $buscarEstado; ?></option>
                                        <option class="text-center text-uppercase" name="estado" value="">Todos los estados</option>
                                    <?php else: ?>
                                    <option selected>Seleccionar...</option>
                                    <?php endif; ?>
                                    <option value="activo">Activo</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="desactivado">Finalizada</option>
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
                        <th scope="col">NOMBRE DE LA INVERSIÓN</th>
                        <th scope="col">CANTIDAD</th>
                        <th scope="col">ESTADO</th>
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
                            <td><?php echo $row['cantidad']; ?></td>
                            <td><?php echo ucfirst($row['estado']); ?></td>
                            <td><?php echo formatear_fecha($row['fecha_registro']); ?></td>
                            <td>
                                <a href="modificar_inversion.php?id=<?php echo $row['id']; ?>&cantidad=<?php echo $row['cantidad']; ?>"><i class="fa fa-edit mr-2"></i></a>
                                <a href="inversiones.php?Borrarid=<?php echo $row['id']; ?>&cantidad=<?php echo $row['cantidad']; ?>"><i class="fa fa-trash text-danger"></i></a>
                            </td>
                        </tr>
                        <?php $total_gastos += $row['cantidad']; ?>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr class="text-center text-uppercase table-light">
                        <th>#</th>
                        <th scope="col">Total Invertido</th>
                        <th scope="col"><?php echo $total_gastos; ?> €</th>
                        <th scope="col"></th>
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
                'buscarInversion' => $buscarInversion,
                'buscarFecha' => $buscarFecha,
                'buscarEstado' => $buscarEstado
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