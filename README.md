# <h1 align="center">MicroFramework</h1>

MicroFramework to lekki framework PHP oparty na architekturze MVC, integrujący rozszerzenia ułatwiające inicjowanie i zarządzanie projektem. Zaprojektowany z myślą o elastyczności, umożliwia programistom szybką konfigurację i efektywne organizowanie projektów.

## Dlaczego warto wybrać MicroFramework?

- **Prostota użycia:** Dzięki skupieniu na prostocie, zarówno początkujący, jak i doświadczeni programiści mogą łatwo pracować z frameworkiem.
- **Rozszerzenia:** Obsługa dodatkowych komponentów znacznie rozszerza możliwości frameworka, spełniając oczekiwania w zakresie obsługi formularzy czy dynamicznego ładowania modeli.
- **Szybki start:** Komenda `init` umożliwia szybkie utworzenie nowego projektu z predefiniowaną strukturą katalogów.
- **Modułowość:** Projektowanie z myślą o modularności pozwala na łatwe dodawanie lub usuwanie komponentów.

## Instalacja
### Instalacja wymaganych pakietów dla ubuntu
```bash
sudo apt install npm
npm install -g grunt-cli
```
Za pomocą Composera MicroFramework można łatwo zainstalować, wykonując poniższą komendę w katalogu projektu:
```bash
composer require krzysztofzylka/micro-framework
```

## Inicjalizacja

Ułatwia inicjowanie projektów. Aby utworzyć nowy projekt, użyj komendy:
```bash
php vendor/bin/microframework init <project directory>
```

## Używanie konsoli

Poznaj komendy konsoli, używając:
```bash
php vendor/bin/microframework
```
Dzięki temu poznasz wszystkie dostępne komendy.

## Struktura projektu

- `public` - folder publiczny
- `public/assets` - zasoby witryny
- `src/Controller` - kontrolery
- `src/Model` - modele
- `src/View` - widoki
- `storage` - przechowywanie danych
- `storage/log` - logi
- `migrations` - migracje (obecnie tylko pliki PHP)
- `.env` - konfiguracja globalna
- `component.json` - konfiguracja komponentów
- `local.env` - konfiguracja lokalna

## Współtworzenie

Zachęcamy do współtworzenia! Masz sugestie, znalazłeś błędy, chcesz pomóc w rozwoju? Otwórz issue lub prześlij pull request.

## Pomoc
Wszelkie problemy oraz pytania należy zadawać przez zakładkę discussions w github pod linkiem: https://github.com/krzysztofzylka/MicroFramework/discussions
