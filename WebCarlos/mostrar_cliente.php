<?php
include 'template/sesion.php';
?>
<?php
// Actualizacion de interes
if (isset($_GET['id']) & isset($_GET['payment'])) {
    $id = $_GET['id'];
    $payment = $_GET['payment'];
    $id_cliente = $_GET['id_cliente'];
    $fecha_pago = $_GET['fecha_pago'];
    $sql = "UPDATE intereses SET amortizado = 1 WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        //Actualizar patrimonio
        $patrimonio = mysqli_query($conn, "SELECT * FROM patrimonios WHERE id = 1");
        $row = mysqli_fetch_array($patrimonio);
        $patrimonio_cantidad = $row['cantidad'] + $payment;
        $sql = 'UPDATE patrimonios SET cantidad = ' . $patrimonio_cantidad . ' WHERE id = 1';
        if (mysqli_query($conn, $sql)) {
            echo "<div class='alert alert-success text-center' role='alert'>
                            Patrimonio Actualizado Correctamente
                        </div>";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        //Registro de Interes
        $sql = "INSERT INTO registros_intereses (id_interes,cantidad, fecha_amortizacion, fecha_pago) VALUES ($id,$payment, CURDATE(),'$fecha_pago')";
        if (mysqli_query($conn, $sql)) {
            echo "<div class='alert alert-success text-center' role='alert'>
                            Registro Creado Correctamente
                        </div>";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        header('Location: mostrar_cliente.php?id=' . $id_cliente);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
    $cliente = mysqli_query($conn, "SELECT * FROM clientes WHERE id = '$id'");
    $cliente_row = mysqli_fetch_array($cliente);
    $consultas_prestamos_cliente = mysqli_query($conn, "SELECT * FROM prestamos WHERE id_cliente = '$id' order by estado");

    // Consulta de Registro de morosidad
    $sql_morosidad = "
SELECT 
    clientes.id AS cliente_id,
    clientes.nombre AS cliente_nombre,
    clientes.apellidos AS cliente_apellidos,
    clientes.movil AS cliente_movil,
    clientes.email AS cliente_email,
    registro_morosidad.id AS morosidad_id,
    registro_morosidad.id_interes AS interes_id,
    registro_morosidad.fecha_pago,
    registro_morosidad.registro_pago,
    registro_morosidad.cantidad,
    registro_morosidad.amortizado
FROM 
    clientes
JOIN 
    prestamos ON clientes.id = prestamos.id_cliente
JOIN 
    intereses ON prestamos.id = intereses.id_prestamo
JOIN 
    registro_morosidad ON intereses.id = registro_morosidad.id_interes
WHERE 
    registro_morosidad.amortizado = 0
    AND 
    clientes.id = '$id'
ORDER BY
    intereses.fecha_pago DESC
";
    $consulta_morosidad = mysqli_query($conn, $sql_morosidad);

    //Consulta de Registro de intereses
    $sql_intereses = "
SELECT 
    clientes.id AS cliente_id,
    prestamos.id AS prestamo_id,
    intereses.id AS interes_id,
    registros_intereses.id AS registro_interes_id,
    registros_intereses.fecha_amortizacion,
    registros_intereses.cantidad,
    registros_intereses.fecha_pago
FROM 
    clientes
JOIN 
    prestamos ON clientes.id = prestamos.id_cliente
JOIN 
    intereses ON prestamos.id = intereses.id_prestamo
JOIN 
    registros_intereses ON intereses.id = registros_intereses.id_interes
WHERE 
    clientes.id = '$id'
ORDER BY
    registros_intereses.fecha_amortizacion DESC";
    $consulta_registro_intereses = mysqli_query($conn, $sql_intereses);
    // Variables para la paginación
    $num_intereses = mysqli_num_rows($consulta_registro_intereses); // Contamos el número de intereses
    $intereses_por_pagina = 10; // Número de intereses por página
    $paginas = ceil($num_intereses / $intereses_por_pagina); // Calculamos el número de páginas

    // Determinar la página actual
    $nume = isset($_REQUEST["nume"]) ? $_REQUEST["nume"] : '1';
    if ($nume == "" || !is_numeric($nume) || $nume < 1) {
        $nume = 1;
    }
    $pagina = (int) $nume;
    $inicio = ($pagina - 1) * $intereses_por_pagina;
    // Realizar la consulta con límite y desplazamiento
    $sql_paginado = "
SELECT 
    clientes.id AS cliente_id,
    prestamos.id AS prestamo_id,
    intereses.id AS interes_id,
    registros_intereses.id AS registro_interes_id,
    registros_intereses.fecha_amortizacion,
    registros_intereses.cantidad,
    registros_intereses.fecha_pago
FROM 
    clientes
JOIN 
    prestamos ON clientes.id = prestamos.id_cliente
JOIN 
    intereses ON prestamos.id = intereses.id_prestamo
JOIN 
    registros_intereses ON intereses.id = registros_intereses.id_interes
WHERE 
    clientes.id = '$id'
ORDER BY
    registros_intereses.fecha_amortizacion DESC
LIMIT $inicio, $intereses_por_pagina";
    $consulta_registro_intereses_paginados = mysqli_query($conn, $sql_paginado);
    // Consulta de los interes pendientes
    $sql_interes_pendiente = "
    SELECT 
                clientes.nombre,
                clientes.apellidos,
                clientes.movil AS telefono,
                intereses.cantidad_interes,
                intereses.fecha_pago,
                intereses.id AS interes_id,
                intereses.amortizado
                FROM 
                intereses
                JOIN 
                prestamos ON intereses.id_prestamo = prestamos.id
                JOIN 
                clientes ON prestamos.id_cliente = clientes.id
                WHERE 
                prestamos.estado IN ('activo', 'pendiente')
                AND
                clientes.id = '$id'
                ORDER BY
                intereses.fecha_pago ASC
                LIMIT 2
                ";
}
//Actualizar Registro de morosidad
elseif (isset($_GET['id_morosidad'])) {
    $id = $_GET['id_morosidad'];
    $cantidad = $_GET['cantidad'];
    $id_interes = $_GET['id_interes'];
    $id_cliente = $_GET['id_cliente'];
    $sql = "UPDATE registro_morosidad SET amortizado = 1 WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        //Actualizar patrimonio
        $patrimonio = mysqli_query($conn, "SELECT * FROM patrimonios WHERE id = 1");
        $row = mysqli_fetch_array($patrimonio);
        $patrimonio_cantidad = $row['cantidad'] + $cantidad;
        $sql = 'UPDATE patrimonios SET cantidad = ' . $patrimonio_cantidad . ' WHERE id = 1';
        if (mysqli_query($conn, $sql)) {
            ;
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        //Registro de Interes
        $sql = "INSERT INTO registros_intereses (id_interes, fecha_amortizacion) VALUES ($id_interes, CURDATE())";
        if (mysqli_query($conn, $sql)) {
            ;
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        header('Location: mostrar_cliente.php?id=' . $id_cliente);
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        exit();
    }
}// Actualizar patrimonio y amortizacion
elseif (isset($_POST['submit'])) {
    echo "Entra al submit";
    $id = $_POST['id'];
    $id_prestamo = $_POST['id_prestamo'];
    $amortizacion = $_POST['amortizacion'];
    $amortizacion_anterior = $_POST['amortizacion_anterior'];
    $amortizacion_total = $amortizacion_anterior + $amortizacion;
    $sql = "UPDATE prestamos SET amortizacion = '$amortizacion_total' WHERE id = '$id_prestamo'";
    if (mysqli_query($conn, $sql)) {
        $patrimonio = mysqli_query($conn, "SELECT * FROM patrimonios WHERE id = 1");
        $patrimonio_row = mysqli_fetch_array($patrimonio);
        $patrimonio_cantidad = $patrimonio_row['cantidad'] + $amortizacion_total;
        $sql = 'UPDATE patrimonios SET cantidad = ' . $patrimonio_cantidad . ' WHERE id = 1';
        if (mysqli_query($conn, $sql)) {
            header('Location: mostrar_cliente.php?id=' . $id);
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
} else {
    echo '<div class="alert alert-danger" role="alert">ID de cliente no especificado.</div>';
    exit();
}
?>
<?php include 'template/cabecera.php'; ?>
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card ">
                <div class="card-header">
                    <h3>Cliente: <?php echo $cliente_row['nombre'] . " " . $cliente_row['apellidos']; ?></h3>
                </div>
                <div class="card-body">
                    <p class="card-text fs-5 ">DNI: <?php echo $cliente_row['id']; ?>.</p>
                    <p class="card-text fs-5  ">Móvil: <?php echo $cliente_row['movil']; ?>.</p>
                    <p class="card-text fs-5 ">Email: <?php echo $cliente_row['email']; ?>.</p>
                    <p class="card-text fs-5 ">Dirección: <?php echo $cliente_row['comentario']; ?>.</p>
                    <a href="modificar_cliente.php?id=<?php echo $cliente_row['id']; ?>"
                        class="btn btn-primary">Modificar</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-12">
                    <?php
                    $consulta_interes_pendiente = mysqli_query($conn, $sql_interes_pendiente);
                    if (mysqli_num_rows($consulta_interes_pendiente) > 0) {
                        while ($row = mysqli_fetch_assoc($consulta_interes_pendiente)) {
                            echo crear_tabla_interes_cliente(
                                $id,
                                $row['nombre'],
                                $row['apellidos'],
                                $row['telefono'],
                                $row['cantidad_interes'],
                                $row['fecha_pago'],
                                $row['interes_id'],
                                $row['amortizado']
                            );
                        }
                    } else {
                        echo "<div class='alert alert-primary text-center mt-5' role='alert'>
                                        No hay intereses pendientes de Cobro
                                </div>";
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Registro de morosidad -->
<div class="col-md-6 mx-auto">
    <h3>Registro de morosidad</h3>
    <div class="row">
        <?php
        if (mysqli_num_rows($consulta_morosidad) > 0) {
            while ($row = mysqli_fetch_assoc($consulta_morosidad)) {
                echo '<div class="col-md-6 mb-3">';
                echo '<div class="card border border-danger">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title text-danger">' . htmlspecialchars($row['cliente_nombre']) . ' ' . htmlspecialchars($row['cliente_apellidos']) . '</h5>';
                echo '<p class="card-text">Teléfono: ' . htmlspecialchars($row['cliente_movil']) . '</p>';
                echo '<p class="card-text">Fecha de Pago: ' . htmlspecialchars($row['fecha_pago']) . '</p>';
                $fecha_actual = new DateTime();
                $fecha_pago = new DateTime($row['fecha_pago']);
                $diferencia = $fecha_actual->diff($fecha_pago);
                $dias = $diferencia->days;

                echo '<p class="card-text">Cantidad: ' . htmlspecialchars($row['cantidad']) . ' €</p>';
                echo '<p class="card-text">Días de retraso: ' . $dias . 'dias </p>';
                echo '<a href="mostrar_cliente.php?id_morosidad=' . $row['morosidad_id'] . '&cantidad=' . $row['cantidad'] . '&id_interes=' . $row['interes_id'] . '&id_cliente=' . $row['cliente_id'] . '" " class="btn btn-danger w-100">Pagar</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "<div class='alert alert-success text-center' role='alert'>
                            No hay registro de morosidad
                          </div>";
        }
        ?>
    </div>
</div>
<!-- Registro de Prestamos -->
<div>
    <h2 class="text-center">Préstamos</h2>
    <?php
    if (mysqli_num_rows($consultas_prestamos_cliente) != 0) {
        echo '<div class="container-xxl px-1 py-5 mx-auto">
                <div class="row">';
        while ($prestamo_row = mysqli_fetch_array($consultas_prestamos_cliente)) {

            echo crear_tabla($prestamo_row['estado'], $prestamo_row['id_cliente'], $prestamo_row['id'], $prestamo_row['cantidad_solicitada'], $prestamo_row['amortizacion'], $prestamo_row['tipo_interes'], $prestamo_row['fecha_inicio'], $prestamo_row['fecha_interes'], $prestamo_row['fecha_final']);
        }
        echo '  </div>
                </div>';
    } else {
        echo '<div class="alert alert-danger" role="alert">No hay prestamos registrados para este cliente.</div>';
    }
    ?>
</div>
<!-- Registro de Intereses -->
<div class="mx-auto" style="width: 50%">
    <h3>Tabla de registro de intereses</h3>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">Fecha de Cobro</th>
                <th scope="col">Fecha de Pago</th>
                <th scope="col">Cantidad de Interés</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_interes = 0;
            if (mysqli_num_rows($consulta_registro_intereses_paginados) > 0) {
                while ($row = mysqli_fetch_assoc($consulta_registro_intereses_paginados)) {
                    echo '<tr>';
                    echo '<td>' . formatear_fecha($row['fecha_pago']) . '</td>';
                    echo '<td>' . formatear_fecha($row['fecha_amortizacion']) . '</td>';
                    echo '<td>' . $row['cantidad'] . ' €</td>';
                    $total_interes += $row['cantidad'];
                    echo '</tr>';
                }
            } else {
                echo "<div class='alert alert-success text-center' role='alert'>
                            No hay registro de intereses
                          </div>";
            }
            ?>
        </tbody>
        <tfoot>
            <tr class="text-uppercase table-light">
                <th scope="col">Total Interés</th>
                <th scope="col"></th>
                <th scope="col"><?php echo $total_interes; ?> €</th>
            </tr>
        </tfoot>
    </table>

    <!-- Paginación -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php
            // Mantener los parámetros de búsqueda en los enlaces de paginación
            $query_string = http_build_query([
                'id' => $id
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