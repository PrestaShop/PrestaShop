Konfiguracja sklepu – Vita Natura / EcoSusz

Po instalacji PrestaShop wykonaj poniższe kroki konfiguracyjne.

1) Dane sklepu (Kontakt → Skontaktuj się z nami / Konfiguracja sklepu)
- Nazwa firmy: GOSPODARSTWO EKOLOGICZNE VITA NATURA ECOSUSZ TOMASZ CHLEBEK
- Adres: Wandalin 129, 24-300 Opole Lubelskie, Polska
- E-mail sklepu: biuro@ecosusz.pl
- Telefony: +48 791 017 002, +48 790 236 506
- Godziny pracy: pn–pt 8:00–17:00, sob. 8:00–14:00
- Strefa czasowa: Europe/Warsaw

2) Podatki (Międzynarodowe → Podatki)
- Opcja A (zalecana dla „rachunek bez VAT”): Wyłącz podatki globalnie – „Włącz podatek” = Nie; ukryj podział cen netto/brutto i podatek na koszyku i dokumentach.
- Opcja B (alternatywa): Pozostaw podatki włączone, ale nie przypisuj grup podatkowych produktom (skrypt już ustawia id_tax_rules_group=0).

3) Dostawa (Wysyłka → Przewoźnicy)
- Dodaj przewoźników:
  - Kurier (np. DPD): koszt 25 zł (PL), czas dostawy 2–4 dni
  - Paczkomaty InPost: koszt 20 zł (PL), czas dostawy 2–4 dni
- Progi darmowej dostawy: Wysyłka → Preferencje → „Darmowa wysyłka od kwoty” = 280 zł.

4) Płatności
- Przelew tradycyjny – zaktualizuj dane modułu „Płatność przelewem”:
  - Odbiorca: GOSPODARSTWO EKOLOGICZNE VITA NATURA ECOSUSZ TOMASZ CHLEBEK
  - Bank: Bank Pocztowy
  - IBAN: PL71 1320 1537 2911 1480 3000 0001
  - SWIFT/BIC: [uzupełnij]
- Płatności online (po otrzymaniu dostępów): PayU, Przelewy24, PayPal, itp.

5) Pola w checkout (Klienci → Ustawienia)
- Wyłącz pola „Firma” i „NIP” (nie są potrzebne, Sprzedawca nie wystawia faktur VAT);
- Zostaw możliwość wpisania imienia i nazwiska oraz adresu dostawy/rozliczeniowego.

6) Dokumenty sprzedażowe
- Konfiguracja → Zamówienia → Faktury: ustaw/zmień prefiks dokumentu na „RACH/” lub „PAR/”; rozważ ukrycie słowa „Faktura” w tłumaczeniach i zastąpienie „Dokument sprzedaży / Rachunek”.
- Tłumaczenia: Międzynarodowe → Tłumaczenia → Temat: Back Office/Sklep – zmień etykiety „Faktura VAT” na „Rachunek” gdzie właściwe.

7) Konto administratora
- Utwórz użytkownika (Ustawienia → Zasoby ludzkie → Pracownicy):
  - Imię i nazwisko: Jolanta Chlebek
  - E-mail: biuro@ecosusz.pl
  - Język: Polski
  - Uprawnienia: Administrator (lub według potrzeb)

8) Wygląd
- Wygląd → Motyw: Hummingbird (zalecany)
- Wygląd → Logo i Favicon: załaduj logo z katalogu „materialy/” (PNG 1000–1500px), utwórz favicon.

9) Informacja o RHD (strona CMS)
- Katalog → Strony → Dodaj stronę „O naszej żywności (RHD)” z informacją, że sprzedaż prowadzona jest w ramach RHD, zgłoszoną w PSSE w Opolu Lubelskim; dodać podstawowe zasady bezpieczeństwa i pochodzenie produktów.

10) Import produktów
- Uruchom skrypt: php install-dev/data/vita-natura-products.php
- Lub importuj plik CSV: install-dev/data/vita-natura-products.csv (Tax rules ID = 0)

8) Strony informacyjne (Katalog → Strony)
- Dodaj/uzupełnij: Regulamin, Polityka prywatności, Polityka cookies, Zwroty i reklamacje, O nas, Kontakt.
