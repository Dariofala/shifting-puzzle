<?php
/*
 * Controlli sui dati di gioco inviati dal client.
 */

const TEMPO_MASSIMO = 359999; // 99:59:59, in secondi

// La griglia deve contenere esattamente i numeri da 0 a 15 (0 = cella vuota), ognuno una sola volta.
function grigliaValida($griglia): bool
{
    if (!is_array($griglia) || count($griglia) !== 16) {
        return false;
    }
    foreach ($griglia as $valore) {
        if (!is_int($valore)) {
            return false;
        }
    }
    $ordinata = $griglia;
    sort($ordinata);
    return $ordinata === range(0, 15);
}

// Il tempo è un numero intero di secondi.
function tempoValido($tempo): bool
{
    return is_int($tempo) && $tempo >= 0 && $tempo <= TEMPO_MASSIMO;
}
