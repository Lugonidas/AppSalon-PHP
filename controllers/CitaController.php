<?php

namespace Controllers;

use MVC\Router;

class CitaController
{

    public static function index(Router $router)
    {
        isAuth();

        $router->render("cita/index", [
            "titulo" => "Crear Cita",
            "nombre" => $_SESSION["nombre"],
            "id" => $_SESSION["id"]
        ]);
    }
}
