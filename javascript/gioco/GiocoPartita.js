// Avvio della pagina di gioco
creaCelle(document.getElementById('griglia'));
impostaPulsanti();
window.addEventListener('keydown', gestisciMossaTastiera);
caricaPartita();
