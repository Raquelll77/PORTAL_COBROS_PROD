<?php
// $finiquito viene del controlador
?>
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
        text-align: center;
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

<h1>Constancia</h1>
<br>
<br>
<?php

if (is_array($saldo) && !empty($saldo)) {
    $saldoDatos = $saldo[0];
    // $totalCancelar = $saldoDatos['TotalACancelar'] ?? 0;
    $saldoCapital = $saldoDatos['Saldo Capital'] ?? 0;
} else {
    //   $totalCancelar = 0;
    $saldoCapital = 0;
}
?>

<p>
    MOVESA S.A. por medio de la presente <strong>HACEMOS CONSTAR QUE:</strong>
    El/La <strong>Sr(a). <?= htmlspecialchars($finiquito->CardName) ?></strong> con número de identidad
    <?= htmlspecialchars($finiquito->Identidad) ?>, mantiene un crédito con nosotros que se facturo el
    <?= date('d/m/Y', strtotime($finiquito->FechaFactura)) ?>, con un
    capital pendiente por pagar de <strong>Lps <?= number_format($saldoCapital, 2) ?></strong> con las siguientes
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


<br>

<p>Esta información es brindada de carácter estrictamente confidencial y sin ningún tipo de responsabilidad de nuestra
    parte.</p>

<br>
<?php
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain');
?>
<p>
    Y Para los fines que al interesado convengan, se extiende la presente en la
    ciudad de San Pedro Sula, a los
    <?= date('d') ?>
    días del mes de
    <?= strftime('%B') ?>
    del año
    <?= date('Y') ?>.
</p>