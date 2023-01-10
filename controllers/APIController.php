<?php

namespace Controllers;

use Model\Cita;
use Model\CitaServicio;
use Model\Servicio;

class APIController
{

    public static function index()
    {
        $servicios = Servicio::all();
        echo json_encode($servicios);
    }

    public static function crear()
    {
        /* Almacena la ciat */
        $cita = new Cita($_POST);
        $resultado = $cita->guardar();
        $idCita = $resultado["id"];

        /* Almacena los servicios con el id de la cita */
        $idServicios = explode(",", $_POST["servicios"]);
        foreach ($idServicios as $idServicio) {
            $args = [
                "citaId" => $idCita,
                "servicioId" => $idServicio
            ];

            $citaServicio  = new CitaServicio($args);
            $citaServicio->guardar();
        }

        /* Retornamos una respuesta */
        echo json_encode(["resultado" => $resultado]);
    }

    public static function eliminar()
    {

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $id = $_POST["id"];
            if (!is_numeric($id)) {
                return;
            }
            $cita = Cita::find($id);
            $cita->eliminar();
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }
    }
}
