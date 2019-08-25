<?php

require 'calculadoras.php';

$principal = 1000.00;
$tasaAnual = 12.00;
$plazo     = 12;

echo "PRINCIPAL: $principal\n";
echo "TASA ANUAL NOMINAL: $tasaAnual\n";
echo "PLAZO: $plazo\n";

$datos = calcularDatosCredito( $principal, $tasaAnual, $plazo);
$cuota = $datos['Cuota'];
$tasaEfectiva = $datos['TasaEfectiva'];
$tasaInternaRetorno = $datos['TasaInternaRetorno'];
$totalPagos = $datos['TotalPagos'];
$interesSobreCapital = $datos['InteresSobreCapital'];
$totalComisiones = $datos['TotalComisiones'];
$totalRentas = $datos['TotalRentas'];

echo "DATOS DEL CREDITO:\n";
echo "\tCuota: $cuota\n";
echo "\tTasa Efectiva: $tasaEfectiva\n";
echo "\tTasa Interna de Retorno: $tasaInternaRetorno\n";
echo "\tTotal Pagos: $totalPagos\n";
echo "\tInteres Sobre Capital: $interesSobreCapital\n";
echo "\tTotal Comisiones: $totalComisiones\n";
echo "\tTotal Rentas: $totalRentas\n";

?>