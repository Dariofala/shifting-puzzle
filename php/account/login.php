<?php
/*
 * Pagina di login. Chi ha già una sessione valida viene portato direttamente al gioco.
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
  <title>Shifting Puzzle – Login</title>
  <link rel="icon" type="image/png" href="../../immagini/SHIFTING_PUZZLE.png">
  <link rel="stylesheet" href="../../css/login.css">
</head>
<body>
  <img src="../../immagini/SHIFTING_PUZZLE.png" alt="Shifting Puzzle" class="logo">

  <form id="formLogin" class="riquadro" novalidate>
    <h1>Login:</h1>

    <?php if (isset($_GET['registrato'])): ?>
      <p class="messaggio-successo">Registrazione completata: ora puoi accedere.</p>
    <?php endif; ?>

    <label for="username">Username:</label>
    <input id="username" name="username" type="text" placeholder="Username" autocomplete="username" required>

    <label for="password">Password:</label>
    <input id="password" name="password" type="password" placeholder="Password" autocomplete="current-password" required>

    <p id="messaggioErrore" class="messaggio-errore" role="alert"></p>

    <button type="submit" id="bottoneSubmit">Login</button>
    <a href="registrazione.php" class="link-secondario">Nuovo utente?</a>
  </form>

  <script src="../../javascript/gestioneDB.js"></script>
  <script src="../../javascript/login/funzioniLogin.js"></script>
  <script src="../../javascript/login/Login.js"></script>
</body>
</html>
