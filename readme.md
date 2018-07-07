# Thisblog

Thisblog is een simpele blog geschreven in PHP bedoeld als studieopdracht. Een gebruiker kan inschrijven, inloggen en blogposts aanmaken of bewerken. Hierbij is het mogelijk om HTML in de blogpost te zetten. Een admin account kan de blogposts beheren.

## Eerste keer opstarten

### Vooraf geinstalleerd
ThisBlog is een PHP applicatie en heeft een AMP stack om te kunnen functioneren. Zorg dat de volgende programma's zijn geinstalleerd en correct geconfigureerd:

- PHP
- MySQL
- Apache

De AMP stack van [Laragon](https://laragon.org) wordt aanbevolen.

### Installeren
1. Gebruik een bestaande MySQL gebruiker of maak een nieuwe aan. Het wachtwoord van de database wordt onbeveiligd opgeslagen in het bestand db-connectie.php. Houd er rekening mee dat derden dit kunnen lezen.
```
CREATE USER 'user'@'hostname';
GRANT ALL PRIVILEGES ON thisblog.* To 'user'@'hostname' IDENTIFIED BY 'password';
FLUSH PRIVILEGES;
```
2. Ga naar 'hostname'/setup.php en vul de gegevens van de MySQL gebruiker in.
3. Klik op 'verwijder dit bestand' om het installatiebestand te verwijderen.
4. Gebruikers kunnen nu toegevoegd worden. Geef een gebruiker de naam admin om adminrechten toe te wijzen. Slechts 1 gebruiker kan admin zijn.

## Licentie

MIT licentie.