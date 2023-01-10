<div class="barra">
    <p>Hola: <span><?php echo $nombre ?? ""; ?></span></p>
    <a href="/logout" class="boton">Cerrar Sesión</a>
</div>

<?php 
if(isset($_SESSION["admin"])): ?>

<div class="barra-servicios">
    <a href="/admin" class="boton">Ver Citas</a>
    <a href="/servicios" class="boton">Ver Servicios</a>
    <a href="/servicios/crear" class="boton">Crear Servicio</a>
</div>

<?php endif; ?>