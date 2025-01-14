<?php
/**
 * Clase auxiliar que muestra componentes reutilizables para vistas
 */
class VistaComponentes{

    /**
     * Muestra un control con las opciones pasadas.
     * @param opciones array de stdObject con las opciones (tooltip, icono, url destino... )
     */
    public static function menuBar(array $opciones){
        echo "<ul class='barraBotonesAccion'>";
        foreach($opciones as $opcion){
            echo "<li>";
            echo "<span class='tooltip'>".$opcion->tooltip."</span>";
            echo "<a href='".$opcion->url."'>";
            echo "<img src='".$opcion->icono."' alt='Botón ".$opcion->tooltip."'></img>";
            echo "</a>";
            echo "</li>";
        }
        echo "</ul>";
    }

    /**
     * Muestra un control con las opciones pasadas, en miniatura
     * @param opciones array de stdObject con las opciones (tooltip, icono, url destino... )
     */
    public static function actionIcons(array $opciones){
        echo "<ul class='barraIconos'>";
        foreach($opciones as $opcion){
            echo "<li>";
            echo "<span class='tooltip'>".$opcion->tooltip."</span>";
            echo "<div class='contenedorIcono'>";
            echo "<a href='".$opcion->url."'>";
            echo "<img src='".$opcion->icono."' alt='Icono ".$opcion->tooltip."'></img>";
            echo "</a>";
            echo "</div>";
            echo "</li>";            
        }
        echo "</ul>";
    }

    /**
     * Muestra un control inline en miniatura
     * @param opciones array de stdObject con las opciones (tooltip, icono, url destino... )
     */
    public static function inlineIcon(array $opciones){
        foreach($opciones as $opcion){
            echo "<div class='barraIconos'>";
            echo "<span class='tooltip'>".$opcion->tooltip."</span>";
            echo "<div class='contenedorIcono'>";
            echo "<a href='".$opcion->url."'>";
            echo "<img src='".$opcion->icono."' alt='Icono ".$opcion->tooltip."'></img>";
            echo "</a>";
            echo "</div>";
        }
    }

}