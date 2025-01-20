<?php include 'template/sesion.php'; ?>
<?php
$id = $_GET['id'];
$cantidad_gasto = $_GET['cantidad'];
$gasto = mysqli_query($conn, "SELECT * FROM gastos WHERE id = '$id'");
$row = mysqli_fetch_array($gasto);

if (isset($_POST['submit'])) {
    $fname = $_POST['fname'];
    $cantidad = $_POST['cantidad'];
    $date = $_POST['date'];
    $sql = "UPDATE gastos SET nombre = '$fname', cantidad = '$cantidad', fecha = '$date' WHERE id = '$id'";
    if (mysqli_query($conn, $sql)) {
        // Actualizar patrimonio
        $patrimonio = mysqli_query($conn, "SELECT * FROM patrimonios WHERE id = 1");
        $row = mysqli_fetch_array($patrimonio);
        $patrimonio_cantidad = $row['cantidad'] + $cantidad_gasto - $cantidad;
        $sql = 'UPDATE patrimonios SET cantidad = ' . $patrimonio_cantidad . ' WHERE id = 1';
        if (mysqli_query($conn, $sql)) {
            header('Location: pagos.php');
            exit(); // Asegúrate de llamar a exit() después de header() para detener la ejecución del script
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
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
                <h3>Modificar Gasto</h3>
                <div class="card">
                    <h5 class="text-center mb-4">Rellenar todos los campos</h5>
                    <form method="POST"
                        action="modificar_pago.php?id=<?php echo $row['id']; ?>&cantidad=<?php echo $row['cantidad']; ?>"
                        class="form-card">
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Nombre del gasto<span class="text-danger">
                                        *</span></label>
                                <input type="text" id="fname" name="fname" value="<?php echo $row['nombre']; ?>"
                                    placeholder="Gasto" required>
                            </div>
                        </div>
                        <div class="row justify-content-between text-left">
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Cantidad<span class="text-danger">
                                        *</span></label>
                                <input type="number" id="cantidad" name="cantidad"
                                    value="<?php echo $row['cantidad']; ?>" placeholder="1000.56" step="0.01" min="0"
                                    max="1000000" required>
                            </div>
                            <div class="form-group col-sm-6 flex-column d-flex">
                                <label class="form-control-label px-3">Fecha<span class="text-danger"> *</span></label>
                                <input type="date" class="form-control" name="date" value="<?php echo $row['fecha']; ?>"
                                    required>
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