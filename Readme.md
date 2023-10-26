# MicroFramework
Microframework jest frameworkiem PHP MVC z dodatkowymi zintegrowanymi rozszerzeniami które pomagają w prosty sposób zainicjować projekt i nim zarządzć

## Dlaczego MicroFramework?
Dzięki MVC oraz dodatkowym rozszerzeniom pozwala w prosty sposób utworzyć projekt bez zbędnej konfiguracji, posiada on wiele gotowych bibliotek np. autoryzacji czy plików współdzielonych aby skutecznie ograniczyć czas potrzebny na utworzenie projektu.

# Wymagania
- PHP >= 8.1
- php-mbstring
- php-pdo
- Composer

## Dodatkowe (niewymagane)
Memcached

# Inicjalizacja
1. Należy zainicjalizować composer
```bash
php composer require krzysztofzylka/micro-framework
```
2. Przechodzimy do folderu w którym chcemy zainicjować projekt i następnie uruchamiamy w nim komendę:
```bash
php vendor/krzysztofzylka/micro-framework/src/console/Console.php init
```
3. Konfigurujemy projekt w folderze env wg. dokumentacji
4. Opcja jeżeli chcemy korzystać z bazy danych
Aby uruchomić aktualizator bazy wchodzimy do konsoli w folderze projektu i uruchamiamy
```bash 
php vendor/krzysztofzylka/micro-framework/src/console/Console.php database update
```
5. Uruchamiamy projekt z folderu `public` w którym znajduje się plik `index.php`

## Jak działa uruchamianie metody z kontrolera
Zostało to uproszczone jak najbardziej, wystarczy wejść w link http://127.0.0.1/{nazwa kontrolera}/{metoda} oraz opcjonalnie /{param1}/{param2}[...]

# Start

## Własny kontroler
Kontrolery dla użytkowników tworzymy w folderze `app/controller`

Dokładny opis kontrolera znajduje się na tej stronie https://doc.clickup.com/9015093357/d/h/8cneu3d-415/2bc2356e64c8eb1/8cneu3d-935

## Własny model
Modele dla użytkowników tworzymy w folderze `app/model`

Dokładny opis modelu znajduje się na tej stronie https://doc.clickup.com/9015093357/d/h/8cneu3d-415/2bc2356e64c8eb1/8cneu3d-955

## Nowy widok
Widoki dla użytkowników tworzymy jako plik o ścieżce `app/view/{nazwa kontrolera}/{nazwa metody}.twig`

# Dokumentacja
Szczegółową dokumentację znajdziecie na stronie (dokumentacja jest na ten moment niepełna ze względu na wersję alpha):
https://doc.clickup.com/9015093357/d/h/8cneu3d-415/2bc2356e64c8eb1

# Pomoc
Wszelkie problemy oraz pytania należy zadawać przez zakładkę discussions w github pod linkiem: https://github.com/krzysztofzylka/MicroFramework/discussions
