<?php
/*
 * Endpoint (GET): partita salvata e record dell'utente autenticato.
 * Risposta: {griglia: array di 16 numeri | null, tempo: secondi | null, record: secondi | null}
 */

require_once __DIR__ . '/../sessione/sessione.php';
abilitaRisposteErroreJSON();
$username = richiediLoginAPI();

$query = connessioneDB()->prepare(
    'SELECT statoPartita, TIME_TO_SEC(ultimoTempo) AS tempo, TIME_TO_SEC(tempoRecord) AS record
     FROM utente
     WHERE username = ?'
);
$query->bind_param('s', $username);
$query->execute();
$utente = $query->get_result()->fetch_assoc();

if ($utente === null) {
    rispondiJSON(['errore' => 'Utente non trovato'], 404);
}

rispondiJSON([
    'griglia' => $utente['statoPartita'] === null ? null : json_decode($utente['statoPartita']),
    'tempo'   => $utente['tempo'] === null ? null : (int) $utente['tempo'],
    'record'  => $utente['record'] === null ? null : (int) $utente['record'],
]);
