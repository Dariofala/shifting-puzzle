<?php
/*
 * Connessione al database.
 *
 * I valori predefiniti sono quelli di un'installazione locale di XAMPP
 * (utente "root" senza password, database "shifting_puzzle").
 * Per un altro ambiente si possono impostare le variabili d'ambiente
 * DB_HOST, DB_USER, DB_PASSWORD e DB_NAME, senza modificare il codice.
 */

date_default_timezone_set('Europe/Rome');

function connessioneDB(): mysqli
{
    static $connessione = null; // una sola connessione per ogni richiesta

    if ($connessione === null) {
        // Gli errori SQL vengono lanciati come eccezioni invece di essere ignorati
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $password = getenv('DB_PASSWORD');
        $connessione = new mysqli(
            getenv('DB_HOST') ?: 'localhost',
            getenv('DB_USER') ?: 'root',
            $password === false ? '' : $password,
            getenv('DB_NAME') ?: 'shifting_puzzle'
        );
        $connessione->set_charset('utf8mb4');
    }

    return $connessione;
}
