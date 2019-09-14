<?php

require 'calculadoras.php';

$principal = 8000;
$tasaAnual = 19.00;
$plazo     = 12;

$datos = calcularDatosCredito( $principal, $tasaAnual, $plazo);
$principal              = $datos['str_Principal'];
$tasaAnual              = $datos['str_TasaAnual'];
$plazoMeses             = $datos['str_PlazoMeses'];
$costoEvaluacion        = $datos['str_CostoEvaluacion'];
$costoEvaluacionIVA     = $datos['str_CostoEvaluacionIVA'];
$principalEfectivo      = $datos['str_PrincipalEfectivo'];
$costoAdjudicacion      = $datos['str_CostoAdjudicacion'];
$costoAdjudicacionIVA   = $datos['str_CostoAdjudicacionIVA'];
$totalInversion         = $datos['str_TotalInversion'];
$cuota                  = $datos['str_Cuota'];
$totalPagos             = $datos['str_TotalPagos'];
$totalIntereses         = $datos['str_TotalIntereses'] ;
$interesSobreCapital    = $datos['str_InteresSobreCapital'];
$totalComisiones        = $datos['str_TotalComisiones'];
$totalComisionesIVA     = $datos['str_TotalComisionesIVA'];
$totalGanancias         = $datos['str_TotalGanancias'];
$utilidad               = $datos['str_Utilidad'];
$utilidadSobreInversion = $datos['str_UtilidadSobreInversion'];
$tasaInternaRetorno     = $datos['str_TasaInternaRetorno'];
$operacionUtilidad      = $datos['str_OperacionUtilidad'];
$operacionIVA           = $datos['str_OperacionIVA'];
$tablaCrece             = $datos['TablaCrece'];

echo "DATOS DEL CREDITO:\n";
echo "\tPRINCIPAL: $principal\n";
echo "\tTASA ANUAL NOMINAL: $tasaAnual\n";
echo "\tPLAZO: $plazoMeses\n";
echo "\tCosto Evaluacion: $costoEvaluacion\n";
echo "\tCosto Evaluacion (IVA): $costoEvaluacionIVA\n";
echo "\tPrincipal Efectivo: $principalEfectivo\n";
echo "\tCosto Adjudicacion: $costoAdjudicacion\n";
echo "\tCosto Adjudicacion (IVA): $costoAdjudicacionIVA\n";
echo "\tTotal Inversion: $totalInversion\n";
echo "\tCuota: $cuota\n";
echo "\tTotal Pagos: $totalPagos\n";
echo "\tTotal Intereses: $totalIntereses\n";
echo "\tInteres Sobre Capital: $interesSobreCapital\n";
echo "\tTotal Comisiones: $totalComisiones\n";
echo "\tTotal Comisiones (IVA): $totalComisionesIVA\n";
echo "\tTotal Ganancias: $totalGanancias\n";
echo "\tUtilidad: $utilidad\n";
echo "\tUtilidad Sobre Inversion: $utilidadSobreInversion\n";
echo "\tTasa Interna de Retorno: $tasaInternaRetorno\n";
echo "\tUtilidad de la Operacion: $operacionUtilidad\n";
echo "\tIVA de la Operacion: $operacionIVA\n";

echo "TABLA DE PAGOS, COMISIONES Y GANANCIAS:\n";
foreach ( $tablaCrece as $indice => $fila )
{
    $pago         = $fila['PAGO'];
    $interes      = $fila['INTERES'];
    $capital      = $fila['CAPITAL'];
    $insoluto     = $fila['INSOLUTO'];
    $comision     = $fila['COMISION'];
    $comision_iva = $fila['COMISION_IVA'];
    $ganancia     = $fila['GANANCIA'];

    echo "\tMES $indice:\n";
    echo "\t\tPago: $pago\n";
    echo "\t\tInteres: $interes\n";
    echo "\t\tCapital: $capital\n";
    echo "\t\tInsoluto: $insoluto\n";
    echo "\t\tComision: $comision\n";
    echo "\t\tComision (IVA): $comision_iva\n";
    echo "\t\tGanancia: $ganancia\n";

}

?>
