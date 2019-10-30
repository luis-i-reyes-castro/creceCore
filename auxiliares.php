<?php

function redondearParaAbajo( $monto)
{
    
    return round( 0.01 * floor( 100. * $monto ) ,2);
}

function redondearParaArriba( $monto)
{
    
    return round( 0.01 * ceil( 100. * $monto ) ,2);
}

// $original          = 1.00 / 3;
// $redondeado        = round( $original, 2);
// $redondeado_abajo  = redondearParaAbajo($original);
// $redondeado_arriba = redondearParaArriba( $original);

// echo "original:          $original\n";
// echo "redondeado:        $redondeado\n";
// echo "redondeado_abajo:  $redondeado_abajo\n";
// echo "redondeado_arriba: $redondeado_arriba\n";

?>