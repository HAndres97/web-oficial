<?php
include 'template/sesion.php';
include 'template/cabecera.php';

// Obtener lista de meses y años para el filtro
$lista_meses_anos = mysqli_query($conn, "SELECT DISTINCT MONTH(fecha_inicio) AS MES, YEAR(fecha_inicio) AS ANO FROM prestamos ORDER BY ANO DESC;");
$mensaje_error = "No hay préstamos registrados";

// Búsqueda de préstamos
$where = " WHERE 1=1 ";
$buscarCliente = "";
$buscarFecha = "";
$buscarEstado = "";
$total_gastos = 0;
$order_by = " ORDER BY fecha_final asc";

// Buscamos por nombre del cliente
if (!empty($_GET['buscarCliente'])) {
    $buscarCliente = $_GET['buscarCliente'];
    if ($buscarCliente != 'Seleccionar..') {
        $where .= " AND prestamos.id_cliente = '$buscarCliente'";
    }
}

// Buscamos por fecha
if (!empty($_GET['buscarFecha'])) {
    $buscarFecha = $_GET['buscarFecha'];
    $where .= ' AND prestamos.fecha_inicio LIKE "' . $buscarFecha . '%"';
}

// Buscamos por estado
if (!empty($_GET['buscarEstado'])) {
    $buscarEstado = $_GET['buscarEstado'];
    if ($buscarEstado != 'Seleccionar...') {
        $where .= " AND prestamos.estado = '$buscarEstado'";
    }
}

$sql = "SELECT clientes.id, clientes.nombre, clientes.apellidos, prestamos.* FROM prestamos JOIN clientes ON prestamos.id_cliente = clientes.id $where ";
$consulta_clientes = mysqli_query($conn, "SELECT * FROM clientes");
$clientes = mysqli_fetch_array($consulta_clientes);
$lista_prestamos = mysqli_query($conn, $sql);

// Variables para la paginación
$num_prestamos = mysqli_num_rows($lista_prestamos); // Contamos el número de préstamos
$prestamos_por_pagina = 10; // Número de préstamos por página
$paginas = ceil($num_prestamos / $prestamos_por_pagina); // Calculamos el número de páginas

// Determinar la página actual
$nume = isset($_REQUEST["nume"]) ? $_REQUEST["nume"] : '1';
if ($nume == "" || !is_numeric($nume) || $nume < 1) {
    $nume = 1;
}
$pagina = (int)$nume;
$inicio = ($pagina - 1) * $prestamos_por_pagina;

// Realizar la consulta con límite y desplazamiento
$sql_paginado = "SELECT clientes.id, clientes.nombre, clientes.apellidos, prestamos.* FROM prestamos JOIN clientes ON prestamos.id_cliente = clientes.id $where $order_by LIMIT $inicio, $prestamos_por_pagina";
$lista_prestamos_paginados = mysqli_query($conn, $sql_paginado);
?>

<div class="d-grid gap-2 col-6 mt-3 mb-3 mx-auto">
    <a class="btn btn-primary" href="registrar_prestamo.php" role="button">Registrar Préstamo</a>
</div>

<!-- Lista de préstamos -->
<div class="container mt-5">
    <h3 class="text-center">Lista de Préstamos</h3>
    <form method="GET" action="prestamos.php">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mx-4 my-4 align-middle" id="tablaClientes" style="min-width: 800px;">
                <caption>Lista de Préstamos</caption>
                <thead>
                    <tr class="text-center text-uppercase table-light">
                        <th></th>
                        <th scope="col">
                            <div class="input-group">
                                <div class="form-group flex-column d-flex w-75">
                                    <select class="form-select select2" aria-label="Large select example" name="buscarCliente" required>
                                        <option selected>Seleccionar..</option>
                                        <?php while ($cliente = mysqli_fetch_array($consulta_clientes)): ?>
                                            <option value="<?php echo $cliente['id']; ?>"><?php echo $cliente['nombre'] . ' ' . $cliente['apellidos']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
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
                        <th scope="col">
                            <div class="input-group">
                                <select class="form-select" name="buscarEstado" id="busquedaEstado">
                                    <?php if (!empty($buscarEstado)): ?>
                                        <option selected class="text-center text-uppercase" name="buscarEstado" value="<?php echo htmlspecialchars($buscarEstado); ?>"><?php echo htmlspecialchars($buscarEstado); ?></option>
                                    <?php else: ?>
                                    <option selected>Seleccionar...</option>
                                    <?php endif; ?>
                                    <option value="activo">Activo</option>
                                    <option value="pendiente">Pendiente</option>
                                    <option value="desactivado">Finalizado</option>
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
                        <th scope="col">NOMBRE DEL CLIENTE</th>
                        <th scope="col">CANTIDAD</th>
                        <th scope="col">ESTADO</th>
                        <th scope="col">FECHA INICIO</th>
                        <th scope="col">FECHA FINAL DEL CONTRATO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $contador = $inicio + 1;
                    while ($row = mysqli_fetch_array($lista_prestamos_paginados)): ?>
                        <tr class="text-center text-uppercase">
                            <td><?php echo $contador++; ?></td>
                            <td><a href="mostrar_cliente.php?id=<?php echo $row['id_cliente']; ?>"><?php echo $row['nombre'] . ' ' . $row['apellidos']; ?></a></td>
                            <td><?php echo $row['cantidad_solicitada']; ?></td>
                            <td><?php echo ucfirst($row['estado']); ?></td>
                            <td><?php echo formatear_fecha($row['fecha_inicio']); ?></td>
                            <td><?php echo formatear_fecha($row['fecha_final']); ?></td>
                        </tr>
                        <?php $total_gastos += $row['cantidad_solicitada']; ?>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr class="text-center text-uppercase table-light">
                        <th>#</th>
                        <th scope="col">Total Prestado</th>
                        <th scope="col"><?php echo $total_gastos; ?> €</th>
                        <th scope="col">ESTADO</th>
                        <th scope="col">FECHA INICIO</th>
                        <th scope="col">FECHA FINAL DEL CONTRATO</th>
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
                'buscarCliente' => $buscarCliente,
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