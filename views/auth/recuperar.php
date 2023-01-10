<h1 class="nombre-pagina"><?php echo $titulo; ?></h1>
<p class="descripcion-pagina">Coloca tu nueva contraseña</p>

<?php
include_once __DIR__ . "/../templates/alertas.php";
?>

<form class="formulario" method="POST">
    <div class="campo">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" placeholder="Tu contraseña">
    </div>

    <input class="boton" type="submit" value="Guardar Contraseña">
</form>

<div class="acciones">
    <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
    <a href="/">¿Aun no tines una cuenta? Crear Cuenta</a>
</div>