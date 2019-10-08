<?php

function crearCopiaFormateada( $tabla, $especificaciones)
{
    $nuevaTabla = array();
    foreach ( $tabla as $ind_fila => $fila )
    {
        $nuevaTabla[$ind_fila] = array();
        foreach ( $especificaciones as $columna => $formato )
        {
            $entrada = $fila[$columna];
            if ( $formato == '$' )
            {
                $entrada = '$' . number_format( $entrada, 2);
            }
            elseif ( $formato == '%' )
            {
                $entrada = number_format( $entrada, 2) . '%';
            }
            elseif ( $formato == 'm' )
            {
                $entrada = strval($entrada) . ' meses';
            }
            elseif ( $formato == 'd' )
            {
                $entrada = strval($entrada) . ' dias';
            }
            else
            {
                $entrada = strval($entrada);
            }
            $nuevaTabla[$ind_fila][$columna] = $entrada;
        }
    }
    return $nuevaTabla;
}

?>