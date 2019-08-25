<?php

function evaluarTasaEfectiva( $cuota, $plazoMeses, $tasaMensual)
{
	$valor = 0.00;
	for ( $k = 1; $k <= $plazoMeses; $k++) {
		$valor += $cuota / ( ( 1.00 + $tasaMensual ) ** $k );
	}
	return $valor;
}

function evaluarTasaInternaRetorno( $rentas, $tasaMensual)
{
	$valor = 0.00;
	foreach ( $rentas as $k => $renta ) {
		$valor += $renta / ( ( 1.00 + $tasaMensual ) ** $k );
	}
	return $valor;
}

function calcularDatosCredito( $principal,
							   $tasaAnualNominal,
							   $plazoMeses )
{
	
	$tasaMensual = $tasaAnualNominal / 12.00 / 100.00;

	// Calcula la cuota mensual usado la formula para EMI
	// https://en.wikipedia.org/wiki/Equated_monthly_installment
	$eta 	= ( 1.00 + $tasaMensual ) ** $plazoMeses;
	$cuota 	= ( $principal * $tasaMensual * $eta ) / ( $eta - 1.00 );
	$cuota 	= round( $cuota, 2);
	$totalPagos = $cuota * $plazoMeses;
	$interesSobreCapital =
	100.00 * ( $totalPagos - $principal ) / $principal;

	$intereses 	= array(0.00);
	$capitales 	= array(0.00);
	$insolutos	= array($principal);
	$comisiones = array(0.00);
	$rentas		= array(0.00);

	// Calcula recursivamente los pagos, las comisiones y las rentas
	require 'tasaComision.php';
	for( $k = 1; $k <= $plazoMeses; $k++)
	{
		$intereses[$k] 	= round( $insolutos[$k-1] * $tasaMensual, 2);
		$capitales[$k] 	= $cuota - $intereses[$k];
		$insolutos[$k] 	= $insolutos[$k-1] - $capitales[$k];
		$comisiones[$k] = round( $insolutos[$k] * $tasaComision, 2);
		$rentas[$k] 	= $cuota - $comisiones[$k];
	}

	// Calcula la tasa efectiva usando un metodo de biseccion
	require "precisionMetodoBiseccion.php";
	$t_min = 0.00;
	$t_max = 0.10;
	while ( $t_max - $t_min > $precisionMetodoBiseccion )
	{
		$t_med = ( $t_max + $t_min ) / 2.00;
		$valor_presente = 
		evaluarTasaEfectiva( $cuota, $plazoMeses, $t_med);
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
	$tasaEfectiva = round( $t_med * 12.00 * 100.00, 2);

	// Calcula la tasa interna de retorno mediante biseccion
	// usando la maxima posible tasa efectiva como acota superior
	$t_min = 0.00;
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
	$datosCredito['Cuota'] = $cuota;
	$datosCredito['TotalPagos'] = $totalPagos;
	$datosCredito['InteresSobreCapital'] = $interesSobreCapital;
	$datosCredito['Intereses'] 	= $intereses;
	$datosCredito['Capitales'] 	= $capitales;
	$datosCredito['Insolutos'] 	= $insolutos;
	$datosCredito['Comisiones'] = $comisiones;
	$datosCredito['Rentas'] 	= $rentas;
	$datosCredito['TotalComisiones'] 	= array_sum($comisiones);
	$datosCredito['TotalRentas'] 		= array_sum($rentas);
	$datosCredito['TasaEfectiva'] 		= $tasaEfectiva;
	$datosCredito['TasaInternaRetorno'] = $tasaInternaRetorno;

	return $datosCredito;

}

?>