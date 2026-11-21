<?php
/*
 * Pagina di registrazione di un nuovo utente.
 */

require_once __DIR__ . '/../sessione/sessione.php';

if (utenteAutenticato() !== null) {
    header('Location: ../partita/gioco.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shifting Puzzle – Registrazione</title>
  <link rel="icon" type="image/png" href="../../immagini/SHIFTING_PUZZLE.png">
  <link rel="stylesheet" href="../../css/login.css">
</head>
<body>
  <img src="../../immagini/SHIFTING_PUZZLE.png" alt="Shifting Puzzle" class="logo">

  <form id="formRegistrazione" class="riquadro" novalidate>
    <h1>Registrazione:</h1>

    <label for="username">Username:</label>
    <input id="username" name="username" type="text" placeholder="Username" autocomplete="username"
           minlength="8" maxlength="16" required>
    <p class="suggerimento">Da 8 a 16 caratteri: lettere, numeri, punto, trattino o underscore.</p>

    <label for="password">Password:</label>
    <input id="password" name="password" type="password" placeholder="Password" autocomplete="new-password"
           minlength="8" maxlength="72" required>

    <label for="ripetiPassword">Ripeti password:</label>
    <input id="ripetiPassword" name="ripetiPassword" type="password" placeholder="Ripeti password"
           autocomplete="new-password" minlength="8" maxlength="72" required>
    <p class="suggerimento">Almeno 8 caratteri, almeno un numero e nessuno spazio.</p>

    <p id="messaggioErrore" class="messaggio-errore" role="alert"></p>

    <button type="submit" id="bottoneSubmit">Registrati</button>
    <a href="login.php" class="link-secondario">Hai già un account? Accedi</a>
  </form>

  <script src="../../javascript/gestioneDB.js"></script>
  <script src="../../javascript/registrazione/funzioniRegistrazione.js"></script>
  <script src="../../javascript/registrazione/Registrazione.js"></script>
</body>
</html>
