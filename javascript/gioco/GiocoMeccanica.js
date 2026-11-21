/*
 * Meccanica del Gioco del Quindici: griglia, mosse, vittoria, risolvibilità e timer.
 *
 * La griglia è un array di 16 numeri letto riga per riga: l'indice i corrisponde
 * alla riga Math.floor(i / 4) e alla colonna i % 4. Lo 0 rappresenta la cella vuota.
 */

const LATO = 4;
const GRIGLIA_RISOLTA = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 0];

let grigliaGioco = [];
let comandiInvertiti = false;
let intervalloTimer = null;
let tempoTrascorso = 0;      // secondi
let mosseAbilitate = false;


//
// GRIGLIA
//
function creaCelle(scacchiera) {
  for (let i = 0; i < LATO * LATO; i++) {
    const cella = document.createElement('div');
    cella.classList.add('cella');
    cella.dataset.indice = i; // posizione della cella nella griglia
    cella.addEventListener('click', gestisciMossaClick);
    scacchiera.append(cella);
  }
}

// Mostra a schermo il contenuto di grigliaGioco
function disegnaGriglia() {
  document.querySelectorAll('.cella').forEach((cella, i) => {
    const valore = grigliaGioco[i];
    cella.textContent = valore === 0 ? '' : valore;
    cella.classList.toggle('nera', valore === 0);
  });
}

// Mescola i numeri (algoritmo di Fisher-Yates) finché la configurazione non è risolvibile
function generaGrigliaGioco() {
  do {
    grigliaGioco = Array.from({ length: LATO * LATO }, (_, i) => i); // 0, 1, ..., 15
    for (let i = grigliaGioco.length - 1; i > 0; i--) {
      const j = Math.floor(Math.random() * (i + 1));
      [grigliaGioco[i], grigliaGioco[j]] = [grigliaGioco[j], grigliaGioco[i]];
    }
  } while (!risolvibile(grigliaGioco) || grigliaRisolta());
}

// Vittoria: numeri da 1 a 15 in ordine crescente e cella vuota in fondo
function grigliaRisolta() {
  return grigliaGioco.every((valore, i) => valore === GRIGLIA_RISOLTA[i]);
}


//
// MOSSE
//
function abilitaMosse() {
  mosseAbilitate = true;
}

function disabilitaMosse() {
  mosseAbilitate = false;
}

function invertiComandi() {
  comandiInvertiti = !comandiInvertiti;
}

/*
 * Frecce della tastiera.
 * Modalità normale: la freccia indica dove si sposta la cella vuota.
 * Comandi invertiti: la freccia indica dove si sposta la casella adiacente alla cella vuota.
 */
function gestisciMossaTastiera(evento) {
  const direzioni = comandiInvertiti
    ? { ArrowUp: 'Su', ArrowDown: 'Giu', ArrowLeft: 'SX', ArrowRight: 'DX' }
    : { ArrowUp: 'Giu', ArrowDown: 'Su', ArrowLeft: 'DX', ArrowRight: 'SX' };
  const direzione = direzioni[evento.key];

  if (direzione === undefined || !mosseAbilitate || menuAperto()) {
    return;
  }
  evento.preventDefault(); // evita lo scorrimento della pagina
  eseguiMossa(direzione);
}

// Click su una casella: si muove solo se è adiacente (non in diagonale) alla cella vuota
function gestisciMossaClick(evento) {
  if (!mosseAbilitate) {
    return;
  }
  const indiceCliccato = Number(evento.currentTarget.dataset.indice);
  const indiceVuoto = grigliaGioco.indexOf(0);

  const rigaCliccata = Math.floor(indiceCliccato / LATO);
  const colonnaCliccata = indiceCliccato % LATO;
  const rigaVuota = Math.floor(indiceVuoto / LATO);
  const colonnaVuota = indiceVuoto % LATO;

  if (Math.abs(rigaCliccata - rigaVuota) + Math.abs(colonnaCliccata - colonnaVuota) !== 1) {
    return;
  }

  if (rigaCliccata > rigaVuota) {
    eseguiMossa('Su');
  } else if (rigaCliccata < rigaVuota) {
    eseguiMossa('Giu');
  } else if (colonnaCliccata > colonnaVuota) {
    eseguiMossa('SX');
  } else {
    eseguiMossa('DX');
  }
}

/*
 * Sposta nella cella vuota la casella adiacente che si muove nella direzione indicata:
 * "Su" = la casella sotto la cella vuota sale, "Giu" = quella sopra scende,
 * "SX" = quella a destra va a sinistra, "DX" = quella a sinistra va a destra.
 * Restituisce false se la mossa non è possibile (la cella vuota è sul bordo).
 */
function spostaDirezione(direzione) {
  const posizioneCasella = { Su: [1, 0], Giu: [-1, 0], SX: [0, 1], DX: [0, -1] }; // rispetto alla cella vuota
  const indiceVuoto = grigliaGioco.indexOf(0);
  const riga = Math.floor(indiceVuoto / LATO) + posizioneCasella[direzione][0];
  const colonna = (indiceVuoto % LATO) + posizioneCasella[direzione][1];

  if (riga < 0 || riga >= LATO || colonna < 0 || colonna >= LATO) {
    return false;
  }

  const indiceCasella = riga * LATO + colonna;
  grigliaGioco[indiceVuoto] = grigliaGioco[indiceCasella];
  grigliaGioco[indiceCasella] = 0;
  return true;
}

function eseguiMossa(direzione) {
  if (!spostaDirezione(direzione)) {
    return;
  }
  disegnaGriglia();
  if (grigliaRisolta()) {
    partitaVinta(); // definita in GiocoGestione.js
  }
}


//
// CONTROLLO DI RISOLVIBILITÀ
// Per una griglia 4x4 la configurazione è risolvibile se:
// - la cella vuota è su una riga pari contando dal basso e le inversioni sono dispari, oppure
// - la cella vuota è su una riga dispari contando dal basso e le inversioni sono pari.
//
function contaInversioni(griglia) {
  let inversioni = 0;
  for (let i = 0; i < griglia.length; i++) {
    for (let j = i + 1; j < griglia.length; j++) {
      // un numero più grande che precede uno più piccolo (la cella vuota non conta)
      if (griglia[i] !== 0 && griglia[j] !== 0 && griglia[i] > griglia[j]) {
        inversioni++;
      }
    }
  }
  return inversioni;
}

function trovaRigaVuotaDalBasso(griglia) {
  return LATO - Math.floor(griglia.indexOf(0) / LATO); // 1 = ultima riga
}

function risolvibile(griglia) {
  const inversioni = contaInversioni(griglia);
  if (trovaRigaVuotaDalBasso(griglia) % 2 === 0) {
    return inversioni % 2 !== 0;
  }
  return inversioni % 2 === 0;
}


//
// TIMER
//
function avviaTimer() {
  fermaTimer();
  aggiornaTimer();
  intervalloTimer = setInterval(() => {
    tempoTrascorso++;
    aggiornaTimer();
  }, 1000);
}

function fermaTimer() {
  clearInterval(intervalloTimer);
  intervalloTimer = null;
}

function aggiornaTimer() {
  document.getElementById('timer').textContent = formattaTempo(tempoTrascorso);
}
