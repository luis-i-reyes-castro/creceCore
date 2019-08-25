<?php

require 'calculadoras.php';

$principal = 4000.00;
$tasaAnual = 14.00;
$plazo     = 18;

echo "PRINCIPAL: $principal\n";
echo "TASA ANUAL NOMINAL: $tasaAnual\n";
echo "PLAZO: $plazo\n";

$datos = calcularDatosCredito( $principal, $tasaAnual, $plazo);
$cuota                = $datos['Cuota'];
$totalPagos           = $datos['TotalPagos'];
$totalIntereses       = $datos['TotalIntereses'] ;
$interesSobreCapital  = $datos['InteresSobreCapital'];
$totalComisiones      = $datos['TotalComisiones'];
$totalRentas          = $datos['TotalRentas'];
$ganancia             = $datos['Ganancia'];
$gananciaSobreCapital = $datos['GananciaSobreCapital'];
$tasaInternaRetorno   = $datos['TasaInternaRetorno'];

echo "DATOS DEL CREDITO:\n";
echo "\tCuota: $cuota\n";
echo "\tTotal Pagos: $totalPagos\n";
echo "\tTotal Intereses: $totalIntereses\n";
echo "\tInteres Sobre Capital: $interesSobreCapital\n";
echo "\tTotal Comisiones: $totalComisiones\n";
echo "\tTotal Rentas: $totalRentas\n";
echo "\tGanancia: $ganancia\n";
echo "\tGanancia Sobre Capital: $gananciaSobreCapital\n";
echo "\tTasa Interna de Retorno: $tasaInternaRetorno\n";

$tablaSolicitantes   = $datos['TablaSolicitantes'];
$tablaCrece          = $datos['TablaCrece'];
$tablaInversionistas = $datos['TablaInversionistas'];
echo "Tabla Solicitantes: \n$tablaSolicitantes\n";
echo "Tabla Crece: \n$tablaCrece\n";
echo "Tabla Inversionistas: \n$tablaInversionistas\n";

?>