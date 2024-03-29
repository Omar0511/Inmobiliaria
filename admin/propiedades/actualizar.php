<?php
    // session_start();

    // $auth = $_SESSION['login'];

    // if (!$auth) {
    //     header('Location: /login.php');
    // }

    require '../../include/funciones.php';
    $auth = estaAutenticado();

    if (!$auth) {
        header('Location: /login.php');
    }
    
    // Validamos que el ID sea un ENTERO
    $id = $_GET['id'];
    $id = filter_var($id, FILTER_VALIDATE_INT);

    if (!$id) {
        header('Location: /admin');
    }

    // Base de Datos
    require '../../include/config/database.php';
    // Mandamos llamar la función que viene de database.php
    $db = conectarDB();

    // Consultar Porpiedades por ID
    $consulta = "SELECT * FROM propiedades WHERE id = ${id}";
    $resultado = mysqli_query($db, $consulta);
    $propiedad = mysqli_fetch_assoc($resultado);

    // Consultar vendedores
    $query = "SELECT * FROM vendedores";
    $resultado = mysqli_query($db, $query);

    // Arreglo con mensaje de errores
    $errores = [];

    $titulo = $propiedad['titulo'];
    $precio = $propiedad['precio'];
    $descripcion = $propiedad['descripcion'];
    $habitaciones = $propiedad['habitaciones'];
    $wc = $propiedad['wc'];
    $estacionamiento = $propiedad['estacionamiento'];
    $vendedor_id = $propiedad['vendedor_id'];
    $imagenPropiedad = $propiedad['imagen'];

    // Ejecutar el código después de que el usuario envía el formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        /*
            Mostrando lo que manda el FORM
            echo '<pre>';
                var_dump($_POST);
            echo '</pre>';
        */

        // Sanitizando y/o Evitando INYECTION SQL
        $titulo = mysqli_real_escape_string($db, $_POST['titulo'] );
        $precio = mysqli_real_escape_string($db, $_POST['precio'] );
        $descripcion = mysqli_real_escape_string($db, $_POST['descripcion'] );
        $habitaciones = mysqli_real_escape_string($db, $_POST['habitaciones'] );
        $wc = mysqli_real_escape_string($db, $_POST['wc'] );
        $estacionamiento = mysqli_real_escape_string($db, $_POST['estacionamiento'] );
        // $vendedor_id = mysqli_real_escape_string($db, $_POST['vendedor_id'] );
        
        // Asignar vendedor_id si está definido en $_POST, de lo contrario, asignar un valor predeterminado (por ejemplo, '')
        $vendedor_id = isset($_POST['vendedor_id']) ? mysqli_real_escape_string($db, $_POST['vendedor_id']) : '';

        $creado = date('Y/m/d');

        // Asignar FILES hacia una variable
        $imagen = $_FILES['imagen'];

        if (!$titulo) {
            $errores[] = "El Título es obligatorio";
        }

        if (!$precio) {
            $errores[] = "El Precio es obligatorio";
        }

        if ( strlen( $descripcion) < 50 ) {
            $errores[] = "La Descripción debe tener minímo 50 carácteres...";
        }

        if (!$habitaciones) {
            $errores[] = "Las Habitaciones son obligatorias";
        }

        if (!$wc) {
            $errores[] = "El WC es obligatorio";
        }

        if (!$estacionamiento) {
            $errores[] = "El Estacionamiento es obligatorio";
        }

        if (!$vendedor_id) {
            $errores[] = "El Vendedor es obligatorio";
        }

        // Convertir de Bytes a KyloBytes, validar por tamaño (100 kb máximo)
        $medida = 1000 * 1000;

        if ($imagen['size'] > $medida) {
            $errores[] = 'La Imagen es muy pesada';
        }

        // Revisar que el array de $errores este vacío
        if ( empty($errores) ) {
            // SUBIDA DE ARCHIVOS
            $carpetaImagenes = '../../imagenes/';

            // Validar si una carpeta existe: is_dir();
            if ( !is_dir($carpetaImagenes) ) {
                // Crear carpeta
                mkdir($carpetaImagenes);
            }

            $nombreImagen = '';

            // unlink() = Elimina Archivos
            if ( $imagen['name'] ) {
                unlink($carpetaImagenes . $propiedad['imagen']);

                // Generar nombre único de img
                $nombreImagen = md5( uniqid( rand(), true ) ) . ".jpg";

                // Subir la imagen
                move_uploaded_file($imagen['tmp_name'], $carpetaImagenes . $nombreImagen );

            } else {
                $nombreImagen = $propiedad['imagen'];
            }

            // INSERT a la BD
            $query = "UPDATE propiedades SET titulo = '${titulo}', precio = '${precio}', imagen = '${nombreImagen}', descripcion = '${descripcion}', habitaciones = ${habitaciones}, wc = ${wc}, estacionamiento = ${estacionamiento}, vendedor_id = ${vendedor_id} WHERE id = ${id}
            ";

            // Probar query: echo $query;
            $resultado = mysqli_query($db, $query);

            if ($resultado) {
                header('Location: /admin?resultado=2');
            }
        }

    }

    // Mandamos a llamar la función de incluirTemplates()
    incluirTemplates('header');
?>

    <main class="contenedor seccion">
        <h1>Actualizar</h1>

        <a href="/admin" class="boton boton-verde-inline-block">Volver</a>

        <?php
            foreach($errores as $e) {
        ?>
            <div class="alerta error"> <?php echo $e; ?> </div>
        <?php
            }
        ?>

        <!-- enctype="multipart/form-data": se agrega si queremos subir archivos y/o imagénes -->
        <form class="formulario" method="POST" enctype="multipart/form-data">
            <fieldset>
                <legend>Información General</legend>

                <label for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo" placeholder="Ingresa el Título" value="<?php echo $titulo; ?>">

                <label for="precio">Precio</label>
                <input type="number" name="precio" id="precio" placeholder="Ingresa el precio" value="<?php echo $precio; ?>">

                <label for="imagen">Imagen</label>
                <!-- accept: indica que aceptará, en este caso imagénes con formato jpeg, png -->
                <input type="file" name="imagen" id="imagen" accept="image/jpeg, image/png" >
                <!-- Las imagénes no es buena opción mostrarlas al ACTUALIZAR, se recomienda solo mostrarla -->
                <img src="/imagenes/<?php echo $propiedad['imagen'];?>" alt="Imagen actual">

                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion"><?php echo $descripcion; ?></textarea>
            </fieldset>

            <fieldset>
                <legend>Información Propiedad</legend>

                <label for="habitaciones">Habitaciones</label>
                <input type="number" name="habitaciones" id="habitaciones" placeholder="Ej: 3" min="1" max="9" value="<?php echo $habitaciones; ?>">

                <label for="wc">Wc</label>
                <input type="number" name="wc" id="precio" placeholder="Ej: 3" min="1" max="9" value="<?php echo $wc; ?>">

                <label for="estacionamiento">Estacionamiento</label>
                <!-- accept: indica que aceptará, en este caso imagénes con formato jpeg, png -->
                <input type="number" name="estacionamiento" id="estacionamiento" placeholder="Ej: 3" min="1" max="9" value="<?php echo $estacionamiento; ?>">
            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>

                <label for="vendedor_id">Vendedor</label>
                <select id="vendedor_id" name="vendedor_id">
                    <option value="" disabled selected>-- Seleccione una opción --</option>
                    <?php 
                        while($row = mysqli_fetch_assoc($resultado) ): 
                    ?>
                            <option 
                                <?php echo $vendedor_id === $row['id'] ? 'selected' : ''; ?>  
                                value="<?php echo $row['id']; ?>"
                            >
                                <?php echo $row['nombre'] . " " . $row['apellido']; ?>
                            </option>
                    <?php 
                        endwhile; 
                    ?>
                </select>
            </fieldset>

            <input type="submit" value="Actualizar Propiedad" class="boton boton-verde-inline-block">
        </form>
    </main>

<?php
    incluirTemplates('footer');
?>