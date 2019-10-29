<?php

function calcularValorPresente( $flujosDeEfectivo, $tasaMensual)
{
    $valor = 0.00;
    foreach ( $flujosDeEfectivo as $k => $flujo )
    {
        $valor += $flujo / ( ( 1.00 + $tasaMensual ) ** $k );
    }
    return $valor;
}

function calcularDatosCCF( $principal, $tasaAnual, $plazoMeses)
{
    require_once "recargos.php";
    require_once "impuestos.php";

    $principal  = round( $principal, 2);
    $tasaAnual  = round( $tasaAnual, 2);
    $plazoMeses = intval($plazoMeses);

    // Calcula el costo de evaluacion (lo paga el solicitante)
    $costoEvaluacion    = recargoEvaluacion( $principal, 'CCF');
    $costoEvaluacionIVA = round( $costoEvaluacion * $tasaIVA, 2);
    $costoEvaluacionTotal = $costoEvaluacion + $costoEvaluacionIVA;
    $principalEfectivo    = $principal - $costoEvaluacionTotal;

    // Calcula el costo de adjudicacion (lo paga el inversionista)
    $costoAdjudicacion     = recargoAdjudicacion( $principal, 'CCF');
    $costoAdjudicacionIVA  = round( $costoAdjudicacion * $tasaIVA, 2);
    $costoAdjudicacionTotal = $costoAdjudicacion + $costoAdjudicacionIVA;
    $totalInversion         = $principal + $costoAdjudicacionTotal;
    
    // Calcula la tasa diaria y la compone para obtener la tasa mensual
    $tasaDiaria  = $tasaAnual / 360. / 100.;
    $tasaMensual = ( ( 1. + $tasaDiaria ) ** 30 ) - 1.;

    // Calcula la cuota mensual usado la formula para EMI
    // (ver: https://en.wikipedia.org/wiki/Equated_monthly_installment)
    $eta   = ( 1. + $tasaMensual ) ** $plazoMeses;
    $cuota = ( $principal * $tasaMensual * $eta ) / ( $eta - 1. );
    $cuota = round( $cuota, 2);

    $pagos     = array(0.);
    $intereses = array(0.);
    $capitales = array(0.);
    $insolutos = array($principal);
    $tasaComision   = tasaComision('CCF');
    $comisiones     = array(0.);
    $comisiones_iva = array(0.);
    $ganancias      = array(0.);

    // Calcula recursivamente los pagos, las comisiones y las rentas
    for( $k = 1; $k < $plazoMeses; $k++)
    {
        $pagos[$k]      = $cuota;
        $intereses[$k]  = round( $insolutos[$k-1] * $tasaMensual, 2);
        $capitales[$k]  = round( $cuota - $intereses[$k], 2);
        $insolutos[$k]  = round( $insolutos[$k-1] - $capitales[$k], 2);
        $comisiones[$k]     = round( $insolutos[$k-1] * $tasaComision, 2);
        $comisiones_iva[$k] = round( $comisiones[$k] * $tasaIVA, 2);
        $ganancias[$k]      = round( $pagos[$k] - $comisiones[$k] - $comisiones_iva[$k], 2);
    }
    // Calcula la ultima fila de las tablas
    $k                  = $plazoMeses;
    $intereses[$k]      = round( $insolutos[$k-1] * $tasaMensual, 2);
    $capitales[$k]      = $insolutos[$k-1];
    $pagos[$k]          = $intereses[$k] + $capitales[$k];
    $insolutos[$k]      = 0.;
    $comisiones[$k]     = round( $insolutos[$k-1] * $tasaComision, 2);
    $comisiones_iva[$k] = round( $comisiones[$k] * $tasaIVA, 2);
    $ganancias[$k]      = round( $pagos[$k] - $comisiones[$k] - $comisiones_iva[$k], 2);

    // Calcula la suma de las comisiones y rentas
    $totalPagos          = round( array_sum($pagos), 2);
    $totalIntereses      = round( $totalPagos - $principal, 2);
    $interesSobreCapital = round( 100. * $totalIntereses / $principal, 2);
    $totalComisiones     = round( array_sum($comisiones), 2);
    $totalComisionesIVA  = round( array_sum($comisiones_iva), 2);
    $totalGanancias      = round( array_sum($ganancias), 2);
    $utilidad            = round( $totalGanancias - $totalInversion, 2);
    $utilidadSobreInversion = round( 100. * $utilidad / $totalInversion, 2);

    // Calcula la Tasa Interna de Retorno (TIR) mensual mediante biseccion
    // usando la tasa mensual nominal como acota superior
    $t_min = 0.;
    $t_max = $tasaMensual;
    while ( $t_max - $t_min > 1E-6 )
    {
        $t_med = ( $t_max + $t_min ) / 2.;
        $valor_presente = calcularValorPresente( $ganancias, $t_med);
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
    // Compone la TIR mensual para obtener la TIR anual
    $tasaInternaRetorno = round( ( ( ( 1. + $t_med ) ** 12 ) - 1. ) * 100., 2);

    // Calcula las utilidades e IVAs de la operacion
    $operacionUtilidad = $costoEvaluacion
                       + $costoAdjudicacion
                       + $totalComisiones;
    $operacionIVA      = $costoEvaluacionIVA
                       + $costoAdjudicacionIVA
                       + $totalComisionesIVA;

    // Almacena todos los datos en una sola variable tipo arreglo
    $datosCredito = array();
    $datosCredito['Principal']              = $principal;
    $datosCredito['TasaAnual']              = $tasaAnual;
    $datosCredito['PlazoMeses']             = $plazoMeses;
    $datosCredito['CostoEvaluacion']        = $costoEvaluacion;
    $datosCredito['CostoEvaluacionIVA']     = $costoEvaluacionIVA;
    $datosCredito['CostoEvaluacionTotal']   = $costoEvaluacionTotal;
    $datosCredito['PrincipalEfectivo']      = $principalEfectivo;
    $datosCredito['CostoAdjudicacion']      = $costoAdjudicacion;
    $datosCredito['CostoAdjudicacionIVA']   = $costoAdjudicacionIVA;
    $datosCredito['CostoAdjudicacionTotal'] = $costoAdjudicacionTotal;
    $datosCredito['TotalInversion']         = $totalInversion;
    $datosCredito['Cuota']                  = $cuota;
    $datosCredito['TotalPagos']             = $totalPagos;
    $datosCredito['TotalIntereses']         = $totalIntereses;
    $datosCredito['InteresSobreCapital']    = $interesSobreCapital;
    $datosCredito['TotalComisiones']        = $totalComisiones;
    $datosCredito['TotalComisionesIVA']     = $totalComisionesIVA;
    $datosCredito['TotalGanancias']         = $totalGanancias;
    $datosCredito['Utilidad']               = $utilidad;
    $datosCredito['UtilidadSobreInversion'] = $utilidadSobreInversion;
    $datosCredito['TasaInternaRetorno']     = $tasaInternaRetorno;
    $datosCredito['OperacionUtilidad']      = $operacionUtilidad;
    $datosCredito['OperacionIVA']           = $operacionIVA;
    
    // Hace copias de los datos anteriores formateadas como strings

    $datosCredito['str_Principal']              = '$' . number_format( $principal, 2);
    $datosCredito['str_TasaAnual']              =       number_format( $tasaAnual, 2) . '%';
    $datosCredito['str_PlazoMeses']             =       number_format( $plazoMeses, 0) . ' meses';
    $datosCredito['str_CostoEvaluacion']        = '$' . number_format( $costoEvaluacion, 2);
    $datosCredito['str_CostoEvaluacionIVA']     = '$' . number_format( $costoEvaluacionIVA, 2);
    $datosCredito['str_CostoEvaluacionTotal']   = '$' . number_format( $costoEvaluacionTotal, 2);
    $datosCredito['str_PrincipalEfectivo']      = '$' . number_format( $principalEfectivo, 2);
    $datosCredito['str_CostoAdjudicacion']      = '$' . number_format( $costoAdjudicacion, 2);
    $datosCredito['str_CostoAdjudicacionIVA']   = '$' . number_format( $costoAdjudicacionIVA, 2);
    $datosCredito['str_CostoAdjudicacionTotal'] = '$' . number_format( $costoAdjudicacionTotal, 2);
    $datosCredito['str_TotalInversion']         = '$' . number_format( $totalInversion, 2);
    $datosCredito['str_Cuota']                  = '$' . number_format( $cuota, 2);
    $datosCredito['str_TotalPagos']             = '$' . number_format( $totalPagos, 2);
    $datosCredito['str_TotalIntereses']         = '$' . number_format( $totalIntereses, 2);
    $datosCredito['str_InteresSobreCapital']    =       number_format( $interesSobreCapital, 2) . '%';
    $datosCredito['str_TotalComisiones']        = '$' . number_format( $totalComisiones, 2);
    $datosCredito['str_TotalComisionesIVA']     = '$' . number_format( $totalComisionesIVA, 2);
    $datosCredito['str_TotalGanancias']         = '$' . number_format( $totalGanancias, 2);
    $datosCredito['str_Utilidad']               = '$' . number_format( $utilidad, 2);
    $datosCredito['str_UtilidadSobreInversion'] =       number_format( $utilidadSobreInversion, 2) . '%';
    $datosCredito['str_TasaInternaRetorno']     =       number_format( $tasaInternaRetorno, 2) . '%';
    $datosCredito['str_OperacionUtilidad']      = '$' . number_format( $operacionUtilidad, 2);
    $datosCredito['str_OperacionIVA']           = '$' . number_format( $operacionIVA, 2);
    
    // Almacena las tablas de pagos como variables tipo grid
    // (ver: https://wiki.processmaker.com/3.1/Grid_Control#PHP_in_Grids)
    $tablaSolicitantes = array();
    $tablaCrece        = array();
    $tablaSinFormato   = array();
    for( $k = 1; $k <= $plazoMeses; $k++)
    {
        $indice    = strval($k);
        $indice2D  = sprintf( '%02d', $k);
        $pago_     = $pagos[$k];
        $interes_  = $intereses[$k];
        $capital_  = $capitales[$k];
        $insoluto_ = $insolutos[$k];
        $comision_     = $comisiones[$k];
        $comision_iva_ = $comisiones_iva[$k];
        $ganancia_     = $ganancias[$k];
        
        $tablaSolicitantes[$indice] =
        array( 'PAGO'     => '$' . number_format( $pago_, 2),
               'INTERES'  => '$' . number_format( $interes_, 2),
               'CAPITAL'  => '$' . number_format( $capital_, 2),
               'INSOLUTO' => '$' . number_format( $insoluto_, 2) );

        $tablaCrece[$indice] =
        array( 'PAGO'     => '$' . number_format( $pago_, 2),
               'INTERES'  => '$' . number_format( $interes_, 2),
               'CAPITAL'  => '$' . number_format( $capital_, 2),
               'INSOLUTO' => '$' . number_format( $insoluto_, 2),
               'COMISION'     => '$' . number_format( $comision_, 2),
               'COMISION_IVA' => '$' . number_format( $comision_iva_, 2),
               'GANANCIA'     => '$' . number_format( $ganancia_, 2) );

        $tablaSinFormato[$indice] =
        array( 'INDICE'   => $indice2D,
               'PAGO'     => $pago_,
               'INTERES'  => $interes_,
               'CAPITAL'  => $capital_,
               'INSOLUTO' => $insoluto_,
               'COMISION'     => $comision_,
               'COMISION_IVA' => $comision_iva_,
               'GANANCIA'     => $ganancia_ );

    }

    $datosCredito['TablaSolicitantes'] = $tablaSolicitantes;
    $datosCredito['TablaCrece']        = $tablaCrece;
    $datosCredito['TablaSinFormato']   = $tablaSinFormato;

    return $datosCredito;

}

?>
