<?php
/** @var string $pre */
/** @var array  $infoCliente  // array de filas; usa $infoCliente[0] si existe */
/** @var array  $saldoPagoHoy // array de filas; usa $saldoPagoHoy[0] si existe */
/** @var array  $movimientos  // array de filas con detalle */
?>

<div class="ui segment active">

    <div class="ui clearing top attached segment active">
        <form action="">
            <button id="generate-pdf" type="button" class="ui teal button right floated">
                <i class="file pdf outline icon"></i> Descargar PDF
            </button>
        </form>

        <div id="estado-cuenta" class="">
            <h2 class="ui header">
                <div class="content">
                    <h2>SKG</h2>
                    <div class="ui sub header">ESTADO DE CUENTA DE CRÉDITO AL <?= date("d/m/Y") ?></div>

                </div>
            </h2>

            <div class="ui grid">
                <div class="eight wide column">
                    <div class="ui segment">
                        <h3>Datos Generales</h3>
                        <?php if (!empty($infoCliente)):
                            $c = $infoCliente[0]; ?>
                            <h5><strong>Cod.Cliente:</strong> <?= htmlspecialchars($c['PRECLICOD'] ?? '') ?></h5>
                            <h5><strong>No. Cuenta:</strong> <?= htmlspecialchars($c['PRENUMERO'] ?? $pre) ?></h5>
                            <h5><strong>Nombre Cliente:</strong> <?= htmlspecialchars($c['PRENOMBRE'] ?? '') ?></h5>
                            <h5><strong>Monto Original:</strong> L <?= number_format((float) ($c['PreMonTotal'] ?? 0), 2) ?>
                            </h5>
                            <h5><strong>Saldo Capital:</strong> L
                                <?= number_format((float) ($c['PreSalCapital'] ?? 0), 2) ?>
                            </h5>
                        <?php else: ?>
                            <em>No hay información del cliente.</em>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="eight wide column">
                    <div class="ui segment">
                        <h3>Total a Pagar a la Fecha</h3>
                        <?php if (!empty($saldoPagoHoy)):
                            $s = $saldoPagoHoy[0]; ?>
                            <h5><strong>Mora:</strong> L <?= number_format((float) ($s['Interes Moratorio'] ?? 0), 2) ?>
                                </h3>
                                <h5><strong>Capital Atrasado:</strong> L
                                    <?= number_format((float) ($s['CapitalVencido'] ?? 0), 2) ?>
                                </h5>
                                <h5><strong>Interés Vencido:</strong> L
                                    <?= number_format((float) ($s['InteresVencido'] ?? 0), 2) ?>
                                </h5>
                                <h5><strong>Interés Vigente:</strong> L
                                    <?= number_format((float) ($s['Interes Vigente'] ?? 0), 2) ?>
                                </h5>
                                <h5><strong>Capital Vigente:</strong> L
                                    <?= number_format((float) ($s['Capital Vigente'] ?? 0), 2) ?>
                                </h5>
                                <hr>
                                <?php
                                $atraso = (float) ($s['CapitalVencido'] ?? 0) + (float) ($s['InteresVencido'] ?? 0) + (float) ($s['Interes Moratorio'] ?? 0);
                                $totalDia = (float) ($s['TotalFecha'] ?? 0);
                                ?>
                                <strong>Atraso:</strong>
                                <span style="font-weight:700; font-size:1.1em;">L <?= number_format($atraso, 2) ?></span>
                                <strong> — Total al Día:</strong>
                                <span style="font-weight:700; font-size:1.1em;">L <?= number_format($totalDia, 2) ?></span>
                            <?php else: ?>
                                <em>No hay totales al día.</em>
                            <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="ui long container fluid p-0 mt-3">
                <h5>Detalles del Préstamo</h5>
                <table class="ui tiny table celled unstackable striped">
                    <thead>
                        <tr>
                            <?php if (!empty($movimientos)):
                                foreach (array_keys($movimientos[0]) as $col): ?>
                                    <th><?= htmlspecialchars($col) ?></th>
                                <?php endforeach; else: ?>
                                <th>No se encontraron resultados</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($movimientos)): ?>
                            <?php foreach ($movimientos as $row): ?>
                                <?php
                                // Si el SP entrega "Dias Atraso"/"Días Atraso", marca la fila
                                $diasKey = array_key_exists('Dias Atraso', $row) ? 'Dias Atraso' :
                                    (array_key_exists('Días Atraso', $row) ? 'Días Atraso' : null);
                                $rowClass = ($diasKey && (int) $row[$diasKey] > 0) ? 'negative' : '';
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <?php foreach ($row as $val): ?>
                                        <?php
                                        if (is_array($val))
                                            $val = implode(', ', $val);
                                        $cell = is_numeric($val) ? number_format((float) $val, 2) : (string) $val;
                                        // recortar ISO date a YYYY-MM-DD si viene con tiempo
                                        if (preg_match('/^\d{4}-\d{2}-\d{2}T?\s?\d{0,2}:?\d{0,2}:?\d{0,2}/', $cell)) {
                                            $cell = substr($cell, 0, 10);
                                        }
                                        ?>
                                        <td><?= htmlspecialchars($cell) ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td>No se encontraron detalles del préstamo.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- estilos de impresión y cortes de página para pdf -->
<style>
    @media print {
        * {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }

    thead {
        display: table-header-group;
    }

    tfoot {
        display: table-footer-group;
    }

    .page-break {
        page-break-before: always;
        break-before: page;
    }
</style>

<!-- html2pdf (solo para el botón PDF) -->
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<script>

    document.getElementById('generate-pdf').addEventListener('click', function () {


        const element = document.getElementById('estado-cuenta');
        const urlParams = new URLSearchParams(window.location.search);
        const Prestamo = urlParams.get('prenumero');
        if (element) {
            const options = {
                margin: 1,
                filename: `EstadoCuenta_Prestamo_${Prestamo}.pdf`,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'cm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };
            html2pdf().set(options).from(element).save();
            console.log("PDF generado.");
        } else {
            console.log("No se encontró el elemento a convertir en PDF.");
        }
    });
</script>