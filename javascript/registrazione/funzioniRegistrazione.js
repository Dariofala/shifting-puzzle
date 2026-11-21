/*
 * Registrazione: controllo dei campi e invio dei dati al server.
 * Le regole sono le stesse verificate dal server in gestioneRegistrazione.php.
 */

const REGOLA_USERNAME = /^[A-Za-z0-9._-]{8,16}$/;

function mostraErroreRegistrazione(testo) {
  document.getElementById('messaggioErrore').textContent = testo;
}

// Restituisce il messaggio di errore, oppure null se i dati sono validi
function controllaInputRegistrazione(username, password, ripetiPassword) {
  if (!REGOLA_USERNAME.test(username)) {
    return "L'username deve avere da 8 a 16 caratteri tra lettere, numeri, punto, trattino e underscore";
  }
  if (password.length < 8 || password.length > 72) {
    return 'La password deve avere da 8 a 72 caratteri';
  }
  if (/\s/.test(password)) {
    return 'La password non deve contenere spazi';
  }
  if (!/[0-9]/.test(password)) {
    return 'La password deve contenere almeno un numero';
  }
  if (password === username) {
    return 'Username e password devono essere diversi';
  }
  if (password !== ripetiPassword) {
    return 'Le due password non coincidono';
  }
  return null;
}

function gestioneInputRegistrazione() {
  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;
  const ripetiPassword = document.getElementById('ripetiPassword').value;
  const bottone = document.getElementById('bottoneSubmit');

  const errore = controllaInputRegistrazione(username, password, ripetiPassword);
  mostraErroreRegistrazione(errore || '');
  if (errore !== null) {
    return;
  }

  bottone.disabled = true;
  richiestaAPI('gestioneRegistrazione.php', { username, password, ripetiPassword })
    .then(() => {
      window.location.href = 'login.php?registrato=1';
    })
    .catch(errore => {
      if (errore.stato === 409) {
        mostraErroreRegistrazione('Username già in uso, scegline un altro');
      } else if (errore.stato === 400 && errore.dati && errore.dati.messaggio) {
        mostraErroreRegistrazione(errore.dati.messaggio);
      } else {
        mostraErroreRegistrazione('Si è verificato un errore, riprova!');
      }
      bottone.disabled = false;
    });
}
