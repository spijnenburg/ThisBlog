# Thisblog

Thisblog is een simpele blog geschreven in PHP bedoeld als studieopdracht. Een gebruiker kan inschrijven, inloggen en blogposts aanmaken of bewerken. Hierbij is het mogelijk om HTML in de blogpost te zetten. Een admin account kan de blogposts beheren.

# Getting Started 

## AMP stack
ThisBlog heeft een AMP stack nodig om te kunnen functioneren. Zorg dat de volgende programma's zijn geinstalleerd en correct geconfigureerd:

- PHP
- MySQL
- Apache

Een stack zoals [Laragon](https://laragon.org) wordt aanbevolen.

## Configureren
1. Maak een gebruiker en database aan in MySQL.
2. Open het script "/includes/db-connectie.php" en vul hier de juiste gegevens in. Houd er rekening mee dat dit niet afgeschermd is.
```
$host = "localhost";
$user = "myname";
$password = "mypassword";
$database = "ThisBlog";
```
3. Browse naar index.php. Zie je een foutmelding dat er geen verbinding gemaakt kan worden, controleer dan bovenstaande stappen. Krijg je een foutmelding dat de tabellen niet zijn gevonden. Ga dan naar de volgende stap om de tabellen toe te voegen.
4. Open setup.php en klik op de button. De database tabellen worden toegevoegd.
5. Verwijder setup.php.
6. Gebruikers kunnen toegevoegd worden. Geef een gebruiker de naam admin om adminrechten toe te wijzen. Slechts 1 gebruiker kan admin zijn.

# Licentie

MIT licentie.