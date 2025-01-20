<?php include 'template/sesion.php'; ?>
<?php
$id = $_GET['id'];
$cantidad_gasto = $_GET['cantidad'];
$inversion = mysqli_query($conn, "SELECT * FROM inversiones WHERE id = '$id'");
$row = mysqli_fetch_array($inversion);
if(isset($_POST['submit'])){
    $fname = $_POST['fname'];
        $estado_inversion = $_POST['inversion'];
        $cantidad = $_POST['cantidad'];
        $date = $_POST['date'];
        $ans = $_POST['ans'];
    $sql = "UPDATE inversiones SET nombre = '$fname', cantidad = '$cantidad', fecha_registro = '$date', estado = '$estado_inversion', comentario = '$ans' WHERE id = '$id'";
    if (mysqli_query($conn, $sql)) {
                ;
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
    $patrimonio = mysqli_query($conn, "SELECT * FROM patrimonios WHERE id = 1");
    $row = mysqli_fetch_array($patrimonio);
    $patrimonio_cantidad = $row['cantidad'] + $cantidad_gasto - $cantidad;
    $sql = 'UPDATE patrimonios SET cantidad = '.$patrimonio_cantidad.' WHERE id = 1';
    if (mysqli_query($conn, $sql)) {
        header('Location: inversiones.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

}
?>
<?php include 'template/cabecera.php'; ?>
<!-- Formulario de registro de Inversiones -->
<div class="body">
    <div class="container-xxl px-1 py-5 mx-auto">
        <div class="row d-flex justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
                <h3>Modificar Inversiones</h3>
                <div class="card">
                    <h5 class="text-center mb-4">Rellenar todos los campos</h5>
                    <form method="POST" action="modificar_inversion.php?id=<?php echo $id; ?>&cantidad=<?php echo $cantidad_gasto; ?>" class="form-card">
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Nombre de la inversión<span class="text-danger">
                                        *</span></label>
                                <input type="text" id="fname" name="fname" value="<?php echo $row['nombre']; ?>" placeholder="Gasto" onblur="validate(1)"
                                    required>
                            </div>
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Estado de inversion<span class="text-danger">
                                        *</span></label>
                                <select class="form-select form-select-lg" aria-label="Large select example" name="inversion" required>
                                    <?php
                                    if($row['estado'] == 'activo'){
                                        $estado = 'Inversion Activa';
                                    }else if($row['estado'] == 'pendiente'){
                                        $estado = 'Inversion Pendiente';
                                    }else{
                                        $estado = 'Inversion Finalizada';
                                    }
                                ;?>
                                    <option value="<?php echo $row['estado']; ?>"><?php echo $estado ?></option>
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
                                    min="0" max="1000000" onblur="validate(2)" value="<?php echo $row['cantidad']; ?>" required>
                            </div>
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Fecha<span class="text-danger"> *</span></label>
                                <input type="date" class="form-control"  name="date" value="<?php echo $row['fecha_registro']; ?>" required/>
                            </div>
                        </div>
                        <div class="row justify-content-between text-left">
                                <div class="form-group col-12 flex-column d-flex"> 
                                    <label class="form-control-label px-3">Comentario<span class="text-danger"> *</span></label>
                                     <textarea  id="ans" name="ans" placeholder="Alguna observacion sobre el cliente" onblur="validate(6)"> <?php echo $row['comentario']; ?> </textarea>
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
</div>
<?php include 'template/footer.php'; ?>