<div class="contenedor-sm login">


    <?php include_once __DIR__ . '/../templates/nombre-sitio.php' ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Iniciar Sesión</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>
        <form class="formulario" action="" method="POST">
            <div class="campo">
                <label for="usuario">Usuario:</label>
                <input type="text" id="usuario" placeholder="Tu usuario" name="usuario">
            </div>
            <div class="campo">
                <label for="password">Password:</label>
                <input type="password" id="password" placeholder="Tu password" name="password">
            </div>
            <input type="submit" class="boton" value="Iniciar Sesión">
        </form>
    </div>
</div>