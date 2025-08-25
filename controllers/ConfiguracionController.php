<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;
use Model\ActiveRecord;
use Clases\Upload;

class ConfiguracionController
{
    public static function subir_creditos(Router $router)
    {
        isAuth();

        // Configurar tiempo de ejecución ilimitado
        set_time_limit(0);
        ini_set('max_execution_time', 0);

        $message = null;
        $status = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Verificar archivo cargado
                if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                    throw new \Exception('No se seleccionó ningún archivo válido.');
                }

                // Configurar conexión a la base de datos
                ActiveRecord::useSQLSrv();
                $dbConnection = ActiveRecord::getActiveDB();

                // Procesar el archivo
                $uploader = new Upload($_FILES['file'], $dbConnection);
                $result = $uploader->processUpload();

                // Mensaje de éxito
                $message = "Archivo procesado exitosamente. Filas afectadas: $result.";
                $status = 'success';
            } catch (\Exception $e) {
                // Mensaje de error
                $message = htmlspecialchars($e->getMessage());
                $status = 'error';
            }
        }

        // Renderizar vista
        $router->render('configuracion/subir_creditos_xgestor', [
            'titulo' => 'Subir Creditos',
            'message' => $message,
            'status' => $status,
        ]);
    }

    public static function index(Router $router)
    {
        isAuth();
        $router->render('configuracion/index', [
            'titulo' => 'Menu Configuracion'

        ]);
    }

    public static function usuarios(Router $router)
    {
        $usuarios = Usuario::all();

        isAuth();
        $router->render('configuracion/usuarios', [
            'titulo' => 'Usuarios',
            'usuarios' => $usuarios
        ]);
    }
    public static function usuariosGuardar()
    {
        isAuth();
        header('Content-Type: application/json; charset=utf-8');

        // Usuario::useSQLSrv(); // Descomenta si esta tabla vive en SQL Server y no lo fijaste antes.

        $id = trim($_POST['id'] ?? '');
        $usuario = trim($_POST['usuario'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $rol = trim($_POST['rol'] ?? '');
        $estado = (int) ($_POST['estado'] ?? 0);
        $estado = $estado ? 1 : 0;

        // Validaciones básicas
        if ($usuario === '' || $nombre === '' || $rol === '') {
            echo json_encode(['ok' => false, 'message' => 'Faltan campos requeridos.']);
            return;
        }

        try {
            if ($id === '') {
                // CREAR
                $yaExiste = Usuario::where('usuario', $usuario);
                if ($yaExiste) {
                    echo json_encode(['ok' => false, 'message' => 'El usuario ya existe.']);
                    return;
                }

                if ($password === '') {
                    echo json_encode(['ok' => false, 'message' => 'La contraseña es requerida para crear el usuario.']);
                    return;
                }

                $u = new Usuario([
                    'nombre' => $nombre,
                    'usuario' => $usuario,
                    'password' => $password,   // SIN hash por decisión actual
                    'rol' => $rol,
                    'estado' => $estado,
                ]);

                if (!$u->guardar()) {
                    echo json_encode(['ok' => false, 'message' => 'No se pudo crear el usuario.']);
                    return;
                }

                echo json_encode([
                    'ok' => true,
                    'message' => 'Usuario creado.',
                    'data' => [
                        'id' => (int) $u->id,
                        'usuario' => $u->usuario,
                        'nombre' => $u->nombre,
                        'rol' => $u->rol,
                        'estado' => (int) $u->estado,
                    ]
                ]);
                return;
            }

            // ACTUALIZAR
            $u = Usuario::find((int) $id);
            if (!$u) {
                echo json_encode(['ok' => false, 'message' => 'Usuario no encontrado.']);
                return;
            }

            // Si el username cambia, validar duplicado
            if (strcasecmp($u->usuario, $usuario) !== 0) {
                $dup = Usuario::where('usuario', $usuario);
                if ($dup) {
                    echo json_encode(['ok' => false, 'message' => 'El usuario ya existe.']);
                    return;
                }
            }

            $u->nombre = $nombre;
            $u->usuario = $usuario;
            // NO tocar $u->password en actualización (se conserva)
            $u->rol = $rol;
            $u->estado = $estado;

            if (!$u->guardar()) {
                echo json_encode(['ok' => false, 'message' => 'No se pudo actualizar el usuario.']);
                return;
            }

            echo json_encode([
                'ok' => true,
                'message' => 'Usuario actualizado.',
                'data' => [
                    'id' => (int) $u->id,
                    'usuario' => $u->usuario,
                    'nombre' => $u->nombre,
                    'rol' => $u->rol,
                    'estado' => (int) $u->estado,
                ]
            ]);
        } catch (\Throwable $e) {
            echo json_encode(['ok' => false, 'message' => 'Error en servidor: ' . $e->getMessage()]);
        }
    }

    /**
     * GET /configuracion/habilitar_usuario?id=123
     * Cambia estado a 1 y redirige al listado.
     */
    public static function usuariosHabilitar()
    {
        // isAuth(); y verificación de rol si aplica
        $id = (int) ($_REQUEST['id'] ?? 0);
        $isAjax = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        // Usuario::useSQLSrv(); // si aplica
        $u = $id ? Usuario::find($id) : null;
        if ($u) {
            $u->estado = 1;
            $u->guardar();
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true, 'estado' => 1, 'message' => 'Usuario habilitado']);
            exit;
        }
        header('Location: ' . BASE_URL . '/configuracion/usuarios');
        exit;
    }

    public static function usuariosInhabilitar()
    {
        $id = (int) ($_REQUEST['id'] ?? 0);
        $isAjax = (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest');

        $u = $id ? Usuario::find($id) : null;
        if ($u) {
            $u->estado = 0;
            $u->guardar();
        }

        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true, 'estado' => 0, 'message' => 'Usuario inhabilitado']);
            exit;
        }
        header('Location: ' . BASE_URL . '/configuracion/usuarios');
        exit;
    }
}

