<?php

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController
{
    public static function index(Router $router)
    {
        isAdmin();
        $servicios = Servicio::all();
        $router->render("servicios/index", [
            "titulo" => "Servicios",
            "nombre" => $_SESSION["nombre"],
            "servicios" => $servicios
        ]);
    }

    public static function crear(Router $router)
    {
        isAdmin();

        $alertas = [];
        $servicio = new Servicio;

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $servicio->sincronizar($_POST);
            $alertas = $servicio->validar();

            if (empty($alertas)) {
                $servicio->guardar();
                header("Location: /servicios");
            }
        }

        $router->render("servicios/crear", [
            "titulo" => "Crear Servicio",
            "nombre" => $_SESSION["nombre"],
            "servicio" => $servicio,
            "alertas" => $alertas
        ]);
    }

    public static function actualizar(Router $router)
    {
        isAdmin();

        if (!is_numeric($_GET["id"])) {
            return;
        }
        $servicio = Servicio::find($_GET["id"]);
        $alertas = [];
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $servicio->sincronizar($_POST);

            $alertas = $servicio->validar();

            if (empty($alertas)) {
                $servicio->guardar();
                header("Location: /servicios");
            }
        }

        $router->render("servicios/actualizar", [
            "titulo" => "Actualizar Servicio",
            "nombre" => $_SESSION["nombre"],
            "alertas" => $alertas,
            "servicio" => $servicio
        ]);
    }

    public static function eliminar()
    {
        isAdmin();

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            
            if (!is_numeric($_POST["id"])) {
                return;
            }
           
            $id = $_POST["id"];

            $servicio = Servicio::find($id);
            $servicio->eliminar();
            header("Location: /servicios");
        }
    }
}
