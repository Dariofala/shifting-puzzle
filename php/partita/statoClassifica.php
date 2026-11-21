<?php
/*
 * Endpoint (GET): classifica dei giocatori che hanno vinto almeno una partita,
 * ordinata per miglior tempo. A parità di tempo i giocatori condividono la posizione.
 * Risposta: [{posizione, username, record (secondi), ultimoAccesso ("gg-mm-aaaa" | null)}, ...]
 */

require_once __DIR__ . '/../sessione/sessione.php';
abilitaRisposteErroreJSON();
richiediLoginAPI();

$risultato = connessioneDB()->query(
    'SELECT u.username,
            TIME_TO_SEC(u.tempoRecord) AS record,
            MAX(l.timestampInizioSessione) AS ultimoAccesso
     FROM utente u
          LEFT JOIN logtable l ON l.utente = u.username
     WHERE u.tempoRecord IS NOT NULL
     GROUP BY u.username, u.tempoRecord
     ORDER BY u.tempoRecord ASC, u.username ASC'
);

$classifica = [];
$posizione = 0;
$recordPrecedente = null;

foreach ($risultato->fetch_all(MYSQLI_ASSOC) as $indice => $riga) {
    $record = (int) $riga['record'];
    if ($record !== $recordPrecedente) {
        $posizione = $indice + 1;
        $recordPrecedente = $record;
    }

    $classifica[] = [
        'posizione'     => $posizione,
        'username'      => $riga['username'],
        'record'        => $record,
        'ultimoAccesso' => $riga['ultimoAccesso'] === null
            ? null
            : date('d-m-Y', strtotime($riga['ultimoAccesso'])),
    ];
}

rispondiJSON($classifica);
