/*
 * Comunicazione con il server: tutte le richieste agli endpoint PHP passano da qui.
 * Le risposte del server sono sempre in formato JSON.
 */

// Errore restituito quando il server risponde con un codice diverso da 2xx
class ErroreServer extends Error {
  constructor(stato, dati) {
    super(`Il server ha risposto con il codice ${stato}`);
    this.stato = stato; // codice HTTP (es. 401 = sessione scaduta)
    this.dati = dati;   // eventuale corpo JSON della risposta
  }
}

/*
 * Esegue una richiesta all'endpoint indicato e restituisce una Promise con la risposta.
 * - senza datiDaInviare: richiesta GET (lettura);
 * - con datiDaInviare: richiesta POST con i dati in formato JSON.
 * "keepalive" permette alla richiesta di completarsi anche se l'utente cambia pagina
 * subito dopo (ad esempio salvataggio seguito dal logout).
 */
async function richiestaAPI(url, datiDaInviare) {
  const opzioni = { credentials: 'same-origin' };

  if (datiDaInviare !== undefined) {
    opzioni.method = 'POST';
    opzioni.headers = { 'Content-Type': 'application/json' };
    opzioni.body = JSON.stringify(datiDaInviare);
    opzioni.keepalive = true;
  }

  const risposta = await fetch(url, opzioni);

  let dati = null;
  try {
    dati = await risposta.json();
  } catch (errore) {
    // risposta vuota o non in formato JSON
  }

  if (!risposta.ok) {
    throw new ErroreServer(risposta.status, dati);
  }
  return dati;
}
