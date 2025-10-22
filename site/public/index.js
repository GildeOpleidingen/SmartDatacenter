// Function to determine the card title based on the device ID
function getCardTitle(deviceId) {
    if (deviceId.includes('power') || deviceId.includes('socket')) {
        return 'Stroomverbruik';
    } else if (deviceId.includes('temp') || deviceId.includes('humidity')) {
        return 'Klimaat Sensor';
    } else if (deviceId.includes('door')) {
        return 'Deur Status';
    } else {
        return 'Onbekende Sensor';
    }
}

// Function to generate the HTML content for a single sensor card
function generateCardContent(sensor) {
    let contentHTML = '';
    const lastUpdated = sensor.dateTime || 'Nog niet bekend';
    
    // Check for Power data (watt, volt)
    if (sensor.watt !== undefined && sensor.volt !== undefined) {
        contentHTML += `<p class="sensor-value">
                            Watt: <span class="watt">${sensor.watt} W</span><br>
                            Volt: <span class="volt">${sensor.volt} V</span>
                        </p>`;
    } else {
        // Build a content list from all available JSON properties
        contentHTML += '<ul class="data-list">';
        let dataFound = false;

        // Loop through all properties and display non-database columns
        for (const key in sensor) {
            // Exclude common database columns
            if (key !== 'ID' && key !== 'device_ID' && key !== 'dateTime' && key !== 'data') {
                dataFound = true;
                let value = sensor[key];
                let unit = '';

                // Add units and format specific values
                if (key === 'temperature') unit = ' °C';
                if (key === 'humidity') unit = ' %';
                if (key === 'status') value = (value == 1 || String(value).toLowerCase() === 'open') ? 'Open' : 'Gesloten';

                contentHTML += `<li><strong>${key.charAt(0).toUpperCase() + key.slice(1)}:</strong> ${value}${unit}</li>`;
            }
        }
        contentHTML += '</ul>';

        if (!dataFound) {
             contentHTML = `<p class="sensor-value">Geen leesbare data</p>`;
        }
    }
    
    return `
        <h2>${getCardTitle(sensor.deviceID)}</h2>
        <h3>(${sensor.deviceID})</h3>
        ${contentHTML}
        <small class="sensor-time">Laatste update: ${lastUpdated}</small>
    `;
}

// Main function to fetch data and update the dashboard
async function updateDashboard() {
    // Select the main container where cards will be placed
    const mainContainer = document.querySelector('main');
    
    try {
        const response = await fetch('api.php');
        if (!response.ok) {
            throw new Error('Netwerkfout: kon data niet ophalen.');
        }
        // sensorData is now an array of all active sensor objects
        const sensorData = await response.json(); 
        
        // Clear existing content
        mainContainer.innerHTML = ''; 

        if (sensorData.length === 0) {
            mainContainer.innerHTML = '<p class="text-center">Geen actieve sensoren gevonden in de database.</p>';
            return;
        }

        // Generate a card for every sensor received
        sensorData.forEach(sensor => {
            const card = document.createElement('div');
            card.className = 'sensor-card';
            card.innerHTML = generateCardContent(sensor);
            mainContainer.appendChild(card);
        });

    } catch (error) {
        console.error("Fout bij het bijwerken van het dashboard:", error);
        // Display a general error message if fetching fails
        if (mainContainer.innerHTML === '') {
             mainContainer.innerHTML = '<p class="text-center">Kon data niet ophalen. Controleer de API.</p>';
        }
    }
}

// Roep de functie meteen aan als de pagina laadt
updateDashboard();

// En stel een interval in om de data elke 10 seconden opnieuw op te halen
setInterval(updateDashboard, 10000);