# SmartDatacenter

Dit project is een webgebaseerd dashboard voor het visualiseren van real-time data van LoRaWAN-sensoren in een datacenter. De applicatie is gebouwd met PHP, HTML, CSS en JavaScript en maakt gebruik van een Object-Georiënteerde (OOP) architectuur voor een schone scheiding van code en functionaliteit.

## Functies

- **Webhook-integratie**: Ontvangt sensordata direct van een LoRaWAN-netwerkserver via een webhook.
- **Dataopslag**: Slaat de ontvangen data op in een relationele MySQL-database.
- **API-endpoint**: Levert de meest recente sensordata aan de frontend in JSON-formaat.
- **Real-time weergave**: Toont de data op een webpagina die automatisch wordt ververst.

## Vereisten

Om dit project te draaien, heb je het volgende nodig:

- Een webserver met **PHP 8.0** of hoger (bijv. XAMPP, WAMP, of een Apache/Nginx-server).
- **MySQL**- of MariaDB-database.
- **Composer** geïnstalleerd.

## Installatie

Volg deze stappen om het project lokaal op te zetten en te draaien.

1.  **Kloon de repository**:
    ```bash
    git clone [jouw-git-repository-url]
    cd [jouw-project-map]
    ```

2.  **Installeer Composer-afhankelijkheden**:
    Navigeer naar de map `site` en installeer de afhankelijkheden. Dit genereert de benodigde `vendor`-map met de autoloader.
    ```bash
    cd site
    composer install
    ```

3.  **Database instellen**:
    Maak een MySQL-database aan met de naam `datacenter_db`. Voer vervolgens de SQL-commando's uit om de benodigde tabellen aan te maken.
    - `sensors`
    - `temperature_readings`
    - `door_readings`

4.  **Configureer de databaseverbinding**:
    Open het bestand `src/Config/DatabaseConfig.php` en pas de database-instellingen aan met jouw gebruikersnaam en wachtwoord.

5.  **Configureer de Webhook**:
    Geef de beheerder van het LoRaWAN-netwerk de URL van je webhook-endpoint: `http://jouw-server.nl/webhook.php`. Dit endpoint zal de binnenkomende sensordata ontvangen en in de database opslaan.

## Gebruik

Zodra alle stappen zijn voltooid en de webserver draait, kun je de website openen via de URL van je server: `http://jouw-server.nl/`. De website zal automatisch de data ophalen en weergeven.