<?php
    include 'template/sesion.php';
?>
<!-- Registro de prestamo -->
<?php
    if(isset($_GET['id']) and isset($_GET['id_cliente'])){
        $id_cliente =$_GET['id_cliente'];
        $id_prestamo = $_GET['id'];
        $consulta_prestamo = mysqli_query($conn,'SELECT * FROM prestamos WHERE id = '.$id_prestamo);
        $prestamo_row = mysqli_fetch_array($consulta_prestamo);
        $consulta_clientes = mysqli_query($conn,"SELECT * FROM clientes");
       
    }

    if(isset($_POST['submit'])){
        $id=$_GET['id'];
        $cantidad_anterior_solicitada = $_GET['cantidad'];
        $id_cliente = $_POST['id_cliente'];
        $id_patrimonio = 1 ;
        $cantidad_solicitada = $_POST['cantidad_solicitada'];
        $amortizacion = $_POST['amortizacion'];
        $tipo_interes = $_POST['tipo_interes'];
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_interes = $_POST['fecha_interes'];
        $fecha_final = $_POST['fecha_final'];
        $estado = $_POST['estado'];
        // Actualizar datos de el prestamo
        $sql = "UPDATE prestamos 
                SET 
                    id_cliente = '$id_cliente', 
                    id_patrimonio = '$id_patrimonio', 
                    cantidad_solicitada = '$cantidad_solicitada', 
                    amortizacion = '$amortizacion', 
                    tipo_interes = '$tipo_interes', 
                    fecha_inicio = '$fecha_inicio', 
                    fecha_interes = '$fecha_interes', 
                    fecha_final = '$fecha_final', 
                    estado = '$estado' 
                WHERE 
                    id = '$id'";

        if (mysqli_query($conn, $sql)) {
            //Actualizar interes relacionado con el prestamo
            $sql_intereses = "SELECT * FROM intereses WHERE id_prestamo = '$id'";
            $result_intereses = mysqli_query($conn, $sql_intereses);
    
            if (mysqli_num_rows($result_intereses) > 0) {
                while ($row_interes = mysqli_fetch_assoc($result_intereses)) {
                    $id_interes = $row_interes['id'];
                    $cantidad_interes = ($cantidad_solicitada * $tipo_interes) / 100; // Ejemplo de cálculo de interés
                    $fecha_pago = date("Y")."-".date("m")."-".$fecha_interes;
                    $sql_update_interes = "UPDATE intereses 
                                           SET 
                                               cantidad_interes = '$cantidad_interes', 
                                               fecha_pago = '$fecha_pago' 
                                           WHERE 
                                               id = '$id_interes'";
                    mysqli_query($conn, $sql_update_interes);
                }
            }
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        
        // Actualizar patrimonio
        $patrimonio = mysqli_query($conn, "SELECT * FROM patrimonios WHERE id = 1");
        $row = mysqli_fetch_array($patrimonio);
        $patrimonio_cantidad = $row['cantidad'] + $cantidad_anterior_solicitada - $cantidad_solicitada + $amortizacion;
        $sql = 'UPDATE patrimonios SET cantidad = '.$patrimonio_cantidad.' WHERE id = 1';
        if (mysqli_query($conn, $sql)) {
            header('Location: mostrar_cliente.php?id='.$id_cliente);
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
?>
<?php include 'template/cabecera.php'; ?>
<!-- Formulario de registro de prestamo -->
<div class="body">
    <div class="container-xxl px-1 py-5 mx-auto">
        <div class="row d-flex justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
                <h3>Modificar Registro de Prestamo</h3>
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-9 col-11">
                <form method="POST" action="modificar_prestamo.php?id=<?php echo $id_prestamo; ?>&cantidad=<?php echo $prestamo_row['cantidad_solicitada']; ?>" class="form-card">
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-sm-6 col-md-5 flex-column d-flex">
                            <label class="form-control-label px-3">ID Cliente<span class="text-danger"> *</span></label>
                            <select class="form-select form-select-lg text-uppercase select2" aria-label="Large select example" name="id_cliente" required>
                                
                                <?php
                                echo"<option value='$id_cliente'>$id_cliente</option>";
                                    while($clientes = mysqli_fetch_array($consulta_clientes)){
                                        echo '<option value="'.$clientes['id'].'">'.$clientes['id'].' - '.$clientes['nombre'].' '.$clientes['apellidos'].'</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Cantidad Solicitada<span class="text-danger"> *</span></label>
                            <input type="number" id="cantidad_solicitada" name="cantidad_solicitada" placeholder="1000.56" step="0.01" min="0" max="1000000" value="<?php echo $prestamo_row['cantidad_solicitada']; ?>" onblur="validate(7)" required>
                        </div>
                    </div>
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Amortización<span class="text-danger"> *</span></label>
                            <input type="number" id="amortizacion" name="amortizacion" placeholder="1000.56" step="0.01" min="0" max="1000000" value="<?php echo $prestamo_row['amortizacion']; ?>" onblur="validate(8)" required>
                        </div>
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Tipo de Interes<span class="text-danger"> *</span></label>
                            <input type="number" id="tipo_interes" name="tipo_interes" placeholder="[0-100]" onblur="validate(9)" min="0" max="100" value="<?php echo $prestamo_row['tipo_interes']; ?>" required>
                        </div>
                    </div>
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Fecha de Inicio<span class="text-danger"> *</span></label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" onblur="validate(10)" value="<?php echo $prestamo_row['fecha_inicio']; ?>"required>
                        </div>
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Día de Cobro del interes<span class="text-danger"> *</span></label>
                            <input type="number" id="fecha_interes" min="1" max="31" name="fecha_interes" placeholder="[1-31]" value="<?php echo $prestamo_row['fecha_interes']; ?>" onblur="validate(12)"required>
                        </div>
                    </div>
                    <div class="row justify-content-between text-left">
                        <div class="form-group col-sm-6 flex-column d-flex">
                            <label class="form-control-label px-3">Fecha Final<span class="text-danger"> *</span></label>
                            <input type="date" id="fecha_final" name="fecha_final" value="<?php echo $prestamo_row['fecha_final']; ?>" onblur="validate(11)" required>
                        </div>
                        <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Estado<span class="text-danger">
                                        *</span></label>
                                <select class="form-select form-select-lg" aria-label="Large select example" name="estado" required>
                                    <option value="<?php echo $prestamo_row['estado']; ?>"><?php echo $prestamo_row['estado']; ?></option>
                                    <option value="activo">Prestamo Activa</option>
                                    <option value="pendiente">Prestamo Pendiente</option>
                                    <option value="desactivado">Prestamo Finalizada</option>
                                </select>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <div class="form-group col-sm-6">
                            <button type="submit" class="btn-block btn-primary" name="submit">Modificar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $('.select2').select2();
</script>
<?php include 'template/footer.php'; ?>