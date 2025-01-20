<?php
session_start();
include 'administrador/config/databaseconnect.php';
include 'funciones.php';
/*
    Comprobamos Usuario y Contraseña
    Cuidado con el numero de caracteres que guardamos con password_has se guarda 60 , si se ponde 50 carcateres se trunca los datos
*/
$error_login = false;
    if(isset($_POST['submit'])){
    /*
        Comprobamos Usuario, creamos Cookies e iniciamos sesion
    */
        $correo = $_POST['correo'];
        $password =$_POST['password'];
        $validar_login = mysqli_query($conn, "SELECT * FROM usuarios WHERE correo = '$correo'");

        if(verificar_usuario($validar_login,$password) == true){
            
            $_SESSION['usuario'] = $correo;
            if(isset($_POST['recordar'])){
                $_SESSION['password'] = $password;
                setcookie("usuario","$correo",time() + 604800 );
                setcookie("password","$password",time() + 604800 );
            }
            header("location:index.php");
        }else{
            $error_login = true;
        }
    }
    if(isset($_COOKIE['usuario'])){
        /*
        Si existe las cookies, nos mete directamente al index
        */
        $correo = $_COOKIE['usuario'];
        $password = $_COOKIE['password'];
        $validar_login = mysqli_query($conn, "SELECT * FROM usuarios WHERE correo = '$correo'");
        if(verificar_usuario($validar_login,$password) == true){
            
            $_SESSION['usuario'] = $correo;
            header("location:index.php");
        }
    }

?>
<!DOCTYPE html>
<html lang= "es">
    <head>
        <meta charset= "UTF-8">
        <meta name= "viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/login.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </head>
    <body>
            <div class="container-fluid ps-md-0">
        <div class="row g-0">
            <div class="d-none d-md-flex col-md-4 col-lg-6 bg-image"></div>
            <div class="col-md-8 col-lg-6">
            <div class="login d-flex align-items-center py-5">
                <div class="container">
                <div class="row">
                    <div class="col-md-9 col-lg-8 mx-auto">
                    <h3 class="login-heading mb-4">Bienvenido!</h3>
                    <?php
                        if($error_login != false){
                            echo "<div class='alert alert-danger' role='alert'>
                                    Usuario o contraseña incorrecta
                                </div>";
                        }
                    ?>
                    <!-- Sign In Form -->
                    <form action="login.php" method="POST">
                        <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingInput" placeholder="name@example.com" name="correo">
                        <label for="floatingInput">Usuario</label>
                        </div>
                        <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">
                        <label for="floatingPassword">Contraseña</label>
                        </div>

                        <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="recordar" id="rememberPasswordCheck" name="recordar">
                        <label class="form-check-label" for="rememberPasswordCheck">
                            Recordar usuario
                        </label>
                        </div>

                        <div class="d-grid">
                        <button class="btn btn-lg btn-primary btn-login text-uppercase fw-bold mb-2" type="submit" name ="submit">Entrar</button>
                        </div>

                    </form>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </body>
</html>
<?php mysqli_close($conn);?>
