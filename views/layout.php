<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistema de Cobros | <?= $titulo ?? '' ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=Open+Sans&display=swap"
        rel="stylesheet">

    <!-- CSS del proyecto -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/build/css/app.css">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= BASE_URL ?>/build/img/logoskg.jpg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= BASE_URL ?>/build/img/logoskg.jpg">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= BASE_URL ?>/build/img/logoskg.jpg">



    <!-- Librerías externas -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.min.css">
</head>

<body>
    <?php $hideChrome = $hideChrome ?? false;

    if (!$hideChrome) { ?>
        <div id="loftloader-wrapper">
            <div class="loader-content">
                <img src="<?= BASE_URL ?>/build/img/logoskg-transparente.png" alt="Logo SKG"
                    style="width:200px;height:auto;" />
                <div class="spinner"></div>
            </div>
        </div>
    <?php } ?>

    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // viene del render del controlador
    ?>

    <?php if (!$hideChrome): ?>
        <?php include __DIR__ . '/principal/header-dashboard.php'; ?>
    <?php endif; ?>

    <?= $contenido ?? '' ?>
    <?= $script ?? '' ?>

    <!-- JS externos -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.9.3/semantic.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <!-- Loader personalizado con logo -->

    <style>
        body {
            overflow: hidden;
            /* evita scroll mientras carga */
        }

        /* Overlay que cubre toda la pantalla */
        #loftloader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(4px);
        }

        #loftloader-wrapper .loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Estilo del logo */
        #loftloader-wrapper img {
            width: 200px;
            height: auto;
            animation: pulse 1.5s infinite;
            opacity: 0.7;
            /* 70% de opacidad */
        }


        /* Animación de “latido” */
        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #333;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            margin-top: 15px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>


    <script>
        // Inicialización global segura
        $(function () {
            $('.ui.dropdown').dropdown();
            $('.ui.checkbox').checkbox();
        });
    </script>

    <?php if (!$hideChrome): ?>
        <?php include __DIR__ . '/principal/footer-dashboard.php'; ?>
    <?php endif; ?>

    <script>
        // Ocultar loader cuando la página haya cargado
        $(window).on('load pageshow', function (e) {
            if (e.persisted) {
                location.reload();
            } else {
                $("#loftloader-wrapper").fadeOut(800, function () {
                    $("body").css("overflow", "auto"); // vuelve a habilitar scroll
                });
            }
        });


        // Mostrar loader antes de salir/navegar
        $(window).on('beforeunload pagehide', function (e) {
            if (e.persisted) {
                location.reload();
            } else {
                $("#loftloader-wrapper").show();
                $("body").css("overflow", "hidden");
            }
        });

    </script>


</body>

</html>