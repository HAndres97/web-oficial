<?php
    include 'template/sesion.php';
    include 'template/cabecera.php';
?>
<?php
    if(isset($_POST['submit'])){
        $fname = $_POST['fname'];
        $cantidad = $_POST['cantidad'];
        $sql = "INSERT INTO patrimonios (nombre, cantidad) VALUES ('$fname', '$cantidad')";
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
?>
    <div class="body">
        <div class="container-xxl px-1 py-5 mx-auto">
            <div class="row d-flex justify-content-center">
                <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
                    <h3>Formulario de Registro de Patrimonio</h3>
                    <div class="card">
                        <h5 class="text-center mb-4">Rellenar todos los campos</h5>
                        <form method="POST" action="patrimonio.php" class="form-card">
                            <div class="row justify-content-between text-left">
                                <div class="form-group col-sm-6 flex-column d-flex">
                                     <label class="form-control-label px-3">Nombre<span class="text-danger"> *</span></label>
                                      <input type="text" id="fname" name="fname" placeholder="Nombre del patrimonio" onblur="validate(1)" required> 
                                </div>
                            </div>
                            <div class="row justify-content-between text-left">
                                <div class="form-group col-sm-6 flex-column d-flex"> 
                                    <label class="form-control-label px-3">Cantidad del Patrimonio<span class="text-danger"> *</span></label> 
                                    <input type="number" id="lname" name="cantidad"  placeholder="1000.56" step="0.01" min="0" max="1000000" onblur="validate(2)" required> 
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
    <div>
<?php include 'template/footer.php'; ?>