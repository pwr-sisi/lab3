# sisi-l03

##SiSi - laboratorium 3. Serwer webowy - jak działa web API?

Do wykonania ćwiczeń z laboratorium potrzebujesz zainstalowanych aplikacji: VirtualBox i Vagrant.

Po pobraniu repozytorium uruchom maszynę vagranta: `vagrant up`. Gdy maszyna się skończy uruchamiać sprawdź czy możesz w przeglądarce odwiedzić adres: `http:localhost:8080`

Jeśli zobaczyłeś ekran powitalny serwera Apache - gratulacje, możesz iść dalej.

### Strony statyczne

Wykorzystaj polecenie curl (w systemach Linux i Mac powinno być wbudowane, w systeme Windows zwykle jest dostępne w Powershellu) aby obejrzeć zawartość następujących stron:
```
curl http://localhost:8080/
curl http://localhost:8080/test.html
curl http://localhost:8080/test.php
```
Wybrane polecenia uruchom ponownie z opcją `-v`

Obejrzyj podane strony przy pomocy przeglądarki przy włączonych narzędziach deweloperskich (Firefox - Ctrl-Shift-I). Szczególną uwagę zwróć na zakładkę *Network*

### Formularze i przekazywanie parametrów
Używając przeglądarki otwórz stronę form.html, wypełnij formularz i wyślij go. Zobacz co wyświetla strona `action.html` do której wysyłane są dane formularza.

Wyślij dane formularza przy pomocy polecenia `curl`:
```
curl http://localhost:8080/action.php
curl http://localhost:8080/action.php?imie=Wojtek
curl -X POST http://localhost:8080/action.php -d "imie=Jan" -d "nazwisko=Kowalski"
curl -X POST http://localhost:8080/action.php -d "imie=Jan&nazwisko=Kowalski"
curl -X POST http://localhost:8080/action.php?imie2=Iza  -d "imie=Wojtek"
```
Wypróbuj również przesyłanie danych formularza przy użyciu formatu JSON:
```
curl -X POST http://localhost:8080/action-json.php -d "{\"name\":\"Wojtek\"}"  -H "Content-Type: application/json"
```
Zwróć uwagę, że przy danych formularza program curl stosuje domyślnie ustawienie `x-url-encoded`.

### Modyfikacja działania serwera www - nie zawsze to co widzisz wygląda tak jak Ci się wydaje

Aby zmienić konfigurację serwera Apache w konsoli przejdź do folderu w którym jest plik `Vagrantfile` a następnie wykonaj polecenie `vagrant ssh`.

Będąc w maszynie wirtualnej wykonaj polecenia
```
sudo nano /etc/httpd/conf/httpd.conf
```
nano to prosty edytor tekstu. dwa najważniejsze polecenia to: Ctrl-O - zapis pliku, Ctrl-X - wyjście.

Przed restartem serwera www w celu uwzglęnienia zmian dobrze jest sprawdzić czy nie zrobilismy w pliku httpd.conf błędów:
```
sudo apachectl configtest   # sprawdzenie wprowadzonych zmian
```
i jeśli wszystko jest OK zrestartować serwer aby uwzglednić zmiany:
```
sudo apachectl restart
```
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
