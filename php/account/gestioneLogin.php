<?php
/*
 * Endpoint di login.
 * Richiesta:  POST JSON {username, password}
 * Risposte:   200 {esito: "ok"}
 *             401 {esito: "credenziali_errate"}
 *             400 {esito: "dati_mancanti"}
 */

require_once __DIR__ . '/../sessione/sessione.php';
abilitaRisposteErroreJSON();

// Hash di una password qualsiasi: serve a eseguire comunque password_verify quando l'utente
// non esiste, così il tempo di risposta non rivela quali username sono registrati.
const HASH_FITTIZIO = '$2y$10$p08qYWQtF0G1ropbch2NSe/akhukWGXKYK1Jt5IuPYyas78QbQBNe';

$dati = leggiRichiestaJSON();
$username = campoTesto($dati, 'username');
$password = campoTesto($dati, 'password');

if ($username === '' || $password === '') {
    rispondiJSON(['esito' => 'dati_mancanti'], 400);
}

$query = connessioneDB()->prepare('SELECT password FROM utente WHERE username = ?');
$query->bind_param('s', $username);
$query->execute();
$utente = $query->get_result()->fetch_assoc();

// password_verify confronta la password inserita con l'hash memorizzato nel database
$passwordCorretta = password_verify($password, $utente['password'] ?? HASH_FITTIZIO);

// Stessa risposta per utente inesistente e password errata: non si rivela quali username esistono
if ($utente === null || !$passwordCorretta) {
    rispondiJSON(['esito' => 'credenziali_errate'], 401);
}

apriSessione($username);
rispondiJSON(['esito' => 'ok']);
