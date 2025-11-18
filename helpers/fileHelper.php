<?php
// es un metodo estatico que encapsula una accion de mover un archivo
class FileHelper
{
    public static function fileHandler($origin, $destination)
    {
        if (file_exists($origin)) {
            if (move_uploaded_file($origin, $destination)) {
                return true;
            }
        }
        return false;
    }
}
