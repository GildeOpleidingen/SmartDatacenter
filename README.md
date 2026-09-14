# SmartDatacenter

# Dependencies & setup
1. Install composer & node.js.
2. Run ```composer install``` & ```npm install```.
3. Create .env file and fill in credentials.

Database credentials:

host: phpmyadmin.gdcs.nl Je moet het volgende ip-adres gebruiken voor de server: 10.250.0.103

username: smartdatacenter

wachtwoord: HvV7tRqxM9nB17.W

Ngrok (link)
ngrok: https://dashboard.ngrok.com/get-started/gateway 

stap 2. 
Your_Authoken kan je vinden in
https://dashboard.ngrok.com/get-started/your-authtoken

strap 3 moet je command
ngrok http 127.0.0.1:80 --host-header=smartdatacenter --url=https://zit-matchbook-bloomers.ngrok-free.dev 

cloud.thethings -> naar GildeDataCenter -> Webhooks
add webhooks 
- url https://zit-matchbook-bloomers.ngrok-free.dev/
  
add filter event data
-end_deivce_ids
-up.downlink_ack.decoded_payload
-up.uplink_message.settings.timestamp

enabled event types
vink uplink message /api/webhook/ttn

