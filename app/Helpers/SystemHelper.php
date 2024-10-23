<?php

namespace App\Helpers;


class SystemHelper
{
    public static function getLogo()
    {
        // // $configuracion = Configuracion::first(); // Asume que solo hay un registro
        // // return $configuracion && $configuracion->logo ? '/storage/logo/' . $configuracion->logo : '/paper/img/logo-color1.png'; // Devuelve la URL del logo

        // $configuracion = Configuracion::first();
        // $logoPath = $configuracion && $configuracion->logo ? storage_path('app/public/logo/' . $configuracion->logo) : public_path('paper/img/logo-color1.png');
        
        // // Verifica si el archivo existe antes de convertirlo
        // if (file_exists($logoPath)) {
        //     $logoData = base64_encode(file_get_contents($logoPath));
        //     $mimeType = mime_content_type($logoPath);
        //     return 'data:' . $mimeType . ';base64,' . $logoData;
        // }

        return '/paper/img/logo-color.png'; // Manejar el caso de que no exista la imagen
    }
}