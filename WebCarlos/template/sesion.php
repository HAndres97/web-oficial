<?php
/*
    Mirar lo de BASEURL
*/
session_start();
include 'administrador/config/databaseconnect.php';
include 'funciones.php';
include 'actualizar_intereses.php';
if (!isset($_SESSION['usuario'])) {
    header("location:login.php");
    session_destroy();
    die();
}
?>