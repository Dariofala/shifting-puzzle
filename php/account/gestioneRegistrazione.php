<?php
/*
 * Endpoint di registrazione.
 * Richiesta:  POST JSON {username, password, ripetiPassword}
 * Risposte:   201 {esito: "ok"}
 *             409 {esito: "utente_esistente"}
 *             400 {esito: "input_non_valido", messaggio}
 *
 * Le regole sono le stesse controllate dal browser (funzioniRegistrazione.js),
 * ripetute qui perché i controlli lato client si possono aggirare.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/api.php';
abilitaRisposteErroreJSON();

// Restituisce il messaggio di errore, oppure null se i dati rispettano le regole.
function validaRegistrazione(string $username, string $password, string $ripetiPassword): ?string
{
    if (!preg_match('/^[A-Za-z0-9._-]{8,16}$/', $username)) {
        return "L'username deve avere da 8 a 16 caratteri tra lettere, numeri, punto, trattino e underscore";
    }
    if (strlen($password) < 8 || strlen($password) > 72) { // 72 byte è il limite di bcrypt
        return 'La password deve avere da 8 a 72 caratteri';
    }
    if (preg_match('/\s/', $password)) {
        return 'La password non deve contenere spazi';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'La password deve contenere almeno un numero';
    }
    if ($password === $username) {
        return 'Username e password devono essere diversi';
    }
    if ($password !== $ripetiPassword) {
        return 'Le due password non coincidono';
    }
    return null;
}

$dati = leggiRichiestaJSON();
$username = campoTesto($dati, 'username');
$password = campoTesto($dati, 'password');
$ripetiPassword = campoTesto($dati, 'ripetiPassword');

$errore = validaRegistrazione($username, $password, $ripetiPassword);
if ($errore !== null) {
    rispondiJSON(['esito' => 'input_non_valido', 'messaggio' => $errore], 400);
}

$hash = password_hash($password, PASSWORD_DEFAULT); // la password non viene mai salvata in chiaro

try {
    $query = connessioneDB()->prepare('INSERT INTO utente (username, password) VALUES (?, ?)');
    $query->bind_param('ss', $username, $hash);
    $query->execute();
} catch (mysqli_sql_exception $eccezione) {
    if ($eccezione->getCode() === 1062) { // chiave primaria duplicata: username già registrato
        rispondiJSON(['esito' => 'utente_esistente'], 409);
    }
    throw $eccezione;
}

rispondiJSON(['esito' => 'ok'], 201);
