/*
 * Funzioni di utilità condivise tra le pagine.
 */

// Converte un numero di secondi nel formato mm:ss (es. 125 -> "02:05")
function formattaTempo(secondi) {
  const minuti = Math.floor(secondi / 60);
  const secondiRimanenti = secondi % 60;
  return `${String(minuti).padStart(2, '0')}:${String(secondiRimanenti).padStart(2, '0')}`;
}
