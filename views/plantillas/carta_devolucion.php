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

    h3 {

        text-decoration: underline;
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

    <h3>Estimados Sres. SKG S.A DE C.V</h3>

    <p>
        Por medio de la presente yo; <strong> <?= $finiquito->CardName ?></strong>, con número de identidad #
        <?= $finiquito->Identidad ?> les devuelvo ya que no puedo seguir pagando por problemas personales, renuncio a
        todo derecho para
        que ustedes puedan disponer de ella a la mayor brevedad posible dicha motocicleta cuenta con las siguientes
        características
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
        <strong>De antemano agradecemos su atención y colaboración</strong>
    </p>
    <br><br>

    <div style="text-align: center; margin-top: 60px;">
        <div style="border-top: 1px solid #000; width: 60%; margin: 0 auto;"></div>
        <p style="font-weight: bold; margin-top: 5px; text-align: center;">
            <?= strtoupper(htmlspecialchars($finiquito->CardName ?? '')) ?>
        </p>
    </div>


    <br><br>

    <p><strong>Recibido por:</strong></p>
</div>