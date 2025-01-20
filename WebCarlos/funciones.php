<?php
function verificar_usuario($validar_login, $password)
{
    $login = false;
    if (mysqli_num_rows($validar_login) > 0) {
        while ($row = mysqli_fetch_assoc($validar_login)) {
            if (password_verify($password, $row['password'])) {
                $login = true;
            }
        }
    }
    return $login;
}
function regresar_mes($mes)
{
    $meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
    return $meses[$mes - 1];
}
function controladorFecha($fecha)
{
    if ($fecha >= 1 && $fecha <= 9) {
        return "0" . $fecha;
    } else {
        return $fecha;
    }
}
function formatear_fecha($fecha)
{
    // Crear un objeto DateTime a partir de la fecha proporcionada
    $fecha_obj = new DateTime($fecha);
    
    // Formatear la fecha en el formato deseado (día, mes y año)
    $dia = $fecha_obj->format('d'); // Día en formato numérico (01-31)
    $mes = $fecha_obj->format('m'); // Mes en formato numérico (01-12)
    $ano = $fecha_obj->format('Y'); // Año en formato numérico completo (e.g., 2023)
    
    // Devolver la fecha formateada
    return "$dia-$mes-$ano";
}
function diferencia_dias($fecha_inicio, $fecha_final) {
    // Crear objetos DateTime para las fechas
    $inicio = new DateTime($fecha_inicio);
    $final = new DateTime($fecha_final);

    // Calcular la diferencia
    $diferencia = $inicio->diff($final);

    // Devolver la diferencia en días
    return $diferencia->days;
}
function crear_tabla($estado, $id_cliente, $id_prestamo, $cantidad_solicitada, $amortizacion, $tipo_interes, $fecha_inicio, $fecha_interes, $fecha_final)
{
    $color = "";
    $tipo = "";
    $formulario = "";
    $cantidad_interes = $cantidad_solicitada * ($tipo_interes / 100);
    $max_amortizacion = $cantidad_solicitada - $amortizacion;
    $diferencia_dias = diferencia_dias($fecha_inicio, $fecha_final);
    $alerta = "";
    if ($diferencia_dias < 30) {
        $alerta = "Próximo a finalizar";
    } 
    if ($estado == "activo") {
        $color = "success";
        $tipo = "Préstamo Activo";
        $formulario = "
                    <form method='POST' action='mostrar_cliente.php'>
                        <input type='hidden' name='id' value='$id_cliente'>
                        <input type='hidden' name='id_prestamo' value='" . htmlspecialchars($id_prestamo) . "'>
                        <input type='hidden' name='amortizacion_anterior' value='" . htmlspecialchars($amortizacion) . "'>
                
                        <div class='input-group mb-3'>
                            <input type='number' class='form-control'  id='amortizacion' name='amortizacion' placeholder='Máximo $max_amortizacion €'
                            step='0.5' min='0' max='$max_amortizacion' required>
                            <button class='btn btn-outline-success' type='submit' name='submit' id='button-addon2'>Amortizar</button>
                        </div>
                    </form>
            ";
    } else if ($estado == "pendiente") {
        $color = "warning";
        $tipo = "Préstamo Pendiente";
        $formulario = "
                    <form method='POST' action='mostrar_cliente.php'>
                        <input type='hidden' name='id' value='$id_cliente'>
                        <input type='hidden' name='id_prestamo' value='" . htmlspecialchars($id_prestamo) . "'>
                        <input type='hidden' name='amortizacion_anterior' value='" . htmlspecialchars($amortizacion) . "'>
                
                        <div class='input-group mb-3'>
                            <input type='number' class='form-control'  id='amortizacion' name='amortizacion' placeholder='Máximo $max_amortizacion €'
                            step='0.5' min='0' max='$max_amortizacion' required>
                            <button class='btn btn-outline-warning' type='submit' name='submit' id='button-addon2'>Amortizar</button>
                        </div>
                    </form>
            ";
    } else if ($estado == "desactivado") {
        $color = "primary";
        $tipo = "Préstamo Finalizado";
    }
    $tabla = "
            <div class='col-md-6'>
                <div class='card border border-$color mb-3 '>
                    <h5 class='card-header text-$color'>$tipo<span class='text-danger float-right'>$alerta</span></h5>
                        <div class='card-body'>
                            <p class='card-text'>Cantidad Solicitada: $cantidad_solicitada €</p>
                            <p class='card-text'>Amortización: $amortizacion €</p>
                            $formulario
                            <p class='card-text'>Interés: $tipo_interes% = $cantidad_interes €</p>
                            <p class='card-text'>Fecha Inicio: ".formatear_fecha($fecha_inicio)."</p>
                            <p class='card-text'>Fecha Final: ".formatear_fecha($fecha_final)."</p>
                            <p class='card-text'>Fecha Cobro de Interés: $fecha_interes de cada mes</p>
                            <a href='modificar_prestamo.php?id=$id_prestamo&id_cliente=$id_cliente' class='btn btn-$color'>Modificar</a>
                            
                        </div>
                </div>
            </div>";
    return $tabla;
}
function crear_tabla_interes($nombre,$apellidos,$movil,$cantidad_interes,$fecha_pago,$id,$amortizacion){
    $color = "";
    $fecha_actual = date('Y-m-d');
    $boton = "";
    if($amortizacion == 0 & $fecha_pago >= $fecha_actual){
        $color = "primary";
        $boton =  '<a href="index.php?id='.$id.'&payment='.$cantidad_interes.'&fecha_pago='.$fecha_pago.'" class="btn btn-primary w-100">Pagar</a>';
    }elseif($amortizacion == 0 & $fecha_pago < $fecha_actual){
        $color = "warning";
        $boton = '<a href="index.php?id='.$id.'&payment='.$cantidad_interes.'&fecha_pago='.$fecha_pago.'" class="btn btn-warning w-100">Pagar</a>';
    }elseif($amortizacion == 1){
        $color = "success";
    }
    $tabla_interes = '
         <div class="card border border-'.$color.'">
                <div class="card-body">
                    <h5 class="card-title text-'.$color.'">'.$nombre.' '.$apellidos.'</h5>
                    <p class="card-text">Móvil: '.$movil.'</p>
                    <p class="card-text">Interés: '.$cantidad_interes.' €</p>
                    <p class="card-text">Fecha de pago: '.formatear_fecha($fecha_pago).'</p>
                    '.$boton.'
                </div>
            </div>
    '; 
    return $tabla_interes;
}
function crear_tabla_interes_cliente($id_cliente,$nombre,$apellidos,$movil,$cantidad_interes,$fecha_pago,$id,$amortizacion){
    $color = "";
    $fecha_actual = date('Y-m-d');
    $boton = "";
    if($amortizacion == 0 & $fecha_pago >= $fecha_actual){
        $color = "primary";
        $boton =  '<a href="mostrar_cliente.php?id='.$id.'&payment='.$cantidad_interes.'&id_cliente='.$id_cliente.'&fecha_pago='.$fecha_pago.'" class="btn btn-primary w-100">Pagar</a>';
    }elseif($amortizacion == 0 & $fecha_pago < $fecha_actual){
        $color = "warning";
        $boton = '<a href="mostrar_cliente.php?id='.$id.'&payment='.$cantidad_interes.'&id_cliente='.$id_cliente.'&fecha_pago='.$fecha_pago.'" class="btn btn-warning w-100">Pagar</a>';
    }elseif($amortizacion == 1){
        $color = "success";
    }
    $tabla_interes = '
         <div class="card border border-'.$color.'">
                <div class="card-body">
                    <h5 class="card-title text-'.$color.'">'.$nombre.' '.$apellidos.'</h5>
                    <p class="card-text">Móvil: '.$movil.'</p>
                    <p class="card-text">Interés: '.$cantidad_interes.' €</p>
                    <p class="card-text">Fecha de pago: '.formatear_fecha($fecha_pago).'</p>
                    '.$boton.'
                </div>
            </div>
    '; 
    return $tabla_interes;
}
?>