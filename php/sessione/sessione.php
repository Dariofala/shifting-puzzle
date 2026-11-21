<?php
/*
 * Gestione della sessione utente.
 *
 * La sessione dura al massimo un'ora dal login. Ogni accesso viene registrato
 * nella tabella logtable: l'inizio al login, la fine al logout o alla scadenza.
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/api.php';

const DURATA_SESSIONE = 3600; // secondi

function avviaSessione(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        // Cookie di sessione non leggibile da JavaScript e non inviato da altri siti
        session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
        session_start();
    }
}

// Restituisce lo username dell'utente autenticato, oppure null se non c'è una sessione valida.
// Una sessione scaduta viene chiusa.
function utenteAutenticato(): ?string
{
    avviaSessione();

    if (!isset($_SESSION['username'], $_SESSION['start_time'])) {
        return null;
    }
    if (time() - $_SESSION['start_time'] > DURATA_SESSIONE) {
        chiudiSessione($_SESSION['start_time'] + DURATA_SESSIONE);
        return null;
    }

    return $_SESSION['username'];
}

// Per le pagine: se l'utente non è autenticato viene rimandato al login.
function richiediLoginPagina(): string
{
    $username = utenteAutenticato();
    if ($username === null) {
        header('Location: ../account/login.php');
        exit;
    }
    return $username;
}

// Per gli endpoint chiamati da JavaScript: se l'utente non è autenticato risponde 401.
function richiediLoginAPI(): string
{
    $username = utenteAutenticato();
    if ($username === null) {
        rispondiJSON(['errore' => 'Sessione scaduta o non valida'], 401);
    }
    return $username;
}

// Dopo un login riuscito: crea una nuova sessione e registra l'accesso.
function apriSessione(string $username): void
{
    avviaSessione();
    session_regenerate_id(true); // nuovo ID di sessione: impedisce la session fixation

    $_SESSION = [];
    $_SESSION['username'] = $username;
    $_SESSION['start_time'] = time();

    $inizio = date('Y-m-d H:i:s', $_SESSION['start_time']);
    $db = connessioneDB();
    $query = $db->prepare('INSERT INTO logtable (utente, timestampInizioSessione) VALUES (?, ?)');
    $query->bind_param('ss', $username, $inizio);
    $query->execute();

    $_SESSION['idLog'] = $db->insert_id;
}

// Registra la fine della sessione (adesso, o all'istante indicato) e la distrugge.
function chiudiSessione(?int $istanteFine = null): void
{
    avviaSessione();

    if (isset($_SESSION['idLog'])) {
        $fine = date('Y-m-d H:i:s', $istanteFine ?? time());
        $idLog = (int) $_SESSION['idLog'];
        $query = connessioneDB()->prepare(
            'UPDATE logtable SET timestampFineSessione = ? WHERE id = ? AND timestampFineSessione IS NULL'
        );
        $query->bind_param('si', $fine, $idLog);
        $query->execute();
    }

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $parametri = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $parametri['path'], $parametri['domain'],
            $parametri['secure'], $parametri['httponly']);
    }
    session_destroy();
}
