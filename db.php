<?php
// Archivo de conexión a la base de datos

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';      
$DATABASE_PASS = '';          
$DATABASE_NAME = 'tarea_login';

// Crear la conexión usando mysqli 
$conexion = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

// Verificar si hubo un error
if (mysqli_connect_errno()) {
    // Si hay error, detenemos el script y mostramos el problema
    exit('Falló la conexión a MySQL: ' . mysqli_connect_error());
}

// Establecer el charset a UTF-8 para manejar correctamente tildes y eñes
mysqli_set_charset($conexion, 'utf8mb4');

?>