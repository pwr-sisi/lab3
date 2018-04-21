# sisi-l03

##SiSi - laboratorium 3. Serwer webowy - jak działa web API?

Do wykonania ćwiczeń z laboratorium potrzebujesz zainstalowanych aplikacji: VirtualBox i Vagrant.

Po pobraniu repozytorium uruchom maszynę vagranta: `vagrant up`. Gdy maszyna się skończy uruchamiać sprawdź czy możesz w przeglądarce odwiedzić adres: `http:localhost:8080`

Jeśli zobaczyłeś ekran powitalny serwera Apache - gratulacje, możesz iść dalej.

Wykorzystaj polecenie curl (w systemach Linux, Mac powinno być wbudowane, w systeme Windows zwykle jest dostępne w Powershellu) aby obejrzeć zawartość następujących stron:
```
curl http://localhost:8080/
curl http://localhost:8080/test.html
curl http://localhost:8080/test.php
```
Wybrane polecenia uruchom ponownie z opcją `-v`

curl http://localhost:8080/action.php
curl http://localhost:8080/action.php?imie=Wojtek
curl -X POST http://localhost:8080/action.php -d imie=Jan -d nazwisko=Kowalski
curl -X POST http://localhost:8080/action.php -d imie=Jan&nazwisko=Kowalski
curl -X POST http://localhost:8080/action.php?imie2=Iza  -d “imie=Wojtek”

curl -X POST http://localhost:8080/action-json.php -d "{\"name\":\"Wojtek\"}"  -H "Content-Type: application/json"




Modyfikacja konfiguracji serwera Apache:
vagrant ssh  
cd /etc/httpd/conf    
nano httpd.conf       #Ctrl-O - zapis, Ctrl-X - wyjście
sudo apachectl configtest   # sprawdzenie wprowadzonych zmian
sudo apachectl restart   # restart w celu uwzględnienia zmian

htpasswd -bc .htpass test password

curl http://localhost:8080/protected/  --user test:password
curl http://localhost:8080/protected  --user test:password
curl http://localhost:8080/protected  -L  --user test:password

<Directory /var/www/html/protected>
AuthType Basic
AuthName "Password Protected Area"
AuthUserFile /etc/httpd/.htpasswd
Require valid-user
</Directory>


Alias /this /vagrant/other

<Location /this>
   Options Indexes FollowSymLinks
   Require all granted
</Location>
<Directory "/var/www/html/rewrite">
    RewriteEngine On
    RewriteBase "/rewrite/"
    RewriteRule  ^user/([^/]*)$ /action.php?name=$1
</Directory>
