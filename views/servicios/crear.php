<h1 class="nombre-pagina"><?php echo $titulo; ?></h1>
<p class="descripcion-pagina">Llena todos los campos para añadir un nuevo servicio</p>

<?php
include_once __DIR__ . "/../templates/barra.php";
include_once __DIR__ . "/../templates/alertas.php";
?>

<form action="/servicios/crear" class="formulario" method="POST">
    <?php
    include_once __DIR__ . "/formulario.php";
    ?>
    <input type="submit" class="boton" value="Crear Servicio">
</form>