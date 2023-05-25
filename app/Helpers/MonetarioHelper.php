<?php

if (! function_exists('formatarNumero')) {
    function formatoMonetarioApiCBC($numero, $casasDecimais = 2, $separadorDecimal = ',', $separadorMilhar = '')
    {
        return number_format($numero, $casasDecimais, $separadorDecimal, $separadorMilhar);
    }
}
