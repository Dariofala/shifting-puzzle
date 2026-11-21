<?php
/*
 * Logout: registra la fine della sessione, la distrugge e torna alla pagina di login.
 * (Il salvataggio della partita in corso avviene prima, dal client: vedi GiocoGestione.js)
 */

require_once __DIR__ . '/sessione.php';

chiudiSessione();
header('Location: ../account/login.php');
exit;
