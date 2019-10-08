<?php

function crearCopiaFormateada( $tabla, $especificaciones)
{
    $nuevaTabla = array();
    foreach ( $tabla as $ind_fila => $fila )
    {
        $nuevaTabla[$ind_fila] = array();
        foreach ( $especificaciones as $columna => $formato )
        {
            $entradaOriginal = $fila[$columna];
            $nuevaColumna    = 'STR_' . $columna;
            $nuevaEntrada    = '';

            if ( $formato == '$' )
            {
                $nuevaEntrada = '$' . number_format( $entradaOriginal, 2);
            }
            elseif ( $formato == '%' )
            {
                $nuevaEntrada = number_format( $entradaOriginal, 2) . '%';
            }
            elseif ( $formato == 'm' )
            {
                $nuevaEntrada = strval($entradaOriginal) . ' meses';
            }
            elseif ( $formato == 'd' )
            {
                $nuevaEntrada = strval($entradaOriginal) . ' dias';
            }
            else
            {
                $nuevaEntrada = strval($entradaOriginal);
            }

            $nuevaTabla[$ind_fila][$columna]      = $entradaOriginal;
            $nuevaTabla[$ind_fila][$nuevaColumna] = $nuevaEntrada;

        }
    }
    return $nuevaTabla;
}

// // Para desarrollo/depuracion
// $tabla = array(
//          '1' => array( 'Name'=>'John Doe',
//                        'Salary'=>22525.99,
//                        'HireDate'=>'2012-12-31'),
//          '2' => array( 'Name'=>'Jane Roe',
//                        'Salary'=>40000.00,
//                        'HireDate'=>'1997-06-01'),
//          '3' => array( 'Name'=>'Jill Hill',
//                        'Salary'=>33600.10,
//                        'HireDate'=>'2008-01-25')
//          );
// $especificaciones = array( 'Name' => '',
//                            'Salary' => '$');

// $nuevaTabla = crearCopiaFormateada( $tabla, $especificaciones);
// print_r($nuevaTabla);

?>