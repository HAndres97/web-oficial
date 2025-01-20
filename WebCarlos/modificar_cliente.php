<?php include 'template/sesion.php'; ?>
<?php
$id = $_GET['id'];
$cliente = mysqli_query($conn, "SELECT * FROM clientes WHERE id = '$id'");
$row = mysqli_fetch_array($cliente);
if(isset($_POST['submit'])){
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $mob = $_POST['mob'];
    $ans = $_POST['ans'];
    $Dni = $_POST['Dni'];
    $sql = "UPDATE clientes SET nombre = '$fname', apellidos = '$lname', movil = '$mob', email = '$email', comentario = '$ans' WHERE id = '$Dni'";
    if (mysqli_query($conn, $sql)) {
        header('Location: mostrar_cliente.php?id='.$Dni);
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

}
?>
<?php include 'template/cabecera.php'; ?>
<div class="body">
    <div class="container-xxl px-1 py-5 mx-auto">
        <div class="row d-flex justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
                <h3>Modificar Cliente</h3>
                <div class="card">
                    <h5 class="text-center mb-4">Rellenar todos los campos</h5>
                    <form method="POST" action="modificar_cliente.php?id=<?php echo $row['id']; ?>" class="form-card">
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Nombre<span class="text-danger"> *</span></label>
                                <input type="text" id="fname" name="fname" placeholder="Nombre del cliente"
                                    onblur="validate(1)" value="<?php echo $row['nombre']; ?>" required>
                            </div>
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Apellidos<span class="text-danger">
                                        *</span></label>
                                <input type="text" id="lname" name="lname" placeholder="Apellidos del cliente"
                                    onblur="validate(2)" value="<?php echo $row['apellidos']; ?>" required>
                            </div>
                        </div>
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Email<span class="text-danger"> *</span></label>
                                <input type="email" id="email" name="email" placeholder="carlos@gmail.com"
                                    onblur="validate(3)" value="<?php echo $row['email']; ?>" required>
                            </div>
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Movil<span class="text-danger"> *</span></label>
                                <input type="text" id="mob" name="mob" placeholder=" +34 6.." minlength="9"
                                    maxlength="9" pattern="[0-9]{9}" value="<?php echo $row['movil']; ?>"
                                    onblur="validate(4)" required>
                            </div>
                        </div>
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Número de Documentación<span class="text-danger">
                                        *</span></label>
                                <input type="text" id="job" name="Dni" placeholder="50584330Q" minlength="9"
                                    maxlength="9" value="<?php echo $row['id']; ?>" onblur="validate(5)" required>
                            </div>
                        </div>
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-12 flex-column d-flex">
                                <label class="form-control-label px-3">Comentario<span class="text-danger">
                                        *</span></label>
                                <textarea id="ans" name="ans" placeholder="Alguna observacion sobre el cliente"
                                    onblur="validate(6)"><?php echo $row['comentario']; ?></textarea>
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="form-group col-sm-6">
                                <button type="submit" class="btn-block btn-primary" name="submit">Modificar
                                    Cliente</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'template/footer.php'; ?>