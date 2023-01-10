<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController
{

     public static function login(Router $router)
     {
          $alertas = [];
          $auth = new Usuario;
          if ($_SERVER["REQUEST_METHOD"] === "POST") {
               $auth = new Usuario($_POST);
               $alertas = $auth->validarLogin();

               if (empty($alertas)) {
                    /* Comprobar que exista el usuario */
                    $usuario = Usuario::where("email", $auth->email);

                    if ($usuario) {
                         $usuario->comprobarPasswordAndVerificado($auth->password);

                         if ($usuario) {
                              if (!isset($_SESSION)) {
                                   session_start();
                              }

                              $_SESSION["id"] = $usuario->id;
                              $_SESSION["nombre"] = $usuario->nombre;
                              $_SESSION["apellido"] = $usuario->apellido;
                              $_SESSION["email"] = $usuario->email;
                              $_SESSION["login"] = true;

                              if ($usuario->admin) {
                                   $_SESSION["admin"] = $usuario->admin ?? null;
                                   header("Location: /admin");
                              } else {
                                   header("Location: /cita");
                              }
                         }
                    } else {
                         Usuario::setAlerta("error", "El usuario no existe");
                    }
               }
          }

          $alertas = Usuario::getAlertas();
          $router->render("auth/login", [
               "titulo" => "Login",
               "auth" => $auth,
               "alertas" => $alertas
          ]);
     }

     public static function logout(Router $router)
     {
          session_start();
          $_SESSION = [];

          header("Location: /");
     }

     public static function olvide(Router $router)
     {
          $alertas = [];

          if ($_SERVER["REQUEST_METHOD"] === "POST") {
               $auth =  new Usuario($_POST);
               $alertas = $auth->validarEmail();

               if (empty($alertas)) {
                    /* Verificar que el email exista */
                    $usuario = Usuario::where("email", $auth->email);

                    if ($usuario && $usuario->confirmado === "1") {
                         $usuario->crearToken();
                         $usuario->guardar();

                         $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                         $email->enviarInstrucciones();

                         Usuario::setAlerta("exito", "Revisa tu email");
                    } else {
                         Usuario::setAlerta("error", "El usuario no existe o no está confirmado");
                    }
               }
          }

          $alertas = Usuario::getAlertas();
          $router->render("auth/olvide", [
               "titulo" => "Recuperar Contraseña",
               "alertas" => $alertas
          ]);
     }

     public static function recuperar(Router $router)
     {
          $alertas = [];

          $token = s($_GET["token"]);

          $usuario = Usuario::where("token", $token);

          if (empty($usuario)) {
               header("Location: /");
          }

          if ($_SERVER["REQUEST_METHOD"] === "POST") {
               $password = new Usuario($_POST);
               $alertas = $password->validarPassword();

               if (empty($alertas)) {
                    $usuario->password = null;
                    $usuario->password = $password->password;
                    $usuario->hashPassword();
                    $usuario->token = null;
                    $resultado = $usuario->guardar();

                    if ($resultado) {
                         header("Location: /");
                    }
               }
          }

          $alertas = Usuario::getAlertas();

          $router->render("auth/recuperar", [
               "titulo" => "Reestabler Contraseña",
               "alertas" => $alertas
          ]);
     }

     public static function crear(Router $router)
     {
          $usuario = new Usuario;
          $alertas = [];

          if ($_SERVER["REQUEST_METHOD"] === "POST") {
               $usuario->sincronizar($_POST);
               $alertas = $usuario->validarNuevaCuenta();

               /* Validar el arreglo de alertas */
               if (empty($alertas)) {
                    /* Verficar que el usuario no este registrado */
                    $resultado = $usuario->existeUsuario();

                    if ($resultado->num_rows) {
                         $alertas = Usuario::getAlertas();
                    } else {
                         /* Hashear el psasword */
                         $usuario->hashPassword();

                         /* Crear tokén */
                         $usuario->crearToken();

                         /* Enviar email para confirmar cuenta */
                         $email = new Email($usuario->email, $usuario->nombre,  $usuario->token);
                         $email->enviarConfirmacion();

                         /* Crear el usuario */
                         $resultado = $usuario->guardar();
                         if ($resultado) {
                              header("Location: /mensaje");
                         }
                    }
               }
          }


          $router->render("auth/crear-cuenta", [
               "titulo" => "Crear Cuenta",
               "usuario" => $usuario,
               "alertas" => $alertas
          ]);
     }

     public static function mensaje(Router $router)
     {
          $router->render("auth/mensaje", [
               "titulo" => "Confirma tu cuenta"
          ]);
     }

     public static function confirmar(Router $router)
     {
          $alertas = [];

          $token = s($_GET["token"]);

          $usuario = Usuario::where("token", $token);

          if (empty($usuario)) {
               /* Mostrar mensaje de error */
               header("Location: /");
          } else {
               /* Modificar a usuario confirmado */
               $usuario->confirmado = 1;
               $usuario->token = "";
               $usuario->guardar();
               Usuario::setAlerta("exito", "Cuenta confirmada correctamente");
          }

          $alertas = Usuario::getAlertas();
          $router->render("auth/confirmar-cuenta", [
               "titulo" => "Confirmar Cuenta",
               "alertas" => $alertas
          ]);
     }
}
