<?php
/*
 * Pagina del gioco, riservata agli utenti autenticati.
 */

require_once __DIR__ . '/../sessione/sessione.php';
$username = richiediLoginPagina();
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Shifting Puzzle – Gioco</title>
  <link rel="icon" type="image/png" href="../../immagini/SHIFTING_PUZZLE.png">
  <link rel="stylesheet" href="../../css/gioco.css">
</head>
<body>
  <div id="pagina">
    <section id="pannelloTempi" aria-label="Tempo e record">
      <p id="record" class="riquadro-verde">RECORD:<br><span id="tempoRecord">--:--</span></p>
      <div id="riquadroTempo">
        <p id="tempo" class="riquadro-verde">Tempo:</p>
        <p id="timer" class="riquadro-verde">00:00</p>
      </div>
      <p id="nuovoRecord" class="riquadro-verde" hidden>Nuovo record!</p>
    </section>

    <main id="centro">
      <img id="logo" src="../../immagini/SHIFTING_PUZZLE.png" alt="Shifting Puzzle">
      <div id="griglia" aria-label="Griglia del gioco"></div>
    </main>

    <nav id="comandi" aria-label="Comandi">
      <button id="bottoneDocumentazione" type="button">Info</button>
      <button id="bottoneInversioneComandi" type="button" aria-pressed="false">Inverti<br>comandi</button>
      <button id="navMenu" type="button" aria-label="Apri il menu" aria-controls="menuTendina" aria-expanded="false">
        <span class="linea"></span>
        <span class="linea"></span>
        <span class="linea"></span>
      </button>
    </nav>
  </div>

  <div id="menuTendina" class="menuScomparsa" aria-hidden="true">
    <button id="pulsanteChiudi" type="button" aria-label="Chiudi il menu">&times;</button>
    <div id="contenutoMenuScomparsa">
      <p id="ciao" class="oggettiMenu">Ciao <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>!</p>
      <div id="messaggioVittoria" hidden>
        <p class="titolo-vittoria">HAI VINTO!</p>
        <p id="tempoFinale" class="tempo-finale"></p>
      </div>
      <p id="messaggioMenu" class="messaggio-menu" role="status"></p>
      <a href="#" id="pulsanteSalvaPartita" class="oggettiMenu">Salva partita</a>
      <a href="#" id="pulsanteNuovaPartita" class="oggettiMenu">Nuova partita</a>
      <a href="../../html/classifica.html" target="_blank" rel="noopener" id="pulsanteClassifica" class="oggettiMenu">Classifica</a>
      <a href="../sessione/logout.php" id="pulsanteLogout" class="oggettiMenu">Logout</a>
    </div>
  </div>

  <script src="../../javascript/gestioneDB.js"></script>
  <script src="../../javascript/utilita.js"></script>
  <script src="../../javascript/gioco/GiocoMeccanica.js"></script>
  <script src="../../javascript/gioco/GiocoGestione.js"></script>
  <script src="../../javascript/gioco/GiocoPartita.js"></script>
</body>
</html>
