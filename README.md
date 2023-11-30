# MicroFramework
MicroFramework to lekki framework PHP oparty na architekturze MVC z zintegrowanymi rozszerzeniami ułatwiającymi inicjowanie i zarządzanie projektem. Framework ten został zaprojektowany z myślą o elastyczności, co pozwala programistom szybko skonfigurować i zorganizować projekty w efektywny sposób.

# Dlaczego MicroFramework?
**Prostota Użycia:**
MicroFramework skupia się na prostocie, dzięki czemu zarówno początkujący, jak i doświadczeni programiści mogą z nim pracować bezproblemowo.

**Rozszerzenia:**
Framework obsługuje dodatkowe komponenty, które znacznie poszerzają jego możliwości. Niezależnie od tego, czy potrzebujesz obsługi formularzy czy dynamicznego ładowania modeli, MicroFramework spełni Twoje oczekiwania.

**Szybki Start:** 
Komenda init pozwala szybko utworzyć nowy projekt z predefiniowaną strukturą katalogów, oszczędzając czas i wysiłek.

**Modułowość:**
MicroFramework został zaprojektowany z myślą o modularności, co pozwala dodawać lub usuwać komponenty w zależności od wymagań projektu.

# Instalacja
MicroFramework może być łatwo zainstalowany za pomocą Composera. Wystarczy uruchomić poniższą komendę w katalogu swojego projektu:
```bash
composer require krzysztofzylka/micro-framework
```

## Inicjalizacja
MicroFramework ułatwia inicjowanie projektów. Skorzystaj z poniższej komendy, aby utworzyć nowy projekt:
```bash
php vendor/bin/microframework init <project directory>
```

## Struktura
- `public` folder publiczny
- `public/assets` dodatkowe pliki dla witryny
- `src/Controller` - kontrolery
- `src/Model` - modele
- `src/View` - widoki
- `storage` - magazyn danych
- `storage/log` - logi
- `.env` - konfiguracja globalna
- `component.json` - plik z konfiguracją komponentów
- `local.env` - lokalna konfiguracja


# Współtworzenie
Zapraszamy do współtworzenia! Jeśli masz sugestie, raporty błędów lub chciałbyś przyczynić się do rozwoju MicroFramework, otwórz nowe zgłoszenie (issue) lub prześlij pull request.

# Pomoc
Wszelkie problemy oraz pytania należy zadawać przez zakładkę discussions w github pod linkiem: https://github.com/krzysztofzylka/MicroFramework/discussions
