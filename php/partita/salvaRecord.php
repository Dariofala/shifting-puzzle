<?php
/*
 * Endpoint (POST JSON {tempo}): chiamato quando l'utente risolve il puzzle.
 * Chiude la partita salvata e aggiorna il record solo se il nuovo tempo è migliore.
 * Risposta: {nuovoRecord: bool, record: secondi}
 *
 * Nota: il tempo è misurato dal browser, quindi il server può solo verificarne il formato
 * e accettarlo se migliora il record (vedi "Limiti noti" nel README).
 */

require_once __DIR__ . '/../sessione/sessione.php';
require_once __DIR__ . '/funzioniPartita.php';
abilitaRisposteErroreJSON();
$username = richiediLoginAPI();

$dati = leggiRichiestaJSON();
$tempo = $dati['tempo'] ?? null;

if (!tempoValido($tempo) || $tempo === 0) {
    rispondiJSON(['errore' => 'Tempo non valido'], 400);
}

$db = connessioneDB();
$db->begin_transaction();

// La partita è conclusa: non c'è più niente da riprendere al prossimo accesso
$query = $db->prepare('UPDATE utente SET statoPartita = NULL, ultimoTempo = NULL WHERE username = ?');
$query->bind_param('s', $username);
$query->execute();

// Il record viene aggiornato solo se non esiste ancora o se il nuovo tempo è più basso
$query = $db->prepare(
    'UPDATE utente SET tempoRecord = SEC_TO_TIME(?)
     WHERE username = ? AND (tempoRecord IS NULL OR TIME_TO_SEC(tempoRecord) > ?)'
);
$query->bind_param('isi', $tempo, $username, $tempo);
$query->execute();
$nuovoRecord = $query->affected_rows > 0;

$query = $db->prepare('SELECT TIME_TO_SEC(tempoRecord) AS record FROM utente WHERE username = ?');
$query->bind_param('s', $username);
$query->execute();
$utente = $query->get_result()->fetch_assoc();

$db->commit();

rispondiJSON([
    'nuovoRecord' => $nuovoRecord,
    'record'      => (int) $utente['record'],
]);
