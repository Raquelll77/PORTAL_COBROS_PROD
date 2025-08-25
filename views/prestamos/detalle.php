<div class="contenedor">
    <?php
    $prenumero = htmlspecialchars($_GET['prenumero']);
    ?>



    <div id="frm-envio-datos" class="contenedor-95">
    <a class="boton-volver" href="<?= BASE_URL ?>/cobros?tab=<?= htmlspecialchars($_GET['tab'] ?? 'busqueda-clientes'); ?>">
    < Volver
</a>

                <h1 class="titulo-detalle">Informacion del Préstamo</h1>


                <?php if ($prestamoDetalle) { ?>
                    <h3>Detalles del Cliente</h3>
                    <hr>
                    <div class="contenido-detalle">
                        <?php foreach ($prestamoDetalle as $detalle) { ?>
                            <p><strong>Nombre:</strong> <?= $detalle['NombreCompleto']; ?></p>
                            <p><strong>Identidad:</strong> <?php echo htmlspecialchars($detalle['identidad']); ?></p>
                            <p><strong>Prestamo: </strong> <?= $_GET['prenumero'] ?></p>
                            <p><strong>Fecha de Aprobacion:</strong> <?= $_GET['fecha']; ?></p>
                            <p><strong>Tipo persona:</strong> <?php echo htmlspecialchars($detalle['tipo_persona']); ?></p>
                            <p><strong>Ciudad:</strong> <?php echo htmlspecialchars($detalle['ciudad']); ?></p>
                            <p><strong>Departamento:</strong> <?php echo htmlspecialchars($detalle['departamento']); ?></p>
                            <p><strong>Sexo:</strong> <?php echo htmlspecialchars($detalle['sexo']); ?></p>
                            <p><strong>Monto Prestamo:</strong> <?php echo htmlspecialchars($detalle['monto_prestamo']); ?></p>
                            <p><strong>Plazo:</strong> <?php echo htmlspecialchars($detalle['plazo']); ?> meses</p>
                            <p><strong>Tasa:</strong> <?php echo htmlspecialchars($detalle['tasa']); ?></p>
                            <p><strong>Estado Civil:</strong> <?php echo htmlspecialchars($detalle['estado_civil']); ?></p>
                            <p><strong>Nombre Conyugue:</strong> <?php echo htmlspecialchars($detalle['nombre_conyuge']); ?></p>
                            <p><strong>Profesion:</strong> <?php echo htmlspecialchars($detalle['profesion']); ?></p>
                            <p><strong>Fecha de nacimiento:</strong>
                                <?php echo htmlspecialchars($detalle['fecha_nacimiento']); ?></p>
                            <p><strong>Escolaridad:</strong> <?php echo htmlspecialchars($detalle['escolaridad']); ?></p>
                            <p><strong>Direccion:</strong> <?php echo htmlspecialchars($detalle['direccion']); ?></p>
                            <p><strong>Lugar de trabajo:</strong> <?php echo htmlspecialchars($detalle['empresa']); ?></p>
                            <p><strong>Puesto:</strong> <?php echo htmlspecialchars($detalle['empresa_puesto']); ?></p>
                            <p><strong>Direccion de trabajo:</strong>
                                <?php echo htmlspecialchars($detalle['empresa_direccion']); ?></p>

                        </div>

                        <h3>Informacion de producto</h3>
                        <hr>
                        <div class="contenido-detalle">
                            <p><strong>Marca Moto:</strong> <?php echo htmlspecialchars($detalle['moto_marca']); ?></p>
                            <p><strong>Modelo:</strong> <?php echo htmlspecialchars($detalle['moto_modelo']); ?></p>
                            <p><strong>Serie:</strong> <?php echo htmlspecialchars($detalle['moto_serie']); ?></p>
                            <p><strong>Color:</strong> <?php echo htmlspecialchars($detalle['moto_color']); ?></p>
                            <p><strong>Año:</strong> <?php echo htmlspecialchars($detalle['moto_ano']); ?></p>
                            <p><strong>Precio:</strong> L<?php echo htmlspecialchars($detalle['moto_valor']); ?></p>
                        </div>
                        <!-- Continúa agregando los demás campos de `$prestamoDetalle` aquí -->
                        <h3>Numeros de contactos</h3>
                        <hr>
                        <h4><strong>Cliente:</strong></h4>
                        <div class="contenido-detalle">
                            <p><strong>Telefono:</strong> <?php echo htmlspecialchars($detalle['telefono']); ?></p>
                            <p><strong>Telefono2:</strong> <?php echo htmlspecialchars($detalle['telefono2']); ?></p>
                            <p><strong>Telefono3:</strong> <?php echo htmlspecialchars($detalle['telefono3']); ?></p>
                            <p><strong>Celular:</strong> <?php echo htmlspecialchars($detalle['celular']); ?></p>
                        </div>
                        <h4><strong>Trabajo:</strong></h4>
                        <div class="contenido-detalle">
                            <p><strong>Telefono:</strong> <?php echo htmlspecialchars($detalle['empresa_telefono']); ?></p>
                            <p><strong>Telefono2:</strong> <?php echo htmlspecialchars($detalle['empresa_telefono2']); ?></p>
                        </div>
                        <h4><strong>Referencias:</strong></h4>
                        <div class="contenido-detalle-3">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Relación</th>
                                        <th>Celular</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo htmlspecialchars($detalle['ref1_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($detalle['ref1_relacion']); ?></td>
                                        <td><?php echo htmlspecialchars($detalle['ref1_telefono_celular']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo htmlspecialchars($detalle['ref2_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($detalle['ref2_relacion']); ?></td>
                                        <td><?php echo htmlspecialchars($detalle['ref2_telefono_celular']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo htmlspecialchars($detalle['ref3_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($detalle['ref3_relacion']); ?></td>
                                        <td><?php echo htmlspecialchars($detalle['ref3_telefono_celular']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo htmlspecialchars($detalle['ref4_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($detalle['ref4_relacion']); ?></td>
                                        <td><?php echo htmlspecialchars($detalle['ref4_telefono_celular']); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>


                        <?php include 'modal_referencias.php' ?>

                    <?php } ?>



                <?php } else { ?>
                    <p>No se encontraron detalles adicionales del cliente.</p>
                    <?php


                }
                ?>
                <section class="saldo-prestamo">
                    <h2>Saldo del Préstamo</h2>
                    <div class="saldo-grid">
                        <?php foreach ($saldoPrestamo as $saldo) { ?>
                            <div class="saldo-card">
                                <h3>Fecha de Pago</h3>
                                <p><?php echo $saldo['DiaPagoCuota']; ?> de cada mes</p>
                            </div>
                            <div class="saldo-card">
                                <h3>Saldo Hoy</h3>
                                <p>L <?php echo number_format($saldo['TotalACancelar'], 2); ?></p>
                            </div>
                            <div class="saldo-card">
                                <h3>Saldo Capital</h3>
                                <p>L <?php echo number_format($saldo['Saldo Capital'], 2); ?></p>
                            </div>
                            <div class="saldo-card">
                                <h3>Pago Mínimo</h3>
                                <p>L <?php echo number_format($saldo['TotalFecha'], 2); ?></p>
                            </div>
                            <div class="saldo-card">
                                <h3>Saldo en Atraso</h3>
                                <p>L <?php echo number_format($saldo['CapitalVencido'] + $saldo['InteresVencido'] + $saldo['Interes Moratorio'], 2); ?>
                                </p>
                            </div>
                            <div class="saldo-card">
                                <h3>Mora</h3>
                                <p>L <?php echo number_format($saldo['Interes Moratorio'], 2); ?></p>
                            </div>
                            <div class="saldo-card">
                                <h3>Días en Atraso</h3>
                                <p><?php echo $saldo['MaxDiasAtraso']; ?></p>
                            </div>
                            <div class="saldo-card">
                                <h3>Cuotas en Atraso</h3>
                                <p><?php echo $saldo['CuotasEnAtraso']; ?></p>
                            </div>
                            <div class="saldo-card">
                                <h3>Valor Cuota</h3>
                                <p>L <?php echo number_format($saldo['Cuota'], 2); ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </section>

                <h3>Comentario Permanente:</h3>

                <textarea class="comentarioPermanente" id="comentarioPermanente" name="comentarioPermanente"><?= htmlspecialchars($comentarioPermanente->comentario ?? '') ?>
        </textarea>


                <!-- Pestañas -->
                <div class="tabs">
                    <button class="tab-button active" data-tab="gestionar">Gestionar</button>
                    <button class="tab-button" data-tab="historial-gestiones">Historial de Gestiones</button>
                    <button class="tab-button" data-tab="historial-pagos">Historial de Pagos</button>
                    <button class="tab-button" data-tab="historial-promesas">Historial Promesas de Pago</button>
                    <button class="tab-button" data-tab="registrar-visita">Registrar Visita</button>
                    <button class="tab-button" data-tab="historial-visitas">Historial Visitas</button>
                </div>

                <!-- Contenido de cada pestaña -->
                <div class="tab-content active" id="gestionar">
                    <h2>Gestionar Cliente</h2>
                    <form id="form-gestion">
                        <input type="hidden" name="prenumero"
                            value="<?= htmlspecialchars($prenumero ?? ($_GET['prenumero'] ?? '')) ?>">

                        <div class="contenido-detalle">

                            <div class="campo">
                                <label for="codigoResultado">Código de Resultado</label>
                                <select name="codigoResultado" id="codigoResultado" required>
                                    <option value="" disabled selected>--Seleccione--</option>

                                    <?php foreach ($codigosResultado as $codigo) { ?>
                                        <option value="<?= $codigo->codigo ?>"><?= $codigo->codigo ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="campo">
                                <label for="">Fecha de revision</label>
                                <input type="date" name="fechaRevision" min="<?= date('Y-m-d'); ?>" required>
                            </div>

                            <div class="campo">
                                <label for="">Fecha de Promesa</label>
                                <input type="date" name="fechaPromesa" id="fechaPromesa" min="<?= date('Y-m-d'); ?>"
                                    disabled required>
                            </div>
                            <div class="campo">
                                <label for="">Monto Promesa</label>
                                <input type="number" placeholder="ejem: L3000" id="montoPromesa" name="montoPromesa"
                                    disabled required>
                            </div>
                            <div class="campo">
                                <label for="">Numero Contactado</label>
                                <input type="number" placeholder="ejem: 89893212" name="numeroContactado" required>
                            </div>

                        </div>
                        <div class="campo">
                            <label class="comentario-gestion" for="comentarioGestion">Comentario Gestion</label>
                            <textarea name="comentarioGestion" id="comentarioGestion"
                                placeholder="ejem: 31039303 AP L1200 23/12 Tienda de Yoro 12:00PM --WD"
                                required></textarea>
                        </div>
                        <input class="boton-submit" type="submit" id="guardar-gestion" value="Guardar">
                    </form>
                </div>


                <div class="tab-content" id="historial-gestiones">
                    <h2>Historial de Gestiones</h2>
                    <div class="scrollable-gestiones">

                        <div class="historial-gestion-cards">
                            <?php if (!empty($historialGestiones) && is_iterable($historialGestiones)) { ?>
                                <?php foreach ($historialGestiones as $gestion): ?>
                                    <div class="gestion-card">
                                        <div class="encabezado-gestion">
                                            <span
                                                class="codigo-resultado"><?= htmlspecialchars($gestion->codigo_resultado); ?></span>
                                            <span class="fecha-hora"><?= htmlspecialchars($gestion->fecha_creacion); ?></span>
                                        </div>
                                        <p class="comentario"><?= htmlspecialchars($gestion->comentario); ?></p>
                                        <p class="numero-contactado">Número Contactado:
                                            <?= htmlspecialchars($gestion->numero_contactado); ?>
                                        </p>
                                        <div class="detalles-secundarios">
                                            <p><strong>Fecha de Revisión:</strong>
                                                <?= htmlspecialchars($gestion->fecha_revision); ?>
                                            </p>

                                            <!-- // Evaluar si el código de la gestión es positivo usando el arreglo de códigos positivos -->
                                            <?php if (in_array($gestion->codigo_resultado, $codigosPositivosArray)): ?>
                                                <p><strong>Fecha de Promesa:</strong>
                                                    <?= htmlspecialchars($gestion->fecha_promesa); ?></p>
                                            <?php endif; ?>

                                            <p class="nombre-gestor">Creado por: <?= htmlspecialchars($gestion->creado_por); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php } else { ?>
                            <p>No hay gestiones registradas para este préstamo.</p>
                        <?php } ?>
                    </div>
                </div>

                <div class="tab-content" id="historial-pagos">
                    <h2>Historial de pagos</h2>
                    <!-- Aquí puedes añadir el contenido específico del historial de pagos -->
                    <div class="tabla-scroll">
                        <table class="tabla-historial-pagos">
                            <thead>
                                <tr>
                                    <th>Fecha de Pago</th>
                                    <th>Aplicado por</th>
                                    <th>Total Pagado</th>
                                    <th>Cod Sucursal</th>
                                    <th>Caja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Ejemplo de una fila, puedes llenar con datos del historial de pagos de cada cliente -->
                                <?php foreach ($pagosClientes as $pagoCliente) { ?>
                                    <tr>
                                        <td><?php echo $pagoCliente['Fecha'] ?></td>
                                        <td><?php echo $pagoCliente['Aplicado por'] ?></td>
                                        <td>L<?php echo number_format($pagoCliente['Total Pagado'], 2) ?></td>
                                        <td><?php echo $pagoCliente['Cod Sucursal'] ?></td>
                                        <td><?php echo $pagoCliente['Tienda'] ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-content" id="historial-promesas">
                    <?php if (!empty($promesas)) { ?>

                        <h2>Promesas del Préstamo</h2>

                        <?php $totalPromesasIncumplidas = count(array_filter($promesas, function ($promesa) {
                            return $promesa['estado_promesa'] === 'INCUMPLIDA';
                        })); ?>

                        <h4>Total de promesas incumplidas: <?= $totalPromesasIncumplidas; ?> </h4>
                        <div class="tabla-scroll">
                            <table class="tabla-historial-promesas">
                                <thead>
                                    <tr>
                                        <th>Numero Contactado</th>
                                        <th>Fecha Gestion</th>
                                        <th>Fecha Promesa</th>
                                        <th>Cod Resultado</th>
                                        <th>Monto Promesa</th>
                                        <th>Estado</th>
                                        <th>Gestionado por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($promesas as $promesa) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($promesa['numero_contactado']); ?></td>
                                            <td><?php echo date('Y-m-d', strtotime($promesa['fecha_creacion'])); ?></td>
                                            <td><?php echo htmlspecialchars($promesa['fecha_promesa']); ?></td>
                                            <td><?php echo htmlspecialchars($promesa['codigo_resultado']); ?></td>
                                            <td><?php echo htmlspecialchars($promesa['montoPromesa']); ?></td>
                                            <td><?php echo htmlspecialchars($promesa['estado_promesa']); ?></td>
                                            <td><?php echo htmlspecialchars($promesa['creado_por']); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <p>No hay promesas registradas para este préstamo.</p>
                    <?php } ?>
                </div>

                <?php include __DIR__ . '/secciones/visitas_domiciliares.php'; ?>
    </div>


</div>

<script src="<?= BASE_URL ?>/build/js/tabs.js"></script>
<script>

    const codigosPositivos = <?= json_encode($codigosPositivosArray) ?>;
    function enviarGestion() {
        const form = document.getElementById('form-gestion');
        const data = new FormData(form);

        const cp = document.getElementById('comentarioPermanente');
        if (cp) data.set('comentarioPermanente', cp.value); // <- clave

        Swal.fire({
            title: "Guardando gestión...",
            text: "Por favor espera mientras procesamos los datos.",
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading(),
        });

        fetch("<?= BASE_URL ?>/prestamos/detalle",{
            method: "POST",
            body: data
        })
            .then(async res => {
                const ct = res.headers.get('content-type') || '';
                if (!ct.includes('application/json')) throw new Error(await res.text());
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(response => {
                if (response.status !== 'success') throw new Error(response.message || 'No se pudo guardar');

                Swal.fire({
                    icon: "success",
                    title: "Gestión guardada exitosamente",
                    showConfirmButton: false,
                    timer: 1500
                });

                // Render historial
                const cont = document.querySelector('.historial-gestion-cards');
                cont.innerHTML = '';
                response.historialGestiones.forEach(g => {
                    let html = `
          <div class="gestion-card">
            <div class="encabezado-gestion">
              <span class="codigo-resultado">${g.codigo_resultado ?? ''}</span>
              <span class="fecha-hora">${g.fecha_creacion ?? ''}</span>
            </div>
            <p class="comentario">${g.comentario ?? ''}</p>
            <p class="numero-contactado">Número Contactado: ${g.numero_contactado ?? ''}</p>
            <div class="detalles-secundarios">
              <p><strong>Fecha de Revisión:</strong> ${g.fecha_revision ?? ''}</p>`;
                    if (codigosPositivos.includes(g.codigo_resultado)) {
                        html += `<p><strong>Fecha de Promesa:</strong> ${g.fecha_promesa ?? ''}</p>`;
                    }
                    html += `
              <p class="nombre-gestor">Creado por: ${g.creado_por ?? ''}</p>
            </div>
          </div>`;
                    cont.insertAdjacentHTML('beforeend', html);
                });

                // Comentario permanente
                const cp = document.getElementById('comentarioPermanente');
                if (cp && response.comentarioPermanente) {
                    cp.value = response.comentarioPermanente.comentario || '';
                }

                // Reset solo del form de gestión
                form.reset();
                document.getElementById('fechaPromesa').disabled = true;
                document.getElementById('montoPromesa').disabled = true;
            })
            .catch(err => {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Error', text: err.message });
            });
    }

    // Listener del form de gestión (solo ese)
    document.getElementById('form-gestion').addEventListener('submit', function (e) {
        e.preventDefault();
        enviarGestion();
    });

    // Habilitar/Deshabilitar campos por código
    document.getElementById("codigoResultado").addEventListener("change", function () {
        const fechaPromesa = document.getElementById("fechaPromesa");
        const montoPromesa = document.getElementById("montoPromesa");
        if (codigosPositivos.includes(this.value)) {
            fechaPromesa.disabled = false;
            montoPromesa.disabled = false;
        } else {
            fechaPromesa.value = "";
            montoPromesa.value = "";
            fechaPromesa.disabled = true;
            montoPromesa.disabled = true;
        }
    });

</script>