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

<h1>FINIQUITO</h1>
<br>
<br>
<p>
  <b>SKG S.A. de C.V.</b>, por este medio hace constar que el cliente
  <b><?= htmlspecialchars($finiquito->CardName ?? '') ?></b>, con número de
  identidad <b><?= htmlspecialchars($finiquito->Identidad ?? '') ?></b>, no tiene
  deuda pendiente en relación al número de factura
  <b><?= htmlspecialchars($finiquito->NumeroFactura ?? '') ?></b>, por la compra de
  un vehículo con las siguientes características:
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

<?php
setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain');
?>
<p>
  Para los fines que al interesado convengan, se extiende la presente en la
  ciudad de San Pedro Sula, a los
  <?= date('d') ?>
  días del mes de
  <?= strftime('%B') ?>
  del año
  <?= date('Y') ?>.
</p>