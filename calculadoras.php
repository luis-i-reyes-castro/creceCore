<?php

function evaluarTasaInternaRetorno( $rentas, $tasaMensual)
{
    $valor = 0.00;
    foreach ( $rentas as $k => $renta ) {
        $valor += $renta / ( ( 1.00 + $tasaMensual ) ** $k );
    }
    return $valor;
}

function calcularDatosCredito( $principal, $tasaAnual, $plazoMeses)
{
    $principal  = round( $principal, 2);
    $tasaAnual  = round( $tasaAnual, 2);
    $plazoMeses = intval($plazoMeses);
    require "constantes.php";
    
    // Calcula la tasa diaria y la compone para obtener la tasa mensual
    $tasaDiaria  = $tasaAnual / 365. / 100.;
    $tasaMensual = ( ( 1. + $tasaDiaria ) ** 30 ) - 1.;

    // Calcula la cuota mensual usado la formula para EMI
    // (ver: https://en.wikipedia.org/wiki/Equated_monthly_installment)
    $eta   = ( 1. + $tasaMensual ) ** $plazoMeses;
    $cuota = ( $principal * $tasaMensual * $eta ) / ( $eta - 1. );
    $cuota = round( $cuota, 2);

    $totalPagos          = round( $cuota * $plazoMeses, 2);
    $totalIntereses      = round( $totalPagos - $principal, 2);
    $interesSobreCapital = round( 100. * $totalIntereses / $principal, 2);
    $adjudicacion        = round( $principal * $tasaAdjudicacion, 2);
    $adjudicacion_iva    = round( $adjudicacion * $tasaIVA, 2);
    $totalInversion      = $principal + $adjudicacion + $adjudicacion_iva;

    $intereses = array(0.);
    $capitales = array(0.);
    $insolutos = array($principal);
    $comisiones     = array(0.);
    $comisiones_iva = array(0.);
    $rentas         = array(0.);

    // Calcula recursivamente los pagos, las comisiones y las rentas
    for( $k = 1; $k <= $plazoMeses; $k++)
    {
        $intereses[$k]  = round( $insolutos[$k-1] * $tasaMensual, 2);
        $capitales[$k]  = round( $cuota - $intereses[$k], 2);
        $insolutos[$k]  = round( $insolutos[$k-1] - $capitales[$k], 2);
        $comisiones[$k]     = round( $insolutos[$k] * $tasaComision, 2);
        $comisiones_iva[$k] = round( $comisiones[$k] * $tasaIVA, 2);
        $rentas[$k]         = round( $cuota
                                   - $comisiones[$k]
                                   - $comisiones_iva[$k], 2);
    }
    // Corrije la ultima fila de las tablas
    $k                  = $plazoMeses;
    $insolutos[$k]      = 0.;
    $comisiones[$k]     = 0.;
    $comisiones_iva[$k] = 0.;
    $rentas[$k]         = $cuota;

    // Calcula la suma de las comisiones y rentas
    $totalComisiones    = round( array_sum($comisiones), 2);
    $totalComisionesIVA = round( array_sum($comisiones_iva), 2);
    $totalRentas        = round( array_sum($rentas), 2);
    $ganancia           = round( $totalRentas - $totalInversion, 2);
    $gananciaSobreInversion =
    round( 100. * $ganancia / $totalInversion, 2);

    // Calcula la Tasa Interna de Retorno (TIR) mensual mediante biseccion
    // usando la tasa mensual nominal como acota superior
    $t_min = 0.;
    $t_max = $tasaMensual;
    while ( $t_max - $t_min > $precisionMetodoBiseccion )
    {
        $t_med = ( $t_max + $t_min ) / 2.;
        $valor_presente = evaluarTasaInternaRetorno( $rentas, $t_med);
        if ( $valor_presente < $totalInversion )
        {
            $t_max = $t_med;
        }
        else if( $valor_presente > $totalInversion )
        {
            $t_min = $t_med;
        }
        else
        {
            break;
        }
    }
    $tasaInternaRetorno = round( $t_med * 12. * 100., 2);

    // Almacena todos los datos en una sola variable tipo arreglo
    $datosCredito = array();
    $datosCredito['Cuota']                  = $cuota;
    $datosCredito['TotalPagos']             = $totalPagos;
    $datosCredito['TotalIntereses']         = $totalIntereses;
    $datosCredito['InteresSobreCapital']    = $interesSobreCapital;
    $datosCredito['Adjudicacion']           = $adjudicacion;
    $datosCredito['AdjudicacionIVA']        = $adjudicacion_iva;
    $datosCredito['TotalInversion']         = $totalInversion;
    $datosCredito['TotalComisiones']        = $totalComisiones;
    $datosCredito['TotalComisionesIVA']     = $totalComisionesIVA;
    $datosCredito['TotalRentas']            = $totalRentas;
    $datosCredito['Ganancia']               = $ganancia;
    $datosCredito['GananciaSobreInversion'] = $gananciaSobreInversion;
    $datosCredito['TasaInternaRetorno']     = $tasaInternaRetorno;
    $tablaSolicitantes   = array();
    $tablaCrece          = array();
    $tablaInversionistas = array();
    
    // Almacena las tablas de pagos como variables tipo grid
    // (ver: https://wiki.processmaker.com/3.1/Grid_Control#PHP_in_Grids)
    for( $k = 1; $k <= $plazoMeses; $k++)
    {
        $indice    = strval($k);
        $interes_  = $intereses[$k];
        $capital_  = $capitales[$k];
        $insoluto_ = $insolutos[$k];
        $comision_     = $comisiones[$k];
        $comision_iva_ = $comisiones_iva[$k];
        $renta_        = $rentas[$k];
        
        $tablaSolicitantes[$indice] =
        array( 'PAGO'     => '$' . number_format( $cuota, 2),
               'INTERES'  => '$' . number_format( $interes_, 2),
               'CAPITAL'  => '$' . number_format( $capital_, 2),
               'INSOLUTO' => '$' . number_format( $insoluto_, 2) );

        $tablaCrece[$indice] =
        array( 'PAGO'     => '$' . number_format( $cuota, 2),
               'INTERES'  => '$' . number_format( $interes_, 2),
               'CAPITAL'  => '$' . number_format( $capital_, 2),
               'INSOLUTO' => '$' . number_format( $insoluto_, 2),
               'COMISION'     => '$' . number_format( $comision_, 2),
               'COMISION_IVA' => '$' . number_format( $comision_iva_, 2),
               'RENTA'        => '$' . number_format( $renta_, 2) );

        $tablaInversionistas[$indice] =
        array( 'PAGO'     => '$' . number_format( $cuota, 2),
               'INSOLUTO' => '$' . number_format( $insoluto_, 2),
               'COMISION'     => '$' . number_format( $comision_, 2),
               'COMISION_IVA' => '$' . number_format( $comision_iva_, 2),
               'RENTA'        => '$' . number_format( $renta_, 2) );

    }

    $datosCredito['TablaSolicitantes']   = $tablaSolicitantes;
    $datosCredito['TablaCrece']          = $tablaCrece;
    $datosCredito['TablaInversionistas'] = $tablaInversionistas;

    return $datosCredito;

}

?>
