// L'invio del form (click sul pulsante o tasto Invio) viene gestito da JavaScript
document.getElementById('formRegistrazione').addEventListener('submit', function (evento) {
  evento.preventDefault(); // impedisce il ricaricamento della pagina
  gestioneInputRegistrazione();
});
