/*
 * Classifica: legge i dati dal server e riempie la tabella.
 * I valori vengono inseriti con textContent, quindi un eventuale codice HTML
 * contenuto nei dati viene mostrato come testo e non eseguito.
 */

function mostraMessaggioClassifica(testo) {
  document.getElementById('messaggioClassifica').textContent = testo;
}

function creaTabella(classifica) {
  if (classifica.length === 0) {
    mostraMessaggioClassifica('Nessun giocatore ha ancora completato una partita.');
    return;
  }

  const corpoTabella = document.getElementById('righeClassifica');
  classifica.forEach(giocatore => {
    const riga = corpoTabella.insertRow();
    const valori = [
      giocatore.posizione,
      giocatore.username,
      giocatore.ultimoAccesso || '—',
      formattaTempo(giocatore.record),
    ];
    valori.forEach(valore => {
      riga.insertCell().textContent = valore;
    });
  });
}

richiestaAPI('../php/partita/statoClassifica.php')
  .then(creaTabella)
  .catch(errore => {
    mostraMessaggioClassifica(errore.stato === 401
      ? 'Effettua il login per vedere la classifica.'
      : 'Impossibile caricare la classifica, riprova più tardi.');
  });
