/*
 * Gestione della partita: caricamento e salvataggio sul server, nuova partita,
 * vittoria, record e menu a tendina.
 */

//
// AVVIO: riprende la partita salvata oppure ne inizia una nuova
//
function caricaPartita() {
  richiestaAPI('statoPartita.php')
    .then(stato => {
      mostraRecord(stato.record);
      if (Array.isArray(stato.griglia)) {
        grigliaGioco = stato.griglia;
        tempoTrascorso = stato.tempo || 0;
        disegnaGriglia();
        abilitaMosse();
        avviaTimer();
      } else {
        nuovaPartita();
      }
    })
    .catch(gestisciErroreServer);
}

function gestisciErroreServer(errore) {
  if (errore.stato === 401) {
    window.location.href = '../account/login.php'; // sessione scaduta
    return;
  }
  mostraMessaggioMenu('Errore di comunicazione con il server, riprova.');
  apriMenuTendina();
}


//
// NUOVA PARTITA
//
function nuovaPartita() {
  document.body.classList.remove('vittoria');
  document.getElementById('messaggioVittoria').hidden = true;
  document.getElementById('nuovoRecord').hidden = true;
  document.getElementById('ciao').hidden = false;
  mostraMessaggioMenu('');

  generaGrigliaGioco();
  disegnaGriglia();
  tempoTrascorso = 0;
  abilitaMosse();
  avviaTimer();
  chiudiMenuTendina();
}


//
// SALVATAGGIO
//
function partitaInCorso() {
  return mosseAbilitate && !grigliaRisolta();
}

// Salva griglia e tempo in un'unica richiesta. Restituisce una Promise.
function salvaPartita() {
  return richiestaAPI('salvaPartita.php', { griglia: grigliaGioco, tempo: tempoTrascorso });
}


//
// VITTORIA E RECORD
//
function partitaVinta() {
  disabilitaMosse();
  fermaTimer();
  aggiornaTimer();

  document.body.classList.add('vittoria');
  document.getElementById('ciao').hidden = true;
  document.getElementById('tempoFinale').textContent = `Tempo: ${formattaTempo(tempoTrascorso)}`;
  document.getElementById('messaggioVittoria').hidden = false;
  mostraMessaggioMenu('');
  apriMenuTendina();

  // Il server chiude la partita salvata e aggiorna il record se il tempo è migliore
  richiestaAPI('salvaRecord.php', { tempo: tempoTrascorso })
    .then(risposta => {
      mostraRecord(risposta.record);
      document.getElementById('nuovoRecord').hidden = !risposta.nuovoRecord;
    })
    .catch(gestisciErroreServer);
}

function mostraRecord(secondi) {
  document.getElementById('tempoRecord').textContent = secondi === null ? '--:--' : formattaTempo(secondi);
}


//
// MENU A TENDINA
//
function apriMenuTendina() {
  const menu = document.getElementById('menuTendina');
  menu.classList.add('aperto');
  menu.setAttribute('aria-hidden', 'false');
  document.getElementById('navMenu').setAttribute('aria-expanded', 'true');
}

function chiudiMenuTendina() {
  const menu = document.getElementById('menuTendina');
  menu.classList.remove('aperto');
  menu.setAttribute('aria-hidden', 'true');
  document.getElementById('navMenu').setAttribute('aria-expanded', 'false');
}

function menuAperto() {
  return document.getElementById('menuTendina').classList.contains('aperto');
}

function mostraMessaggioMenu(testo) {
  document.getElementById('messaggioMenu').textContent = testo;
}


//
// PULSANTI
//
function impostaPulsanti() {
  document.getElementById('navMenu').addEventListener('click', apriMenuTendina);
  document.getElementById('pulsanteChiudi').addEventListener('click', chiudiMenuTendina);

  document.getElementById('bottoneDocumentazione').addEventListener('click', () => {
    window.open('../../html/documentazione.html', '_blank', 'noopener');
  });

  document.getElementById('bottoneInversioneComandi').addEventListener('click', evento => {
    invertiComandi();
    evento.currentTarget.setAttribute('aria-pressed', String(comandiInvertiti));
    evento.currentTarget.classList.toggle('attivo', comandiInvertiti);
  });

  document.getElementById('pulsanteSalvaPartita').addEventListener('click', evento => {
    evento.preventDefault();
    if (!partitaInCorso()) {
      mostraMessaggioMenu('Nessuna partita in corso da salvare.');
      return;
    }
    salvaPartita()
      .then(() => mostraMessaggioMenu('Partita salvata!'))
      .catch(gestisciErroreServer);
  });

  document.getElementById('pulsanteNuovaPartita').addEventListener('click', evento => {
    evento.preventDefault();
    nuovaPartita();
  });

  // Logout: prima si salva la partita in corso, poi si chiude la sessione
  document.getElementById('pulsanteLogout').addEventListener('click', evento => {
    evento.preventDefault();
    const destinazione = evento.currentTarget.href;
    const salvataggio = partitaInCorso() ? salvaPartita() : Promise.resolve();
    disabilitaMosse();
    fermaTimer();
    salvataggio
      .catch(() => {}) // anche se il salvataggio fallisce l'utente deve poter uscire
      .finally(() => { window.location.href = destinazione; });
  });

  // Il tasto Esc chiude il menu
  document.addEventListener('keydown', evento => {
    if (evento.key === 'Escape' && menuAperto()) {
      chiudiMenuTendina();
    }
  });
}
