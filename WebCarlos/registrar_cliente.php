<?php
    include 'template/sesion.php';
    include 'template/cabecera.php';
?>
<?php
    $usuario_registrado = 2;
    if(isset($_POST['submit'])){
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $mob = $_POST['mob'];
        $Dni = $_POST['Dni'];
        $ans = $_POST['ans'];
        $validar_usua = mysqli_query($conn, "SELECT * FROM clientes WHERE id = '$Dni'");
        if(mysqli_num_rows($validar_usua) > 0){
                $usuario_registrado = 0;
        }else{
            $sql = "INSERT INTO clientes (id, nombre, apellidos, movil, email, comentario) VALUES ('$Dni', '$fname', '$lname', '$mob','$email','$ans')";
            if (mysqli_query($conn, $sql)) {
                $usuario_registrado = 1;
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }
?>
    <div class="body">
        <div class="container-xxl px-1 py-5 mx-auto">
            <div class="row d-flex justify-content-center">
                <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
                    <h3>Formulario de Registro de Cliente</h3>
                    <div class="card">
                        <h5 class="text-center mb-4">Rellenar todos los campos</h5>
                        <?php
                            if($usuario_registrado == 1){
                                echo "<div class='alert alert-success' role='alert'>
                                        Cliente Creado Correctamente
                                    </div>";
                            }else if($usuario_registrado == 0){
                                echo "<div class='alert alert-danger' role='alert'>
                                        Cliente ya registrado
                                    </div>";
                            }
                        ?>
                        <form method="POST" action="registrar_cliente.php" class="form-card">
                            <div class="row justify-content-between text-left">
                                <div class="form-group col-sm-6 flex-column d-flex">
                                     <label class="form-control-label px-3">Nombre<span class="text-danger"> *</span></label>
                                      <input type="text" id="fname" name="fname" placeholder="Nombre del cliente" onblur="validate(1)" required> 
                                </div>
                                <div class="form-group col-sm-6 flex-column d-flex">
                                     <label class="form-control-label px-3">Apellidos<span class="text-danger"> *</span></label>
                                      <input type="text" id="lname" name="lname" placeholder="Apellidos del cliente" onblur="validate(2)" required> 
                                </div>
                            </div>
                            <div class="row justify-content-between text-left">
                                <div class="form-group col-sm-6 flex-column d-flex"> 
                                    <label class="form-control-label px-3">Email<span class="text-danger"> *</span></label> 
                                    <input type="email" id="email" name="email" placeholder="carlos@gmail.com" onblur="validate(3)" required> 
                                </div>
                                <div class="form-group col-sm-6 flex-column d-flex"> 
                                    <label class="form-control-label px-3">Móvil<span class="text-danger"> *</span></label>
                                     <input type="text" id="mob" name="mob" placeholder=" +34 6.." minlength="9" maxlength="9" pattern="[0-9]{9}" onblur="validate(4)" required> 
                                </div>
                            </div>
                            <div class="row justify-content-between text-left">
                                <div class="form-group col-sm-6 flex-column d-flex"> 
                                    <label class="form-control-label px-3">Número de Documentación<span class="text-danger"> *</span></label> 
                                    <input type="text" id="job" name="Dni" placeholder="50584330Q" minlength="9" maxlength="9" onblur="validate(5)" required> 
                                </div>
                            </div>
                            <div class="row justify-content-between text-left">
                                <div class="form-group col-12 flex-column d-flex"> 
                                    <label class="form-control-label px-3">Dirección<span class="text-danger"> *</span></label>
                                     <textarea  id="ans" name="ans" placeholder="Alguna observacion sobre el cliente" onblur="validate(6)"> </textarea>
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