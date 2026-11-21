-- -----------------------------------------------------------------------------
-- Shifting Puzzle: schema del database (MySQL 5.7+ / MariaDB 10.2+)
--
-- Si importa da phpMyAdmin (scheda "Importa") oppure da terminale con:
--   mysql -u root -p < sql/shifting_puzzle.sql
--
-- ATTENZIONE: se il database esiste già viene eliminato e ricreato.
-- -----------------------------------------------------------------------------

DROP DATABASE IF EXISTS `shifting_puzzle`;

CREATE DATABASE `shifting_puzzle`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `shifting_puzzle`;

-- -----------------------------------------------------------------------------
-- Utenti registrati, con l'eventuale partita salvata e il miglior tempo
-- -----------------------------------------------------------------------------
CREATE TABLE `utente` (
  `username`     VARCHAR(16)  COLLATE utf8mb4_bin NOT NULL,  -- confronto sensibile a maiuscole/minuscole
  `password`     VARCHAR(255) NOT NULL,                      -- hash generato con password_hash()
  `statoPartita` VARCHAR(64)  NULL DEFAULT NULL,             -- griglia salvata in JSON (NULL = nessuna partita in corso)
  `ultimoTempo`  TIME         NULL DEFAULT NULL,             -- tempo trascorso nella partita salvata
  `tempoRecord`  TIME         NULL DEFAULT NULL,             -- miglior tempo (NULL = nessuna partita vinta)
  PRIMARY KEY (`username`)
) ENGINE = InnoDB;

-- -----------------------------------------------------------------------------
-- Registro delle sessioni: una riga per ogni login,
-- completata con l'ora di fine al logout o alla scadenza della sessione
-- -----------------------------------------------------------------------------
CREATE TABLE `logtable` (
  `id`                      INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `utente`                  VARCHAR(16)  COLLATE utf8mb4_bin NOT NULL,
  `timestampInizioSessione` DATETIME     NOT NULL,
  `timestampFineSessione`   DATETIME     NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_logtable_utente` (`utente`),
  CONSTRAINT `fk_logtable_utente`
    FOREIGN KEY (`utente`) REFERENCES `utente` (`username`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE = InnoDB;
