<?php
/**
 * Clase auxiliar que gestiona sesiones.
 */
class Sesion{
    /**
     * Comprueba si existe una sesión válida, en caso contrario se debería redirigir a pantalla de Login.
     * @return bool True si la sesión es válida.
     */
    public static function checkValidSession():bool{
        return isset($_SESSION['nombre']) && isset($_SESSION['rol']) && isset($_SESSION['IdUsuario']);
    }
}