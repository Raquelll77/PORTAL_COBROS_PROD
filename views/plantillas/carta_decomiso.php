<style>
    @page {
        margin: 0;
    }

    body {
        font-family: Arial, sans-serif;
        padding: 3cm;
        background-image: url("<?= $fondo ?>");
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }

    h1 {
        text-decoration: underline;
        font-size: 1rem;
    }

    p {
        text-align: justify;
        font-size: 14px;
    }

    .info {
        margin-left: 1cm;
        font-weight: bold;
    }
</style>

<?php
$meses = [
    'January' => 'enero',
    'February' => 'febrero',
    'March' => 'marzo',
    'April' => 'abril',
    'May' => 'mayo',
    'June' => 'junio',
    'July' => 'julio',
    'August' => 'agosto',
    'September' => 'septiembre',
    'October' => 'octubre',
    'November' => 'noviembre',
    'December' => 'diciembre'
];

$fechaActual = date('d') . ' de ' . $meses[date('F')] . ' de ' . date('Y');
?>

<div class="contenido">
    <div class="encabezado">
        <p><strong>San Pedro Sula, <?= $fechaActual ?></strong></p>
    </div>

    <br><br>

    <h1>AUTORIZACIÓN DE DECOMISO</h1>

    <div class="datos">
        <p><strong>Número de préstamo:</strong> <?= htmlspecialchars($prenumero) ?></p>
        <br><br>
        <h1><strong>Sr(a).</strong> <?= htmlspecialchars($finiquito->CardName ?? '') ?>:</h1>
    </div>

    <p>
        Por el incumplimiento repetitivo en sus obligaciones de pago, y en vista de que su cuenta tiene más de
        60 días en mora, se le solicita hacer entrega al Sr.
        <strong><u><?= htmlspecialchars($gestor ?: '________________') ?></u></strong>,
        quien va en representación de <strong>SKG S.A. de C.V.</strong>, del vehículo con las siguientes
        características:
    </p>

    <div class="info">
        <p>Serie: <?= htmlspecialchars($finiquito->Serie ?? '') ?></p>
        <p>Motor: <?= htmlspecialchars($finiquito->Motor ?? '') ?></p>
        <p>Marca: <?= htmlspecialchars($finiquito->Marca ?? '') ?></p>
        <p>Modelo: <?= htmlspecialchars($finiquito->Modelo ?? '') ?></p>
        <p>Color: <?= htmlspecialchars($finiquito->Color ?? '') ?></p>
        <p>Año: <?= htmlspecialchars($finiquito->Año ?? '') ?></p>
        <p>Cilindraje: <?= htmlspecialchars($finiquito->Cilindraje ?? '') ?></p>
    </div>

    <p>
        En virtud del incumplimiento de pago y con base en el <strong>artículo K</strong> del contrato de crédito,
        debido a los múltiples requerimientos para poner la cuenta al día sin obtener respuesta favorable,
        nos vemos en la penosa obligación de proceder con el retiro de la unidad.
    </p>
</div>