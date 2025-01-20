<?php
// filepath: /c:/xampp/htdocs/WebCarlos/template/sesion.php

// Iniciar la sesión
session_start();

// Incluir archivos necesarios
include_once 'administrador/config/databaseconnect.php';
include_once 'funciones.php';
include_once 'actualizar_intereses.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    // Redirigir al usuario a la página de inicio de sesión
    header("Location: login.php");
    // Destruir la sesión
    session_destroy();
    // Terminar el script
    exit();
}
?>