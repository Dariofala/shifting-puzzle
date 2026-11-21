<?php
/*
 * Funzioni di supporto per gli endpoint che comunicano con il client in JSON.
 */

// Invia una risposta JSON con il codice HTTP indicato e termina lo script.
function rispondiJSON($dati, int $codice = 200): void
{
    http_response_code($codice);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($dati);
    exit;
}

// Legge il corpo di una richiesta POST e lo decodifica da JSON.
// Se il metodo non è POST o il corpo non è un oggetto JSON valido, risponde con un errore.
function leggiRichiestaJSON(): array
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        rispondiJSON(['errore' => 'Metodo non consentito'], 405);
    }

    $dati = json_decode(file_get_contents('php://input'), true);
    if (!is_array($dati)) {
        rispondiJSON(['errore' => 'Richiesta non valida'], 400);
    }

    return $dati;
}

// Restituisce il campo testuale richiesto, oppure una stringa vuota se manca o non è un testo.
function campoTesto(array $dati, string $campo): string
{
    return isset($dati[$campo]) && is_string($dati[$campo]) ? $dati[$campo] : '';
}

// Un errore imprevisto (ad esempio il database non raggiungibile) diventa una risposta JSON 500:
// il client riceve un messaggio generico, i dettagli finiscono solo nel log del server.
function abilitaRisposteErroreJSON(): void
{
    set_exception_handler(function (Throwable $eccezione) {
        error_log('Shifting Puzzle: ' . $eccezione->getMessage());
        rispondiJSON(['errore' => 'Errore interno del server'], 500);
    });
}
