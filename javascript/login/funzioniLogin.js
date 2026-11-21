/*
 * Login: controllo dei campi e invio delle credenziali al server.
 */

function mostraErroreLogin(testo) {
  document.getElementById('messaggioErrore').textContent = testo;
}

function gestioneInputLogin() {
  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;
  const bottone = document.getElementById('bottoneSubmit');

  mostraErroreLogin('');
  if (username === '' || password === '') {
    mostraErroreLogin('Inserisci username e password');
    return;
  }

  bottone.disabled = true; // evita invii multipli
  richiestaAPI('gestioneLogin.php', { username, password })
    .then(() => {
      window.location.href = '../partita/gioco.php';
    })
    .catch(errore => {
      // Messaggio unico: non si rivela se esiste l'utente o se è sbagliata la password
      mostraErroreLogin(errore.stato === 401
        ? 'Username o password non corretti'
        : 'Si è verificato un errore, riprova!');
      bottone.disabled = false;
    });
}
