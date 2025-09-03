<?php
namespace Controllers;

use Model\Usuario;
use MVC\Router;

class LoginController
{

    public static function login(Router $router)
    {
        Usuario::useSQLSrv(); // si estás en SQL Server
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarLogin();

            if (empty($alertas)) {
                // Buscar usuario
                $usuario = Usuario::where('usuario', $auth->usuario);

                if (!$usuario) {
                    Usuario::setAlerta('error', 'El usuario no existe');
                } else {
                    // Verificar si está activo
                    if ((int) $usuario->estado !== 1) {
                        Usuario::setAlerta('error', 'Usuario inactivo. Contacte a un administrador.');
                    } else {
                        // Comparar contraseña (plain por ahora)
                        if (($_POST['password'] ?? '') === (string) $usuario->password) {
                            session_start();
                            $_SESSION['PORTAL_COBROS'] = [];
                            $_SESSION['PORTAL_COBROS']['id'] = $usuario->id;
                            $_SESSION['PORTAL_COBROS']['nombre'] = $usuario->nombre;
                            $_SESSION['PORTAL_COBROS']['usuario'] = $usuario->usuario;
                            $_SESSION['PORTAL_COBROS']['rol'] = $usuario->rol;
                            $_SESSION['PORTAL_COBROS']['login'] = true;

                            if ($usuario->rol === 'TELECOBRO') {
                                header('Location: ' . BASE_URL . '/cobros');
                                exit;
                            }

                            header('Location: ' . BASE_URL . '/principal');
                            exit;
                        } else {
                            Usuario::setAlerta('error', 'Password incorrecto');
                        }
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesion',
            'alertas' => $alertas,
            'hideChrome' => true
        ]);
    }


    public static function logout()
    {
        // Cerrar la sesión
        session_start();
        $_SESSION['PORTAL_COBROS'] = [];
        header('Location: ' . BASE_URL . '/');
    }
}
