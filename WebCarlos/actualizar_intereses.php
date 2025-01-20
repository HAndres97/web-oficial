<?php
/**
 * Actualizamos los intereses de los prestamos activos y pendientes cada vez que se inicia sesion
 * 1- Recogemos todos los intereses de los prestamos activos y pendientes
 * 2- Comprobamos si la fecha de pago es igual o mayor a la fecha actual y la amortizacion
 * 3- Si la fecha de pago es mayor por 5 dias, se creara un registro de morosidad y se cambiara la fecha de pago a la siguiente fecha de pago
 * 4- Si por el contrario la amortizacion es 1 y la fecha de pago es igual o mayor a la fecha de pago actual, se cambiara la amortizacion a 0 y se cambiara la fecha de pago a la siguiente fecha de pago
 */
// Obtener la fecha actual
$fecha_actual = new DateTime();

// Obtener el mes y el año de la fecha actual
$mes_actual = $fecha_actual->format('m'); // Mes en formato numérico (01-12)
$ano_actual = $fecha_actual->format('Y'); // Año en formato numérico completo (e.g., 2023)

$consulta_bbdd = "SELECT 
    intereses.id AS interes_id,
    intereses.id_prestamo,
    intereses.cantidad_interes,
    intereses.amortizado,
    intereses.fecha_pago,
    prestamos.id AS prestamo_id,
    prestamos.estado AS prestamo_estado
FROM 
    intereses
JOIN 
    prestamos ON intereses.id_prestamo = prestamos.id
WHERE 
    prestamos.estado IN ('activo', 'pendiente');";
$consulta = mysqli_query($conn, $consulta_bbdd);
if (mysqli_num_rows($consulta) > 0) {
    while ($row = mysqli_fetch_array($consulta)) {
        $fecha_pago = new DateTime($row['fecha_pago']);
        if ($fecha_actual > $fecha_pago and $row['amortizado'] == 0) {
            $diferencia = $fecha_actual->diff($fecha_pago);
            $dias = $diferencia->days;
            if ($dias > 6) {
                // Creamos un registro de morosidad
                $sql_morosidad = "INSERT INTO registro_morosidad (id_interes, fecha_pago, cantidad, amortizado) VALUES ('" . $row['interes_id'] . "', '" . $row['fecha_pago'] . "', '" . $row['cantidad_interes'] . "', 0)";
                if (mysqli_query($conn, $sql_morosidad)) {
                    ;
                } else {
                    echo "Error: " . $sql_morosidad . "<br>" . mysqli_error($conn);
                }
                // Actualizar la fecha de pago al mes siguiente y al año actual, manteniendo el día de cobro
                $proxima_fecha_pago = new DateTime($row['fecha_pago']);
                $dia_pago = $proxima_fecha_pago->format('d'); // Obtener el día de cobro original
                $proxima_fecha_pago->modify('first day of next month');
                $ultimo_dia_mes = $proxima_fecha_pago->format('t'); // Obtener el último día del mes siguiente
                if ($dia_pago > $ultimo_dia_mes) {
                    $dia_pago = $ultimo_dia_mes; // Ajustar al último día del mes si el día de cobro original no existe en el mes siguiente
                }
                // Ajustar el año si el mes es enero
                if ($proxima_fecha_pago->format('m') == '01') {
                    $ano_actual++;
                }
                $proxima_fecha_pago->setDate($ano_actual, $proxima_fecha_pago->format('m'), $dia_pago);
                $nueva_fecha_pago = $proxima_fecha_pago->format('Y-m-d');
                $sql_fecha_pago = "UPDATE intereses SET fecha_pago = '$nueva_fecha_pago' WHERE id = '" . $row['interes_id'] . "'";
                if (mysqli_query($conn, $sql_fecha_pago)) {
                    ;
                } else {
                    echo "Error: " . $sql_fecha_pago . "<br>" . mysqli_error($conn);
                }
            }
        } elseif ($row['amortizado'] == 1 && $fecha_actual >= $fecha_pago) {
            // Cambiamos la amortizacion a 0
            $sql_amortizacion = "UPDATE intereses SET amortizado = 0 WHERE id = '" . $row['interes_id'] . "'";
            if (mysqli_query($conn, $sql_amortizacion)) {
                ;
            } else {
                echo "Error: " . $sql_amortizacion . "<br>" . mysqli_error($conn);
            }
            // Actualizar la fecha de pago al mes siguiente y al año actual, manteniendo el día de cobro
            $proxima_fecha_pago = new DateTime($row['fecha_pago']);
            $dia_pago = $proxima_fecha_pago->format('d'); // Obtener el día de cobro original
            $proxima_fecha_pago->modify('first day of next month');
            $ultimo_dia_mes = $proxima_fecha_pago->format('t'); // Obtener el último día del mes siguiente
            if ($dia_pago > $ultimo_dia_mes) {
                $dia_pago = $ultimo_dia_mes; // Ajustar al último día del mes si el día de cobro original no existe en el mes siguiente
            }
            // Ajustar el año si el mes es enero
            if ($proxima_fecha_pago->format('m') == '01') {
                $ano_actual++;
            }
            $proxima_fecha_pago->setDate($ano_actual, $proxima_fecha_pago->format('m'), $dia_pago);
            $nueva_fecha_pago = $proxima_fecha_pago->format('Y-m-d');
            $sql_fecha_pago = "UPDATE intereses SET fecha_pago = '$nueva_fecha_pago' WHERE id = '" . $row['interes_id'] . "'";
            if (mysqli_query($conn, $sql_fecha_pago)) {
                ;
            } else {
                echo "Error: " . $sql_fecha_pago . "<br>" . mysqli_error($conn);
            }
        }
    }
}
?>