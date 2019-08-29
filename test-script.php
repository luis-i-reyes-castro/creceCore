<?php

require 'calculadoras.php';

$principal = 1000;
$tasaAnual = 12.00;
$plazo     = 12;

echo "PRINCIPAL: $principal\n";
echo "TASA ANUAL NOMINAL: $tasaAnual\n";
echo "PLAZO: $plazo\n";

$datos = calcularDatosCredito( $principal, $tasaAnual, $plazo);
$cuota                = $datos['Cuota'];
$totalPagos           = $datos['TotalPagos'];
$totalIntereses       = $datos['TotalIntereses'] ;
$interesSobreCapital  = $datos['InteresSobreCapital'];
$adjudicacion         = $datos['Adjudicacion'];
$adjudicacion_iva     = $datos['AdjudicacionIVA'];
$totalInversion       = $datos['TotalInversion'];
$totalComisiones      = $datos['TotalComisiones'];
$totalComisionesIVA   = $datos['TotalComisionesIVA'];
$totalRentas          = $datos['TotalRentas'];
$ganancia             = $datos['Ganancia'];
$gananciaSobreCapital = $datos['GananciaSobreCapital'];
$tasaInternaRetorno   = $datos['TasaInternaRetorno'];

echo "DATOS DEL CREDITO:\n";
echo "\tCuota: $cuota\n";
echo "\tTotal Pagos: $totalPagos\n";
echo "\tTotal Intereses: $totalIntereses\n";
echo "\tInteres Sobre Capital: $interesSobreCapital\n";
echo "\tAdjudicacion: $adjudicacion\n";
echo "\tAdjudicacion (IVA): $adjudicacion_iva\n";
echo "\tTotal Inversion: $totalInversion\n";
echo "\tTotal Comisiones: $totalComisiones\n";
echo "\tTotal Comisiones (IVA): $totalComisionesIVA\n";
echo "\tTotal Rentas: $totalRentas\n";
echo "\tGanancia: $ganancia\n";
echo "\tGanancia Sobre Capital: $gananciaSobreCapital\n";
echo "\tTasa Interna de Retorno: $tasaInternaRetorno\n";

$tablaSolicitantes   = $datos['TablaSolicitantes']['1']['PAGO'];
$tablaCrece          = $datos['TablaCrece']['1']['COMISION'];
$tablaInversionistas = $datos['TablaInversionistas']['1']['RENTA'];

echo "Tabla Solicitantes (1,PAGO): \n$tablaSolicitantes\n";
echo "Tabla Crece (1,COMISION): \n$tablaCrece\n";
echo "Tabla Inversionistas (1,RENTA): \n$tablaInversionistas\n";

?>
