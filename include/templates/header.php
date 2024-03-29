<?php
    if ( !isset($_SESSION) ) {
        session_start();
    }

    $auth = $_SESSION['login'] ?? false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienes Raíces</title>
    <link rel="stylesheet" href="/build/css/app.css">
</head>
<body>
    <!--
        inicio, lo modificamos para que siga haciendo su función, es decir; mientras este la clase de: inicio, mostrará
        ciertos estilos, mientras NO, pues no aplica esos estilos.
        Al modificar en este apartado, crearemos la variable en el archvio de index.php para que funcione.
        
        <header class="header inicio"> 
    -->

    <header class="header <?php echo $inicio ? 'inicio' : ''; ?> ">
        <div class="contenedor contenido-header">
            <div class="barra">
                <a href="/index.php">
                    <img src="/build/img/logo.svg" alt="Logotipo Bienes Raíces">
                </a>

                <div class="mobile-menu">
                    <img src="/build/img/barras.svg" alt="Icono Menú Hamburguesa">
                </div>

                <div class="derecha">
                    <img class="dark-mode-boton" src="/build/img//dark-mode.svg" alt="Icono Dark Mode">

                    <nav class="navegacion">
                        <?php
                            if (!$auth) {
                        ?>
                                <a href="/login.php">Iniciar Sesión</a>
                        <?php 
                            }
                        ?>                        
                        <a href="nosotros.php">Nostros</a>
                        <a href="anuncios.php">Anuncios</a>
                        <a href="blog.php">Blog</a>
                        <a href="contacto.php">Contacto</a>
                        <?php
                            if ($auth) {
                        ?>
                                <a href="cerrar-sesion.php">Cerrar Sesión</a>
                        <?php 
                            }
                        ?>
                    </nav>
                </div>
            </div>

            <?php
                if ( $inicio ) {
            ?>
                    <h1>Venta de Casas y Departamentos Exclusivos de Lujo</h1>
            <?php
                }
            ?>
        </div>
    </header>