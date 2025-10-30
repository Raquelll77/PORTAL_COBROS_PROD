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
    $totalCancelar = $saldoDatos['TotalACancelar'] ?? 0;
} else {
    $totalCancelar = 0;
}
?>

<p>
    Por medio de la presente <strong>SKG S.A. DE C.V</strong> RTN <strong>05019018057713</strong> hace constar que el
    <strong>Sr. <?= htmlspecialchars($finiquito->CardName) ?></strong> con número de identidad
    <?= htmlspecialchars($finiquito->Identidad) ?>, tiene un saldo pendiente, que para su cancelación total al
    <?= date('d/m/Y') ?> es la cantidad de <strong>Lps <?= number_format($totalCancelar, 2) ?></strong>.
</p>


</p>

<br>

<p>Realizar depósito del cheque o Transferencia al banco:
    Banco Atlántida 2011-1018-444
</p>

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