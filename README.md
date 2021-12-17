# Zadanie testowe
***
Tabela z użytkownikami znajduje się pod adresem /user
---
#### Częste błędy przy konfigruacji
1. W pliku `php.ini` 
    * Powinny włączone rozszerzenia `openssl`, `curl` oraz `pdo_mysql` lub inny w zależności od skonfigurowanej bazy danych
    * Wskazywać ścieżkę do folderu ww rozszerzeń
    * Zmienne `openssl.cafile` i `curl.cainfo` powinny Wskazywać scieżkę (najlepiej bezwzględną) do pliku z certyfikatami SSL (w przypadku Windowsa trzeba plik pobrać go [stąd](https://curl.haxx.se/ca/cacert.pem))
2. W przypadku użytego przeze mnie serwera MariaDB w pliku `.env` musiałem sprecyzować użycie nie tylko jego wersji np. `DATABASE_URL="mysql://db:password@127.0.0.1:3306/zadanie?serverVersion=mariadb-10.5.9"`
