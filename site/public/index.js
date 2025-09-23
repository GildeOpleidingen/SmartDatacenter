// Functie om de data op te halen en de pagina bij te werken
async function updateDashboard() {
    try {
        // Roep ons eigen api.php script aan
        const response = await fetch('api.php');
        if (!response.ok) {
            throw new Error('Netwerkfout: kon data niet ophalen.');
        }
        const sensordata = await response.json();

        // Loop door de ontvangen data
        sensordata.forEach(meting => {
            if (meting.meting_type === 'temperature') {
                document.getElementById('temperature-value').textContent = `${meting.waarde} °C`;
                document.getElementById('temperature-time').textContent = `Laatste update: ${meting.tijdstip}`;
            } else if (meting.meting_type === 'humidity') {
                document.getElementById('humidity-value').textContent = `${meting.waarde} %`;
                document.getElementById('humidity-time').textContent = `Laatste update: ${meting.tijdstip}`;
            } else if (meting.meting_type === 'door_status') {
                // Voorbeeld voor een deursensor die 0 (dicht) of 1 (open) teruggeeft
                const statusText = meting.waarde == 1 ? 'Open' : 'Gesloten';
                document.getElementById('door-value').textContent = statusText;
                document.getElementById('door-time').textContent = `Laatste update: ${meting.tijdstip}`;
            }
        });

    } catch (error) {
        console.error("Fout bij het bijwerken van het dashboard:", error);
        // Optioneel: toon een foutmelding op de pagina
    }
}

// Roep de functie meteen aan als de pagina laadt
updateDashboard();

// En stel een interval in om de data elke 10 seconden (10000 milliseconden) opnieuw op te halen
setInterval(updateDashboard, 10000);