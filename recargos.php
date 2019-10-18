<?php

function recargoEvaluacion( $principal, $tipoDeOperacion)
{
    $recargo = 0.;
    if ( $principal >= 0. )
    {
        if ( strcasecmp( $tipoDeOperacion, 'CCF') == 0 )
        {
            if ( $principal <= 2500. )
            {
                $recargo += round( 0.10 * $principal, 2);
            }
            elseif ( $principal <= 5000. )
            {
                $recargo += 250.;
            }
            else
            {
                $recargo += round( 0.05 * $principal, 2);
            }
        }
        elseif ( strcasecmp( $tipoDeOperacion, 'Factoring') == 0 )
        {
            if ( $principal <= 4000. )
            {
                $recargo += round( 0.06 * $principal, 2);
            }
            elseif ( $principal <= 6000. )
            {
                $recargo += 240.;
            }
            else
            {
                $recargo += round( 0.04 * $principal, 2);
            }
        }
    }
    return $recargo;
}

function recargoAdjudicacion( $principal, $tipoDeOperacion)
{
    $tasa    = 0.0080;
    $recargo = 0.;
    if ( $principal >= 0. )
    {
        $recargo += round( $tasa * $principal, 2);
    }
    return $recargo;
}

function tasaComision( $tipoDeOperacion)
{
    if ( strcasecmp( $tipoDeOperacion, 'CCF') == 0 )
    {
        return 0.0045;
    }
    elseif ( strcasecmp( $tipoDeOperacion, 'Factoring') == 0 )
    {
        return 0.0010;
    }
    return 0.;
}

?>