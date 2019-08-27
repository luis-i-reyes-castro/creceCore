<?php

function evaluarTasaInternaRetorno( $rentas, $tasaMensual)
{
    $valor = 0.00;
    foreach ( $rentas as $k => $renta ) {
        $valor += $renta / ( ( 1.00 + $tasaMensual ) ** $k );
    }
    return $valor;
}

function calcularDatosCredito( $principal, $tasaAnualNominal, $plazoMeses)
{
    require "constantes.php";
    
    $tasaMensual = $tasaAnualNominal / 12.00 / 100.00;

    // Calcula la cuota mensual usado la formula para EMI
    // (ver: https://en.wikipedia.org/wiki/Equated_monthly_installment)
    $eta     = ( 1.00 + $tasaMensual ) ** $plazoMeses;
    $cuota     = ( $principal * $tasaMensual * $eta ) / ( $eta - 1.00 );
    
    $cuota               = round( $cuota, 2);
    $totalPagos          = round( $cuota * $plazoMeses, 2);
    $totalIntereses      = round( $totalPagos - $principal, 2);
    $interesSobreCapital = round( 100.00 * $totalIntereses / $principal, 2);

    $intereses  = array(0.00);
    $capitales  = array(0.00);
    $insolutos  = array($principal);
    $comisiones = array(0.00);
    $rentas     = array(0.00);

    // Calcula recursivamente los pagos, las comisiones y las rentas
    for( $k = 1; $k <= $plazoMeses; $k++)
    {
        $intereses[$k] = round( $insolutos[$k-1] * $tasaMensual, 2);
        $capitales[$k] = round( $cuota - $intereses[$k], 2);
        $insolutos[$k] = round( $insolutos[$k-1] - $capitales[$k], 2);
        if ( $insolutos[$k] < 0.00 )
        {
            $insolutos[$k] = 0.00;
        }
        $comisiones[$k] = round( $insolutos[$k] * $tasaComision, 2);
        $rentas[$k]     = round( $cuota - $comisiones[$k], 2);
    }
    $totalComisiones = array_sum($comisiones);
    $totalRentas     = array_sum($rentas);
    $ganancia             = $totalRentas - $principal;
    $gananciaSobreCapital = round( 100.00 * $ganancia / $principal, 2);

    // Calcula la Tasa Interna de Retorno (TIR) mensual mediante biseccion
    // usando la tasa mensual nominal como acota superior
    $t_min = 0.00;
    $t_max = $tasaMensual;
    while ( $t_max - $t_min > $precisionMetodoBiseccion )
    {
        $t_med = ( $t_max + $t_min ) / 2.00;
        $valor_presente = 
        evaluarTasaInternaRetorno( $rentas, $t_med);
        if ( $valor_presente < $principal )
        {
            $t_max = $t_med;
        }
        else if( $valor_presente > $principal )
        {
            $t_min = $t_med;
        }
        else
        {
            break;
        }
    }
    $tasaInternaRetorno = round( $t_med * 12.00 * 100.00, 2);

    // Almacena todos los datos en una sola variable tipo arreglo
    $datosCredito = array();
    $datosCredito['Cuota']                = $cuota;
    $datosCredito['TotalPagos']           = $totalPagos;
    $datosCredito['TotalIntereses']       = $totalIntereses;
    $datosCredito['InteresSobreCapital']  = $interesSobreCapital;
    $datosCredito['TotalComisiones']      = $totalComisiones;
    $datosCredito['TotalRentas']          = $totalRentas;
    $datosCredito['Ganancia']             = $ganancia;
    $datosCredito['GananciaSobreCapital'] = $gananciaSobreCapital;
    $datosCredito['TasaInternaRetorno']   = $tasaInternaRetorno;
    $tablaSolicitantes                    = array();
    $tablaCrece                           = array();
    $tablaInversionistas                  = array();
    
    // Almacena las tablas de pagos como variables tipo grid
    // (ver: https://wiki.processmaker.com/3.1/Grid_Control#PHP_in_Grids)
    for( $k = 1; $k <= $plazoMeses; $k++)
    {
        $indice    = strval($k);
        $interes_  = $intereses[$k];
        $capital_  = $capitales[$k];
        $insoluto_ = $insolutos[$k];
        $comision_ = $comisiones[$k];
        $renta_    = $rentas[$k];
        
        $tablaSolicitantes[$indice] =
        array( 'PERIODO' => $k,
               'PAGO'    => $cuota,
               'INTERES' => $interes_,
               'CAPITAL' => $capital_,
               'INSOLUTO' => $insoluto_ );

        $tablaCrece[$indice] =
        array( 'PERIODO'  => $k,
               'PAGO'     => $cuota,
               'INTERES'  => $interes_,
               'CAPITAL'  => $capital_,
               'INSOLUTO' => $insoluto_,
               'COMISION' => $comision_,
               'RENTA'    => $renta_ );

        $tablaInversionistas[$indice] =
        array( 'PERIODO'  => $k,
               'PAGO'     => $cuota,
               'INSOLUTO' => $insoluto_,
               'COMISION' => $comision_,
               'RENTA'    => $renta_ );

    }

    $datosCredito['TablaSolicitantes']   = $tablaSolicitantes;
    $datosCredito['TablaCrece']          = $tablaCrece;
    $datosCredito['TablaInversionistas'] = $tablaInversionistas;

    return $datosCredito;

}

?>
