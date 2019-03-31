# sisi-l03

## SiSi - laboratorium 3. Serwer webowy - jak działa web API?

Do wykonania ćwiczeń z laboratorium potrzebujesz zainstalowanych aplikacji: VirtualBox i Vagrant. Obie aplikacje istnieją w wersjach dla systemów Linux, Windows, Mac.

Po pobraniu repozytorium uruchom maszynę vagranta: `vagrant up`. Gdy maszyna zakończy proces uruchamiania w wyświetlonym przez VirtualBox okinie maszyny wirtualnej zaloguj się na konto vagrant używając hasła vagrant.

W ramach ćwiczenia przygotujesz plik konfiguracyjny dla serwera Nginx.

### Strony statyczne

Otwórz okno konsoli (System tools > LXTerminal). Wpisz polecenie
```
sudo  nano /etc/nginx/nginx.conf
```
W otworzonym pliku, poniżej sekcji events wstaw kolejną sekcję konfigurującą obsługę protokołu HTTP. Opcja gzip włącza kompresję przesyłanych plików:
```
http {
    gzip on;

}
```

Wewnątrz sekcji http wstaw sekcję reprezentującą serwer wirtualny:
```
server {
    server_name localhost;
    listen 80;

    root /vagrant/www;
}
```
Zapisz zmodyfikowany plik konfiguracyjny (Ctrl-S).

Otwórz drugie okno terminala i wykonaj polecenia: sprawdzające konfigurację i restartującą serwer. Po każdej modyfikacji pliku konfiguracyjnego sprawdzaj czy składnia jest poprawna i dopiero później restartuj serwer:
```
sudo nginx -t
sudo nginx -s reload
```

Otwórz okno przeglądarki i zobacz czy możesz otworzyć stronę http://localhost/. W oknie terminala wpisz polecenie:
```
curl http://localhost/
curl -v http://localhost/
```
aby sprawdzić dane przesyłane z serwera. Często użycie polecenia curl jest szybsze niż odpalenie przeglądarki.


### Proste przekierowanie zawartości 

Obejrzyj źródło strony w przeglądarce (View Page Source). Zauważ, że znajduje się tam odniesienie do rysunku (img), ale wpisanie w przeglądarce adresu http://localhost/img/webserver.jpeg nie wyświetla obrazka, ponieważ w katalogu /vagrant/www nie ma katalogu img z obrazkami. Obrazki znajdują się w katalogu /vagrant/images. Poniżej opcji root dodaj moduł definiujący przekierowanie:
```
location /img/ {
    gzip off;
    alias /vagrant/images/;
}
```
Sprawdź konfigurację i zrestartuj serwer. Teraz na głównej stronie powinien być już wyświetlany obrazek. Dodatkowo opcja gzip wyłącza kompresję obrazków tylko w tej sekcji.

### Ochrona strony hasłem

W folderze /vagrant/www znajduje się folder protected, w którym mają być przechowywane strony chronione hasłem. Obejrzyj plik /vagrant/password_file zawierający listę użytkowników i ich haseł. Dodaj swojego użytkownika z nowym hasłem.

Poniżej sekcji z poprzedniego zadania dodaj sekcję konfigurującą ochronę hasłem:
```
location /protected/ {
    auth_basic "Protected web pages";
    auth_basic_user_file /vagrant/password_file;
}
```
Zrestartuj serwer (sprawdzając wcześniej konfigurację) i sprawdź czy przed wyświetleniem chronionej strony przeglądarka pyta o hasło. Zaloguj się na swojego użytkownika.

Spróbuj następnie to samo zrobić przy pomocy programu curl:
```
curl http://localhost/protected/
curl http://localhost/protected  --user ania:password
curl http://localhost/protected -L --user ania:password
```

### Nginx jako proksy

Serwer Nginx może przekazywać połączenia do innych aplikacji (również umieszczonych na innych serwerach).

Uruchom aplikację node.js:
```
sudo pm2 start /vagrant/node/hello.js
```
Sprawdź czy w przeglądarce zobaczysz działającą aplikację: http://localhost:3000/

Poniżej sekcji z poprzedniego zadania umieść sekcję przekierowującą ruch:
```
location /app/ {
    proxy_pass http://127.0.0.1:3000/;
    proxy_set_header Host $host;
}
```
Po zrestartowaniu serwera zobaczysz, że aplikacja jest dostępna pod adresem: http://localhost/app/. Nginx przekierowuje zapytanie do innej aplikacji. Opcja proxy_set_header przekazuje do aplikacji nazwę hosta z którego nadeszło zapytanie.

### Przepisywanie adresów

Nie zawsze pliki dają się zapisać tak jakbyśmy chcieli pokazać to użytkownikowi. Zamiast stosować zapis http://localhost/app?name=Wojtek (parametr name przekazywany metodą GET) chcelibyśmy zastosować następujący zapis: http:localhost/greet/Wojtek. Aby to zrobić, pod wcześniejszą sekcją dodaj następujący kod:
```
location /greet/ {
    rewrite ^/greet/(.*)$ /app/?name=$1?;
}
```
Spowoduje on, że każde zapytanie do adresu `http://localhost/greet/Monika` zostanie przekształcone opcją rewrite na zapytanie: `http://localhost:8080/app?name=Monika`. 
Zrestartuj serwer.

Obejrzyj wywołanie strony przy pomocy przeglądarki i programu `curl`. 

Obejrzyj stronę http://localhost/form.html. Wpisz dane użytkownika, i zobacz gdzie trafiasz po kliknięciu przycisku Greet.

### Szyfrowanie połączenia z serwerem Nginx

Aby zaszyfrować połączenie używając protokołu HTTPS poniżej opcji listen 80; dodaj następujące opcje:
```
listen 443 ssl;

ssl_certificate /vagrant/ssl/localhost.crt;
ssl_certificate_key /vagrant/ssl/localhost.key;
```
Opcje ssl_certyficate określają gdzie znajduje sie cartyfikat i klucz szyfrowania.
Zrestartuj serwer i zobacz czy możesz obejrzeć stronę wpisując adres https://localhost/.
Ponieważ certyfikat jest typu self-signed, to przeglądarka wyświetli ostrzeżenie o możliwym zagrożeniu. Musisz w takim wypadku dodać wyjątek bezpieczeństwa.
      
Teraz znasz właściwie wszystkie narzędzia które pozwolą nam zaimplementować API REST.
