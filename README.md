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
nano to prosty edytor tekstu, a jego dwa najważniejsze polecenia to: Ctrl-O - zapis pliku, Ctrl-X - wyjście.

Przed restartem serwera www w celu uwzglęnienia zmian sprawdź czy nie zrobiłeś w pliku httpd.conf błędów (i popraw je przed restartem serwera):
```
sudo apachectl configtest   # sprawdzenie wprowadzonych zmian
```
Jeśli wszystko jest OK uruchom ponownie serwer aby uwzglednić zmiany:
```
sudo apachectl restart
```

### Proste przekierowanie zawartości 

W pliku httpd.conf dodaj następujące dyrektywy (poniżej sekcji `<Directory /var/www/html>...</Directory>`):
```
Alias /this /vagrant/other

<Location /this>
   Options Indexes FollowSymLinks
   Require all granted
</Location>
```
Teraz wszystkie zapytania kierowane pod adresem `https://localhost:8080/this` pobierają tak naprawdę zawartość z folderu `other`.


### Ochrona strony hasłem

Do pliku httpd.conf dodaj ochronę folderu `protected` przy pomocy hasła (wstaw fragment poniżej poprzedniej dyrektywy):
```
<Directory /var/www/html/protected>
AuthType Basic
AuthName "Password Protected Area"
AuthUserFile /etc/httpd/.htpasswd
Require valid-user
</Directory>
```
Dyrektywa mówi, że zawartość folderu `/protected` jest udostępniana po zalogowaniu użytkownika.
Dodatkowo musisz jeszcze utworzyć plik z hasłami:
```
htpasswd -bc /etc/httpd/.htpass test password
```
Powyższe polecenie zakłada nowy plik (opcja `-c`) i umieszcza w nim nazwę użytkownika oraz skrót hasła.

Spróbuj przy pomocy przeglądarki otworzyć stronę `http://localhost:8080/protected/`, a następnie to samo zrobić przy pomocy programu curl:
```
curl http://localhost:8080/protected/
curl http://localhost:8080/protected/  --user test:password
curl http://localhost:8080/protected  --user test:password
curl http://localhost:8080/protected  -L  --user test:password
```

### Przepisywanie adresów

Nie zawsze pliki dają się zapisać tak jakbyśmy chcieli pokazać to użytkownikowi.
Pod wcześniejszymi dyrektywami dodaj następujący kod:
```
<Directory "/var/www/html/rewrite">
    RewriteEngine On
    RewriteBase "/rewrite/"
    RewriteRule  ^user/([^/]*)$ /action.php?name=$1
</Directory>
```
Spowoduje on, że każde zapytanie do adresu http://localhost:8080/rewrite/user/Monika zostanie przekształcone na zapytanie: `http://localhost:8080/action.php?name=Monika`. Obejrzyj wywołanie strony przy pomocy programu `curl`. Teraz znasz właściwie wszystkie narzędzia które pozwolą nam zaimplementować API REST.
