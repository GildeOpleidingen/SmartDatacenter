# Smart Datacenter Sensors

## Power Socket
**ID:** `powersocket-001`

| Property | Unit | Description |
|----------|------|-------------|
| Current | mA | The amount of electrical current currently flowing through the power socket. |
| Voltage | V | The electrical voltage currently measured by the power socket. |
| Power | W | The amount of electrical power currently being consumed. |
| Power Factor | % | Indicates how efficiently the electrical power is being used. A higher percentage generally means more efficient use of the supplied power. |
| Total Energy | Wh | The total amount of electrical energy measured by the socket since the energy counter was started or reset. |
| State | — | Indicates the current state of the power socket's switch/relay. |

---

## Temperature & Humidity Sensor
**ID:** `temphumidity-001`

| Property | Unit | Description |
|----------|------|-------------|
| Temp DS | °C | Temperature measured by the DS-series temperature sensor. |
| Temp SHT | °C | Temperature measured by the SHT sensor. |
| Humidity | % | Measures the relative humidity of the surrounding air. |
| External Sensor | — | Indicates whether an external sensor is enabled or detected. |
| System Timestamp | — | A timestamp representing when the measurement or system event occurred. Usually stored as a Unix timestamp. |

---

## Sound Level Sensor
**ID:** `soundlevelsensor-001`

| Property | Unit | Description |
|----------|------|-------------|
| La | dB | The current A-weighted sound level measured by the sensor. |
| Laeq | dB | The equivalent continuous A-weighted sound level over a specific measurement period. It represents the average sound energy during that period. |
| Lamax | dB | The highest A-weighted sound level measured during the measurement period. |
| Freq Weight | — | Defines the frequency weighting used for the sound measurement. For example, A-weighting adjusts the measurement to better represent human hearing. |
| Time Weight | — | Defines how quickly the sound-level measurement responds to changes in sound. Examples include Fast, Slow, and Impulse. |
| Battery | % | Indicates the remaining battery level of the sensor, normally represented as a percentage. |

---

## Indoor Ambient Monitoring Sensor
**ID:** `indoorambiencemonitoringsensor`

| Property | Unit | Description |
|----------|------|-------------|
| Co2 | ppm | Measures the concentration of carbon dioxide in the surrounding air. |
| PM10 | — | Measures the concentration of particulate matter with a diameter of 10 micrometers or smaller. |
| PM2.5 | — | Measures the concentration of fine particulate matter with a diameter of 2.5 micrometers or smaller. |
| Humidity | % | Measures the relative humidity of the surrounding air. |
| Pressure | hPa | Measures the atmospheric pressure surrounding the sensor. |
| Light Level | — | Measures the amount of ambient light detected by the sensor. The exact unit depends on the sensor. |
| TVOC | — | Measures the total amount of volatile organic compounds detected in the air. The exact unit depends on the sensor. |
| Battery | % | Indicates the remaining battery level of the sensor. |

---

## Motion Sensor
**ID:** `motion-001`

| Property | Unit | Description |
|----------|------|-------------|
| Motion | — | Indicates whether motion is currently being detected. |
| Counter | — | Counts the number of motion events detected by the sensor since the counter was started or reset. |
| Battery Voltage | V | Measures the current voltage supplied by the sensor's battery. |
| Tamper | — | Indicates whether the sensor has detected an attempt to tamper with, open, or interfere with the device. |
| Time | — | A device-specific timing or status parameter. Its exact meaning depends on the implementation of the motion sensor. |

---

## Door Sensor
**ID:** `deursensor-002`

| Property | Unit | Description |
|----------|------|-------------|
| Door State | — | Indicates whether the door is currently open or closed. |
| Times Opened | — | Counts how many times the door has been detected opening since the counter was started or reset. |
| Last Open Duration | s | Records how long the door remained open during its most recent opening event. |
| Battery Voltage | V | Measures the current voltage supplied by the sensor's battery. |
| Alarm | — | Indicates whether an alarm condition has been detected by the door sensor. |

---

## Door Sensor
**ID:** `deursensor-003`

| Property | Unit | Description |
|----------|------|-------------|
| Door State | — | Indicates whether the door is currently open or closed. |
| Times Opened | — | Counts how many times the door has been detected opening since the counter was started or reset. |
| Last Open Duration | s | Records how long the door remained open during its most recent opening event. |
| Battery Voltage | V | Measures the current voltage supplied by the sensor's battery. |
| Alarm | — | Indicates whether an alarm condition has been detected by the door sensor. |