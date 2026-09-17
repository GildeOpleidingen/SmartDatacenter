# SmartDatacenter
Example smartdatacenter.conf for apache2

<VirtualHost *:8080>
        ServerName smartdatacenter
        DocumentRoot /var/www/SmartDatacenter/public
        <Directory /var/www/SmartDatacenter/public>
                AllowOverride all
                Require all granted
        </Directory>
</VirtualHost>

<VirtualHost *:80>
        # The ServerName directive sets the request scheme, hostname and port that
        # the server uses to identify itself. This is used when creating
        # redirection URLs. In the context of virtual hosts, the ServerName
        # specifies what hostname must appear in the request's Host: header to
        # match this virtual host. For the default virtual host (this file) this
        # value is not decisive as it is used as a last resort host regardless.
        # However, you must set it for any further virtual host explicitly.
        #ServerName www.example.com

        ServerAdmin webmaster@localhost
        DocumentRoot /var/www/SmartDatacenter/public
        ServerName smartdatacenter
        # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
        # error, crit, alert, emerg.
        # It is also possible to configure the loglevel for particular
        # modules, e.g.
        #LogLevel info ssl:warn

        ErrorLog ${APACHE_LOG_DIR}/error.log
        CustomLog ${APACHE_LOG_DIR}/access.log combined

        # For most configuration files from conf-available/, which are
        # enabled or disabled at a global level, it is possible to
        # include a line for only one particular virtual host. For example the
        # following line enables the CGI configuration for this host only
        # after it has been globally disabled with "a2disconf".
        #Include conf-available/serve-cgi-bin.conf
    <Directory /var/www/SmartDatacenter/public>
        AllowOverride All
        Require all granted
    </Directory>

</VirtualHost>
# Requirements
Debian, Composer, Node JS, Apache2 Ngrok

You need to have an account on the things network and contact your product owner.

After installing all the requirements.
1. Setup a virtualhost in apache2 en make sure the DocumentRoot is directed to the public folder and also check the Directory.
2. Add a listen 8080 to the ports.conf file in apache2.
3. The site should run under localhost by default.
4. Run ```composer install``` & ```npm install```.
5. Create .env file and fill in the database credentials. These credentials you have to create yourself.
6. Set up NGrok with port 8080 with this command ngrok http 8080 --url https://ngrok-example.ngrok-free.dev
7. When you have an account at the things network and you have rights to Gilde-DataCenter. Then you need to add a webhook with the url you have got by NGROK.
8. Now The things network should communicate through NGrok with you local development

