<?php
// filepath: /c:/xampp/htdocs/WebCarlos/clientes.php
include 'template/sesion.php';
include 'template/cabecera.php';

// Inicializar variables
$buscarDNI = "";
$buscarNombre = "";
$mensaje_error = "No hay clientes registrados";

// Obtener lista de clientes
$where = " WHERE 1=1 ";
if (!empty($_GET['buscarDNI'])) {
    $buscarDNI = $_GET['buscarDNI'];
    $where .= " AND id LIKE '%$buscarDNI%'";
}

if (!empty($_GET['buscarNombre'])) {
    $buscarNombre = $_GET['buscarNombre'];
    $where .= " AND (nombre LIKE '%$buscarNombre%' OR apellidos LIKE '%$buscarNombre%')";
}

$sql = "SELECT * FROM clientes $where ORDER BY apellidos ASC";
$lista_clientes = mysqli_query($conn, $sql);

// Contar el número de clientes
$num_clientes = mysqli_num_rows($lista_clientes);

// Variables para la paginación
$clientes_por_pagina = 10; // Número de clientes por página
$paginas = ceil($num_clientes / $clientes_por_pagina); // Calculamos el número de páginas

// Determinar la página actual
$nume = isset($_REQUEST["nume"]) ? $_REQUEST["nume"] : '1';
if ($nume == "" || !is_numeric($nume) || $nume < 1) {
    $nume = 1;
}
$pagina = (int)$nume;
$inicio = ($pagina - 1) * $clientes_por_pagina;

// Realizar la consulta con límite y desplazamiento
$sql_paginado = "SELECT * FROM clientes $where ORDER BY apellidos ASC LIMIT $inicio, $clientes_por_pagina";
$lista_clientes_paginados = mysqli_query($conn, $sql_paginado);
?>

<div class="d-grid gap-2 col-6 mt-3 mb-3 mx-auto">
    <a class="btn btn-primary" href="registrar_cliente.php" role="button">Registrar Cliente</a>
</div>

<?php if ($num_clientes > 0): ?>
    <div class='w-75 mx-auto'>
        <h3 class='text-center'>Lista de Clientes</h3>
        <form method='GET' action='clientes.php'>
            <div class="table-responsive">
                <table class='table table-bordered table-hover mx-4 my-4 align-middle' id='tablaClientes'>
                    <caption>Lista de Clientes</caption>
                    <thead>
                        <tr class='text-center text-uppercase table-light'>
                            <th></th>
                            <th scope='col'>
                                <div class='input-group'>
                                    <input type='text' class='form-control' name='buscarDNI' placeholder='Buscar DNI' id='busquedaDNI' maxlength="9" value='<?php echo htmlspecialchars($buscarDNI); ?>'>
                                    <div class='input-group-append'>
                                        <button class='btn btn-secondary' type='submit'>
                                            <i class='bi bi-search'></i>
                                        </button>
                                    </div>
                                </div>
                            </th>
                            <th scope='col' class='text-center text-uppercase'>
                                <div class='input-group'>
                                    <input type='text' class='form-control' name='buscarNombre' placeholder='Buscar Nombre' id='busquedaNombre' value='<?php echo htmlspecialchars($buscarNombre); ?>'>
                                    <div class='input-group-append'>
                                        <button class='btn btn-secondary' type='submit'>
                                            <i class='bi bi-search'></i>
                                        </button>
                                    </div>
                                </div>
                            </th>
                            <th scope='col'></th>
                        </tr>
                        <tr class='text-center text-uppercase table-light'>
                            <th>#</th>
                            <th scope='col'>DNI</th>
                            <th scope='col' class='text-center text-uppercase'>Nombre y Apellidos</th>
                            <th scope='col'>CONTACTO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $contador = $inicio + 1;
                        while ($row = mysqli_fetch_array($lista_clientes_paginados)): ?>
                            <tr class='text-center text-uppercase'>
                                <td><?php echo $contador++; ?></td>
                                <td><?php echo $row['id']; ?></td>
                                <td><a href='mostrar_cliente.php?id=<?php echo $row['id']; ?>'> <?php echo $row['nombre'] ." ". $row['apellidos']; ?></a></td>
                                <td><?php echo $row['movil']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </form>
 <!-- Paginación -->
 <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                // Mantener los parámetros de búsqueda en los enlaces de paginación
                $query_string = http_build_query([
                    'buscarDNI' => $buscarDNI,
                    'buscarNombre' => $buscarNombre
                ]);
                for ($i = 1; $i <= $paginas; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                        <a class="page-link" href="?nume=<?php echo $i; ?>&<?php echo $query_string; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
<?php else: ?>
    <div class='alert alert-danger text-center' role='alert'>
        <?php echo $mensaje_error; ?>
    </div>
    <div class='d-grid gap-2 col-6 mx-auto'>
        <a href="clientes.php" class="btn btn-primary">Volver</a>
    </div>
<?php endif; ?>

<?php include 'template/footer.php'; ?>