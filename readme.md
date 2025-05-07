# Instalación Ubuntu

* Crear certificado autofirmado SSL
* Configurar VirtualHost y SSL dentro de /etc/apache2/sites-available 
* Crear la DDBB
* Crear un archivo llamado .env a partir del llamado .env.example
* Crear link simbólico desde la carpeta storage hacia la public/storage, usando paths absolutos, por ej:
    - ln -s /var/www/html/optus/storage /var/www/html/optus/public/storage
* Dar permisos a las carpetas storage y logs para que la aplicación pueda escribir en ellas
    - Para Windows usar MKLink, mklink /d \MyFolder \Users\User1\Documents
    ej. Mklink /D "H:\WebSitios\optus\public\storage" "H:\WebSitios\optus\storage"

# Tareas

* Iniciar Websocket de Subasta Online: "composer cli AuctionTask"
* Ejecutar validación de fechas: "composer cli DatesLimitTask"

# Tareas CRON
    - 0 0 * * * cd /var/www/html/optus && /usr/local/bin/composer cli DatesLimitTask
    - @reboot cd /var/www/html/optus && /usr/local/bin/composer cli AuctionTask
    