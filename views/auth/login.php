<div class="login">
    <div class="login-card">
        <div class="cont">
            <h1 class="logo-skg">SKG</h1>
            <p class="extra">Gestiona tus créditos </p>
        </div>

        <div class="cont">

            <form class="formulario_l" action="" method="POST">

                <fieldset>
                    <?php if (!empty($alertas)): ?>
                        <div class="alertas">
                            <?php foreach ($alertas as $tipo => $mensajes): ?>
                                <?php foreach ($mensajes as $mensaje): ?>
                                    <div class="alerta <?= htmlspecialchars($tipo) ?>">
                                        <?= htmlspecialchars($mensaje) ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <legend class="descripcion">
                        Iniciar sesión en tu cuenta
                    </legend>
                    <div class="campof">
                        <label for="usuario">Usuario</label>
                        <input type="text" id="usuario" name="usuario" placeholder="Tu usuario" required>
                    </div>

                    <div class="campof">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>

                    <button type="submit" class="btn-login">Iniciar Sesión</button>
                </fieldset>


            </form>


        </div>
    </div>
</div>