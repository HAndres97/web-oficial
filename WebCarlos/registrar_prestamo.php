<?php
include 'template/sesion.php';

// Función para registrar un préstamo
function registrar_prestamo($conn, $data) {
    $id_cliente = $data['id_cliente'];
    $id_patrimonio = 1;
    $cantidad_solicitada = $data['cantidad_solicitada'];
    $amortizacion = $data['amortizacion'];
    $tipo_interes = $data['tipo_interes'];
    $fecha_inicio = $data['fecha_inicio'];
    $fecha_interes = $data['fecha_interes'];
    $fecha_final = $data['fecha_final'];
    $estado = $data['estado'];

    // Verificar que el cliente existe
    $validar_cliente = mysqli_query($conn, "SELECT * FROM clientes WHERE id = '$id_cliente'");
    if (mysqli_num_rows($validar_cliente) > 0) {
        $sql = "INSERT INTO prestamos (id_cliente, id_patrimonio, cantidad_solicitada, amortizacion, tipo_interes, fecha_inicio, fecha_interes, fecha_final, estado) VALUES ('$id_cliente', '$id_patrimonio', '$cantidad_solicitada', '$amortizacion', '$tipo_interes', '$fecha_inicio', '$fecha_interes', '$fecha_final', '$estado')";
        if (mysqli_query($conn, $sql)) {

            // Crear intereses
            $cantidad_interes = $cantidad_solicitada * ($tipo_interes / 100);
            $amortizado = false;

            // Calcular la fecha de pago a mes vencido
            $fecha_interes_parts = explode('-', $fecha_interes);
            $anio = (int)$fecha_interes_parts[0];
            $mes = (int)$fecha_interes_parts[1];
            $dia = (int)$fecha_interes_parts[2];

            // Ajustar el mes y el año
            if ($mes == 12) {
                $mes = 1;
                $anio++;
            } else {
                $mes++;
            }

            // Ajustar el día si es necesario
            $ultimo_dia_mes = date("t", strtotime("$anio-$mes-01"));
            if ($dia > $ultimo_dia_mes) {
                $dia = $ultimo_dia_mes;
            }

            $fecha_pago = "$anio-$mes-$dia";

            $sql = "INSERT INTO intereses (id_prestamo, cantidad_interes, amortizado, fecha_pago) VALUES (LAST_INSERT_ID(), '$cantidad_interes', '$amortizado', '$fecha_pago')";
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Error: Cliente no encontrado.";
    }

    // Actualizar patrimonio
    $patrimonio = mysqli_query($conn, "SELECT * FROM patrimonios WHERE id = 1");
    $row = mysqli_fetch_array($patrimonio);
    $patrimonio_cantidad = $row['cantidad'] - $cantidad_solicitada + $amortizacion;
    $sql = 'UPDATE patrimonios SET cantidad = ' . $patrimonio_cantidad . ' WHERE id = 1';
    if (!mysqli_query($conn, $sql)) {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    header('Location: mostrar_cliente.php?id=' . $id_cliente);
}

// Procesar el formulario de registro de préstamo
if (isset($_POST['submit'])) {
    registrar_prestamo($conn, $_POST);
}

// Obtener lista de clientes
$consulta_clientes = mysqli_query($conn, "SELECT * FROM clientes");
$clientes = mysqli_fetch_array($consulta_clientes);

include 'template/cabecera.php';
?>
<div class="body">
    <div class="container-xxl px-1 py-5 mx-auto">
        <div class="row d-flex justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
                <h3>Formulario de Registro de Prestamo</h3>
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-9 col-11">
                <form method="POST" action="registrar_prestamo.php" onsubmit="return validarFechas()">
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-sm-6 col-md-5 flex-column d-flex">
                            <label class="form-control-label px-3">ID Cliente<span class="text-danger"> *</span></label>
                            <select class="form-select form-select-lg text-uppercase select2" aria-label="Large select example" name="id_cliente" required>
                                <?php
                                    while($clientes = mysqli_fetch_array($consulta_clientes)){
                                        echo '<option value="'.$clientes['id'].'">'.$clientes['id'].' - '.$clientes['nombre'].' '.$clientes['apellidos'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Cantidad Solicitada<span class="text-danger"> *</span></label>
                            <input type="number" id="cantidad_solicitada" name="cantidad_solicitada" placeholder="1000.56" step="100" min="0" max="1000000" onblur="validate(7)" required>
                        </div>
                    </div>
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Amortización<span class="text-danger"> *</span></label>
                            <input type="number" value="0" id="amortizacion" name="amortizacion" placeholder="1000.56" step="0.5" min="0" max="1000000" onblur="validate(8)" required>
                        </div>
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Tipo de Interes<span class="text-danger"> *</span></label>
                            <input type="number" id="tipo_interes" name="tipo_interes" placeholder="[0-100]" value="10" onblur="validate(9)" min="0" max="100" required>
                        </div>
                    </div>
                    <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Fecha Inicio<span class="text-danger"> *</span></label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" value="<?php echo date('Y-m-d'); ?>" required />
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                </div>
                            </div>
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Día de Cobro del interes<span class="text-danger"> *</span></label>
                            <input type="number" id="fecha_interes" min="1" max="31" name="fecha_interes" placeholder="[1-31]" onblur="validate(12)"required>
                        </div>
                    </div>
                    <div class="row justify-content-between text-left">
                    <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Fecha Final<span class="text-danger"> *</span></label>
                                <div class="input-group">
                                    <input type="date" class="form-control" id="fecha_final" name="fecha_final" value="<?php echo date('Y-m-d'); ?>" required />
                                    <span class="input-group-text"><i class="bi bi-calendar"></i></span>
                                </div>
                            </div>
                        <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Estado<span class="text-danger">
                                        *</span></label>
                                <select class="form-select form-select-lg" aria-label="Large select example" name="estado" required>
                                    <option selected>Seleccionar...</option>
                                    <option value="activo">Prestamo Activa</option>
                                    <option value="pendiente">Prestamo Pendiente</option>
                                    <option value="desactivado">Prestamo Finalizada</option>
                                </select>
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
<?php include 'template/footer.php'; ?>