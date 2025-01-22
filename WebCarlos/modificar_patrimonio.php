<?php
    include("template/sesion.php");
    // Actualizar patrimonio
    if(isset($_POST['submit'])){
        $fname = $_POST['fname'];
        $cantidad = $_POST['cantidad'];
        $sql = "UPDATE patrimonios SET nombre = '$fname', cantidad = '$cantidad' WHERE id = 1";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
    // Consulta para obtener los datos de la tabla patrimonios
    $consulta_patrimonio = "SELECT * FROM patrimonios where id = 1";
    $patrimonios = mysqli_query($conn, $consulta_patrimonio);
    $row = mysqli_fetch_array($patrimonios);

?>
<?php include 'template/cabecera.php'; ?>
    <div class="body">
        <div class="container-xxl px-1 py-5 mx-auto">
            <div class="row d-flex justify-content-center">
                <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
                    <h3>Formulario de Modificar Patrimonio</h3>
                    <div class="card">
                        <h5 class="text-center mb-4">Rellenar todos los campos</h5>
                        <form method="POST" action="modificar_patrimonio.php" class="form-card">
                            <div class="row justify-content-between text-left">
                                <div class="form-group col-sm-6 flex-column d-flex">
                                     <label class="form-control-label px-3">Nombre<span class="text-danger"> *</span></label>
                                      <input type="text" id="fname" name="fname" value="<?php echo $row['nombre']; ?>" placeholder="Nombre del patrimonio" onblur="validate(1)" required> 
                                </div>
                            </div>
                            <div class="row justify-content-between text-left">
                                <div class="form-group col-sm-6 flex-column d-flex"> 
                                    <label class="form-control-label px-3">Cantidad del Patrimonio<span class="text-danger"> *</span></label> 
                                    <input type="number" id="lname" name="cantidad" value="<?php echo $row['cantidad']; ?>"  placeholder="1000.56" step="0.01" min="0" max="1000000" onblur="validate(2)" required> 
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
    <div>
<?php include 'template/footer.php'; ?>