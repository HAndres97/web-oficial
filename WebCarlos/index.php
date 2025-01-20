<?php
include 'template/sesion.php';
?>
<?php
$consulta_patrimonio = mysqli_query($conn, "SELECT * FROM patrimonios");
if (mysqli_num_rows($consulta_patrimonio) == 0) {
        header("Location: patrimonio.php");
}
$mes_actual = date("m");
$mes_actual = regresar_mes($mes_actual);
// Consulta para obtener la cantidad total de gastos del mes actual
$sql_gastos = "
SELECT 
    SUM(cantidad) AS total_gastos
FROM 
    gastos
WHERE 
    MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
$result_gastos = mysqli_query($conn, $sql_gastos);
$row_gastos = mysqli_fetch_assoc($result_gastos);
$total_gastos = $row_gastos['total_gastos'];

// Consulta para obtener la cantidad total de inversiones del mes actual
$sql_inversiones = "
SELECT 
    SUM(cantidad) AS total_inversiones
FROM 
    inversiones
WHERE 
    MONTH(fecha_registro) = MONTH(CURDATE()) AND YEAR(fecha_registro) = YEAR(CURDATE())";
$result_inversiones = mysqli_query($conn, $sql_inversiones);
$row_inversiones = mysqli_fetch_assoc($result_inversiones);
$total_inversiones = $row_inversiones['total_inversiones'];
// Consulta para obtener la cantidad total del patrimonio
$sql_patrimonio = "
SELECT 
    SUM(cantidad) AS total_patrimonio
FROM 
    patrimonios";
$result_patrimonio = mysqli_query($conn, $sql_patrimonio);
$row_patrimonio = mysqli_fetch_assoc($result_patrimonio);
$total_patrimonio = number_format($row_patrimonio['total_patrimonio'], 2);
// Actualizacion de interes
if (isset($_GET['id']) & isset($_GET['payment']) ) {
        $id = $_GET['id'];
        $payment = $_GET['payment'];
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
        } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }

}
// Actualizacion de morosidad
if (isset($_GET['id_morosidad'])) {
        $id = $_GET['id_morosidad'];
        $cantidad = $_GET['cantidad'];
        $id_interes = $_GET['id_interes'];
        $sql = "UPDATE registro_morosidad SET amortizado = 1 WHERE id = $id";
        if (mysqli_query($conn, $sql)) {
                //Actualizar patrimonio
                $patrimonio = mysqli_query($conn, "SELECT * FROM patrimonios WHERE id = 1");
                $row = mysqli_fetch_array($patrimonio);
                $patrimonio_cantidad = $row['cantidad'] + $cantidad;
                $sql = 'UPDATE patrimonios SET cantidad = ' . $patrimonio_cantidad . ' WHERE id = 1';
                if (mysqli_query($conn, $sql)) {
                        echo "<div class='alert alert-success text-center' role='alert'>
                            Patrimonio Actualizado Correctamente
                        </div>";
                } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
                //Registro de Interes
                $sql = "INSERT INTO registros_intereses (id_interes, fecha_amortizacion) VALUES ($id_interes, CURDATE())";
                if (mysqli_query($conn, $sql)) {
                        echo "<div class='alert alert-success text-center' role='alert'>
                            Registro Creado Correctamente
                        </div>";
                } else {
                        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
                echo "<div class='alert alert-success text-center' role='alert'>
                            Morosidad Actualizada Correctamente
                        </div>";
        } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
}
// Consulta para mostrar intereses de los prestamos activos y pendientes
$sql = "
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
                prestamos.estado IN ('activo', 'pendiente')";
// Consulta para registro de morosidad
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
ORDER BY
    intereses.fecha_pago DESC
LIMIT 10
";
$consulta_morosidad = mysqli_query($conn, $sql_morosidad);
?>
<?php include 'template/cabecera.php'; ?>
<div class="container mt-5">
        <div class="row">
                <!-- Div redondo con cantidad en el medio -->
                <div class="col-md-6 d-flex justify-content-center align-items-center mb-4 mb-md-0">
                        <div class="rounded-circle bg-primary text-white d-flex justify-content-center align-items-center"
                                style="width: 200px; height: 200px;">
                                <h2><?php echo $total_patrimonio; ?> €</h2>
                        </div>
                </div>
                <!-- Div dividido en dos filas -->
                <div class="col-md-6">
                        <div class="row">
                                <div class="card">
                                        <div class="card-body p-3">
                                                <h5 class="card-title text-uppercase mb-2">Gastos de
                                                        <?php echo $mes_actual ?> </h5>
                                                <p class="card-text mb-0">Total Gastos: <?php echo $total_gastos; ?> €
                                                </p>
                                        </div>
                                </div>
                                <div class="card">
                                        <div class="card-body p-3">
                                                <h5 class="card-title text-uppercase mb-2">Inversiones de
                                                        <?php echo $mes_actual ?> </h5>
                                                <p class="card-text mb-0">Total Inversiones:
                                                        <?php echo $total_inversiones; ?> €</p>
                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</div>
<div class="container-xxl px-1 py-5 mx-auto">
        <div class="row">
                <!-- Lista de intereses Pendientes -->
                <div class="col-md-4">
                        <h3>Intereses en tiempo</h3>
                        <?php
                        $where = " AND intereses.amortizado = 0 AND intereses.fecha_pago >= CURDATE() ORDER BY intereses.fecha_pago ASC LIMIT 4";
                        $sql_activo = $sql . $where;
                        $consulta_activo = mysqli_query($conn, $sql_activo);
                        if (mysqli_num_rows($consulta_activo) > 0) {

                                while ($row = mysqli_fetch_assoc($consulta_activo)) {

                                        echo crear_tabla_interes(
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
                No hay intereses pendientes
            </div>";
                        }
                        ?>
                </div>
                <!-- Lista de intereses en demora -->
                <div class="col-md-4">
                        <h3>Intereses fuera de tiempo</h3>
                        <?php
                        $where = " AND intereses.amortizado = 0 AND intereses.fecha_pago < CURDATE() ORDER BY intereses.fecha_pago ASC LIMIT 4";
                        $sql_tiempo = $sql . $where;
                        $consulta_tiempo = mysqli_query($conn, $sql_tiempo);
                        if (mysqli_num_rows($consulta_tiempo) > 0) {

                                while ($row = mysqli_fetch_assoc($consulta_tiempo)) {

                                        echo crear_tabla_interes(
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
                                echo "<div class='alert alert-warning text-center mt-5' role='alert'>
              No hay intereses fuera de tiempo
          </div>";
                        }
                        ?>
                        <!-- Añade más tarjetas según sea necesario -->
                </div>
                <!-- Lista de Pagados Pendientes -->
                <div class="col-md-4">
                        <h3>Intereses pagados</h3>
                        <?php
                        $where = " AND intereses.amortizado = 1 ORDER BY intereses.fecha_pago DESC LIMIT 4";
                        $sql_pagado = $sql . $where;
                        $consulta_pagado = mysqli_query($conn, $sql_pagado);
                        if (mysqli_num_rows($consulta_pagado) > 0) {

                                while ($row = mysqli_fetch_assoc($consulta_pagado)) {

                                        echo crear_tabla_interes(
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
                                echo "<div class='alert alert-success text-center mt-5' role='alert'>
                                        No hay intereses pagados
                                </div>";
                        }
                        ?>
                        <!-- Añade más tarjetas según sea necesario -->
                </div>
                <!-- Registro de morosidad -->
                <div class="container mt-5">
                        <h3>Registro de morosidad</h3>
                        <div class="row">
                                <?php
                                if (mysqli_num_rows($consulta_morosidad) > 0) {
                                        while ($row = mysqli_fetch_assoc($consulta_morosidad)) {
                                                echo '<div class="col-md-6 my-n3  ">';
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
                                                echo '<p class="card-text">Dias de retrado: ' . $dias . ' dias </p>';
                                                echo '<a href="index.php?id_morosidad=' . $row['morosidad_id'] . '&cantidad=' . $row['cantidad'] . '&id_interes=' . $row['interes_id'] . '" " class="btn btn-danger w-100">Pagar</a>';
                                                echo '</div>';
                                                echo '</div>';
                                                echo '</div>';
                                        }
                                } else {
                                        echo "<div class='alert alert-danger text-center' role='alert'>
                            No hay registro de morosidad
                          </div>";
                                }
                                ?>
                        </div>
                </div>
        </div>
</div>
<?php include 'template/footer.php'; ?>