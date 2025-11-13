# Import produktów Vita Natura / EcoSusz

Ten katalog zawiera gotowe skrypty do importu produktów ekologicznych suszonych owoców i przetworów dla sklepu Vita Natura / EcoSusz.

## Dostępne produkty

### Suszone owoce BIO (100g):
- ✓ Jabłka suszone BIO - 15,90 zł
- ✓ Gruszki suszone BIO - 17,90 zł
- ✓ Maliny suszone BIO - 24,90 zł
- ✓ Czereśnie suszone BIO - 22,90 zł
- ✓ Śliwki suszone BIO - 16,90 zł
- ✓ Mix suszonych owoców BIO (Jabłka, Gruszki, Maliny) - 19,90 zł

### Zioła i napary:
- ✓ Ziele pokrzywy suszone BIO 100g - 12,90 zł
- ✓ Kwiat czarnego bzu nieotarty BIO 100g - 14,90 zł

### Soki i przetwory:
- ✓ Sok malinowy tłoczony BIO 300ml - 18,90 zł
- ✓ Syrop malinowy BIO 300ml - 16,90 zł
- ✓ Konfitura z czarnej maliny BIO bez cukru - 19,90 zł
- ✓ Ocet jabłkowy cydrowy BIO 500ml - 16,90 zł

**Razem: 13 produktów**

## Metoda 1: Import przez skrypt PHP (ZALECANE)

### Po zainstalowaniu PrestaShop uruchom:

```bash
# Z kontenera Docker
make docker-sh
php install-dev/data/vita-natura-products.php

# Lub bezpośrednio z terminala w kontenerze
php /var/www/html/install-dev/data/vita-natura-products.php
```

### Co robi skrypt:
- ✓ Tworzy kategorię "Suszone owoce BIO"
- ✓ Konfiguruje VAT 5% dla produktów spożywczych (Polska)
- ✓ Importuje wszystkie 13 produktów z pełnymi opisami
- ✓ Dodaje zdjęcia produktów z katalogu `materialy/`
- ✓ Ustawia stany magazynowe
- ✓ Dodaje tagi SEO i metadata

## Metoda 2: Import CSV przez panel admina

### Kroki:

1. **Zaloguj się do panelu admina**
   - URL: http://localhost:8001/admin-dev
   - Login: demo@prestashop.com
   - Hasło: Correct Horse Battery Staple

2. **Przejdź do:**
   ```
   Zaawansowane parametry → Importuj
   ```

3. **Konfiguracja importu:**
   - Typ: **Produkty**
   - Plik: Wybierz `vita-natura-products.csv`
   - Język: Polski
   - Separator: Średnik (;)
   - Wielokrotny separator wartości: Przecinek (,)

4. **Mapowanie kolumn:**
   - PrestaShop powinien automatycznie rozpoznać kolumny
   - Sprawdź mapowanie przed importem

5. **Opcje:**
   - ☑ Pomiń istniejące produkty
   - ☑ Użyj adresu URL produktu (jeśli dostępny)
   - ☐ Wymuś wszystkie numery ID

6. **Kliknij "Importuj CSV"**

### ⚠️ UWAGA dla importu CSV:
- Obrazy muszą być dostępne pod URL: `http://localhost:8001/materialy/nazwa-pliku.jpg`
- Alternatywnie: Skopiuj zdjęcia do `img/tmp/` przed importem
- Kategoria "Suszone owoce BIO" musi być wcześniej utworzona ręcznie

## Metoda 3: Ręczne kopiowanie obrazów (jeśli potrzebne)

Jeśli obrazy nie importują się automatycznie:

```bash
# Z głównego katalogu PrestaShop
make docker-sh

# Skopiuj obrazy do katalogu tymczasowego importu
cp materialy/*.jpg img/tmp/
cp materialy/*.webp img/tmp/

# Zmień uprawnienia
chmod 755 img/tmp/*
chown www-data:www-data img/tmp/*
```

Następnie w CSV zmień ścieżki obrazów z URL na nazwy plików:
```
http://localhost:8001/materialy/plik.jpg → plik.jpg
```

## Struktura danych produktów

Każdy produkt zawiera:

### Podstawowe informacje:
- Nazwa produktu
- Numer referencyjny (VN-XXX-XXX)
- Kategoria główna
- Cena (netto)
- VAT 5% (produkty spożywcze w Polsce)

### Opisy zgodne z wymogami dla żywności:
- **Skład** (składniki w kolejności malejącej)
- **Wartości odżywcze** (na 100g/100ml)
- **Alergeny** (jeśli dotyczy)
- **Sposób przechowywania**
- **Sposób użycia** (dla herbat, ziół)
- **Certyfikaty** (BIO, ekologiczne)

### SEO i marketing:
- Krótki opis (meta description)
- Pełny opis produktu z HTML
- Tagi (słowa kluczowe)
- URL przyjazne SEO
- Meta title i keywords

### Dane logistyczne:
- Waga produktu
- Stan magazynowy
- Dostępność
- Zdjęcie produktu

## Weryfikacja po imporcie

### Sprawdź w panelu admina:

1. **Katalog → Produkty**
   - Powinieneś zobaczyć wszystkie 13 produktów
   - Status: Aktywne ✓

2. **Katalog → Kategorie**
   - Kategoria "Suszone owoce BIO" z produktami

3. **Międzynarodowe → Podatki**
   - Grupa VAT 5% (żywność)

4. **Odwiedź sklep:**
   - http://localhost:8001
   - Sprawdź czy produkty są widoczne
   - Zweryfikuj zdjęcia i opisy

## Dane prawne do uzupełnienia

⚠️ **Przed uruchomieniem sklepu** uzupełnij w panelu admina:

### Konfiguracja → Informacje o sklepie:
- [ ] Nazwa firmy (Vita Natura / EcoSusz)
- [ ] NIP, REGON, KRS
- [ ] Adres siedziby
- [ ] Telefon kontaktowy
- [ ] Email kontaktowy

### Dla sprzedaży żywności BIO:
- [ ] Numer rejestracji GIS/GIJHARS
- [ ] Certyfikat HACCP
- [ ] Certyfikat BIO (jednostka certyfikująca)
- [ ] Dane Urzędu Skarbowego
- [ ] Rejestracja BDO (odpady opakowaniowe)

### Dokumenty prawne:
- [ ] Regulamin sklepu
- [ ] Polityka prywatności (RODO)
- [ ] Polityka cookies
- [ ] Procedura reklamacyjna (14 dni zwrotu)

## Wsparcie

Jeśli import się nie powiedzie:

1. **Sprawdź logi błędów:**
   ```bash
   tail -f var/logs/dev.log
   ```

2. **Sprawdź uprawnienia:**
   ```bash
   ls -la materialy/
   ls -la img/
   ```

3. **Wyczyść cache:**
   ```bash
   make cc
   # lub
   php bin/console cache:clear
   ```

4. **Reimport:**
   - Usuń istniejące produkty (jeśli niepoprawne)
   - Uruchom skrypt ponownie

## Dodatkowe materiały

Katalog `materialy/` zawiera również:

- **Logo Vita Natura/EcoSusz** (3 wersje):
  - Kolorowe logo (do nagłówka)
  - Logo czarno-białe wersja A i B
  - Format: AI, EPS, PDF, PNG, JPG (różne rozmiary)

- **Instrukcje użycia logo:**
  - `ecosusz_logo-informacje.jpg`

- **Opisy produktów (źródło):**
  - `Opisy do sklepu internetowego.odt`

## Następne kroki

Po zaimportowaniu produktów:

1. ✓ Dostosuj szablon graficzny sklepu
2. ✓ Dodaj logo Vita Natura/EcoSusz
3. ✓ Skonfiguruj metody płatności
4. ✓ Skonfiguruj metody dostawy
5. ✓ Uzupełnij dane prawne i certyfikaty
6. ✓ Przetestuj proces zamówienia
7. ✓ Skonfiguruj email notifications
8. ✓ Dodaj informacje o certyfikatach BIO na stronie głównej

---

**Vita Natura / EcoSusz**  
*Ekologiczne suszone owoce z polskich sadów*
