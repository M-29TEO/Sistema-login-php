# Sistema de Autenticación de Usuarios en PHP

## Descripción Breve
Sistema web de autenticación desarrollado en PHP que permite:
- Registro de nuevos usuarios
- Inicio de sesión seguro
- Zona privada de perfil
- Actualización de datos personales
- Cambio de contraseña con verificación
- Cierre de sesión

## Requisitos del Sistema
- **PHP** 7.4 o superior
- **MySQL** 5.7 o superior
- **Servidor web:** Apache (XAMPP, WAMP, MAMP)
- **Navegador:** Chrome, Firefox, Edge

## Instalación y Prueba Local

### Clonar o descargar el repositorio
```bash
git clone https://github.com/TU_USUARIO/TU_REPOSITORIO.git

//Pasos
Mover a la carpeta del servidor
Copia la carpeta del proyecto a:

XAMPP: C:\xampp\htdocs\

WAMP: C:\wamp\www\

MAMP: /Applications/MAMP/htdocs/

Crear la base de datos
Abrir phpMyAdmin: http://localhost/phpmyadmin

Crear una base de datos llamada tarea_login

Ejecutar el siguiente SQL en la pestaña "SQL":

sql
CREATE TABLE usuarios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    cedula VARCHAR(20) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_registro DATETIME NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY correo_unique (correo)
);

Configurar conexión
Verificar que el archivo db.php tenga estos datos:

php
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'tarea_login';

Probar la aplicación
Acceder desde el navegador:

text
http://localhost/TAREA_DESARROLLOWEB/registro.php

 Flujo de prueba
Registrar un nuevo usuario

Iniciar sesión con el correo y contraseña

Actualizar nombre y correo en el perfil

Cambiar la contraseña

Cerrar sesión

Estructura de Archivos
Archivo	Función
registro.php	Formulario y lógica de registro
login.php	Formulario y lógica de inicio de sesión
perfil.php	Zona privada y actualización de datos
cambiar_password.php	Cambio seguro de contraseña
logout.php	Destrucción de sesión
db.php	Conexión a la base de datos
