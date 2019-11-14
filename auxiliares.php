<?php

function redondearParaAbajo( $valor, $decimales = 2)
{
    
    $t1 = 10. ** $decimales;
    $t2 = 0.1 ** $decimales;
    return round( floor( $t1 * $valor ) * $t2, $decimales);
}

function redondearParaArriba( $valor, $decimales = 2)
{
    
    $t1 = 10. ** $decimales;
    $t2 = 0.1 ** $decimales;
    return round( ceil( $t1 * $valor ) * $t2, $decimales);
}

// $original          = 2.00 / 3;
// $redondeado        = round( $original, 4);
// $redondeado_abajo  = redondearParaAbajo( $original, 4);
// $redondeado_arriba = redondearParaArriba( $original, 4);

// echo "original:          $original\n";
// echo "redondeado:        $redondeado\n";
// echo "redondeado_abajo:  $redondeado_abajo\n";
// echo "redondeado_arriba: $redondeado_arriba\n";

?>