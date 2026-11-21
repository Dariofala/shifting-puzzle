<?php
/*
 * Endpoint (POST JSON {griglia, tempo}): salva la partita in corso dell'utente autenticato.
 * griglia: array con i numeri da 0 a 15 letti riga per riga (0 = cella vuota)
 * tempo:   secondi trascorsi dall'inizio della partita
 */

require_once __DIR__ . '/../sessione/sessione.php';
require_once __DIR__ . '/funzioniPartita.php';
abilitaRisposteErroreJSON();
$username = richiediLoginAPI();

$dati = leggiRichiestaJSON();
$griglia = $dati['griglia'] ?? null;
$tempo = $dati['tempo'] ?? null;

if (!grigliaValida($griglia) || !tempoValido($tempo)) {
    rispondiJSON(['errore' => 'Dati della partita non validi'], 400);
}

$statoPartita = json_encode($griglia);

// SEC_TO_TIME converte i secondi nel formato del tipo TIME (es. 125 -> 00:02:05)
$query = connessioneDB()->prepare(
    'UPDATE utente SET statoPartita = ?, ultimoTempo = SEC_TO_TIME(?) WHERE username = ?'
);
$query->bind_param('sis', $statoPartita, $tempo, $username);
$query->execute();

rispondiJSON(['esito' => 'ok']);
