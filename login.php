<?php
    require 'include/config/database.php';
    $db = conectarDB();

    $errores = [];

    // Autenticar Usuario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = mysqli_real_escape_string($db, filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL));
        $password = mysqli_real_escape_string($db, $_POST['password']);

        if (!$email) {
            $errores[] = "El E-mail es obligatorio o no es válido";
        }

        if (!$password) {
            $errores[] = "El Password es obligatorio";
        }

        if ( empty($errores) ) {
            // Revisar si el usuario existe
            $query = "SELECT * FROM usuarios WHERE email = '${email}' ";
            $resultado = mysqli_query($db, $query);

            if ($resultado->num_rows) {
                // Revisar si el Password es correcto
                $usuario = mysqli_fetch_assoc($resultado);

                // Verificar si el password es correcto
                $auth = password_verify($password, $usuario['password']);

                if($auth) {
                    // El Usuario esta autenticado
                    session_start();

                    // Llenar el arreglo de sesión
                    $_SESSION['usuario'] = $usuario['email'];
                    $_SESSION['login'] = true;

                    header('Location: /admin');
                } else {
                    $errores[] = "El Password es incorrecto";
                }

            } else {
                $errores[] = "El Usuario NO existe";
            }
        }
    }

    require 'include/funciones.php';

    incluirTemplates('header');
?>

    <main class="contenedor seccion contenido-centrado">
        <h1>Iniciar Sesión</h1>

        <?php 
            foreach($errores as $e) { 
        ?>
                <div class="alerta error"> <?php echo $e; ?> </div>
        <?php 
            } 
        ?>

        <form class="formulario" method="POST">
            <fieldset>
                <legend>Email y Password</legend>

                <label for="email">E-mail</label>
                <input type="mail" name="email" id="email" placeholder="Ingresa tu E-mail">

                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Ingresa tu Password">

                <input type="submit" value="Iniciar Sesión" class="boton boton-verde-block">
            </fieldset>
        </form>
    </main>

<?php
    incluirTemplates('footer');
    
    //include './include/templates/footer.php';
?>