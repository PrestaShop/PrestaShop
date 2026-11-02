# Kredyt kupiecki (trade credit) w core PrestaShop 9.2.0

## Gdzie co jest

| | |
|---|---|
| Metody płatności jako kredyt kupiecki, kwota domyślna (50 000) | *Płatności → Preferencje*, karta "Trade credit" |
| Saldo klienta (edycja) i historia operacji | *Klienci → Edycja* / *Szczegóły* |
| Dostępny kredyt i blokada przy braku środków | checkout: etykieta opcji płatności + `themes/_core/js/checkout-payment.js` |
| Obciążenie (brutto z dostawą), walidacja serwerowa | `PaymentModule::validateOrder()` |
| Zwrot przy anulowaniu / statusie "Zwrot" / korekcie | `OrderHistory::changeIdOrderState()`, `OrderSlipCreator::create()` |

Logika: `src/Core/Domain/TradeCredit` (reguły) i `src/Adapter/TradeCredit` (`TradeCreditOrderService`). Encje: `CustomerTradeCredit`, `CustomerTradeCreditOperation`.

## Decyzje

- **Checkboxy w Preferencjach płatności**, bo w 9.2 nie ma w core formularza edycji pojedynczej metody płatności.
- **Saldo w nowej encji, nie w `outstanding_allow_amount`.** To pole B2B ma inną semantykę: limit liczony z faktur.
- **Kredyt tylko dla klientów z kontem.** Przyznawany przy pierwszym zamówieniu metodą kredytową; kwota ustawiona wcześniej przez admina ma pierwszeństwo.
- **Obciążenie w transakcji z `SELECT … FOR UPDATE`.** Podwójne wysłanie koszyka obciąża raz, nieudane zamówienie zwalnia kredyt.
- **Zwroty nie wykonują się dwa razy** (unikalny klucz operacji) i nie przekraczają obciążenia.
- **Waluta kredytu:** `PS_TRADE_CREDIT_CURRENCY` (PLN). Inne waluty są przeliczane po kursie sklepu.

## Ograniczenia

- Cofnięcie anulowania nie obciąża kredytu ponownie (admin koryguje saldo ręcznie).
- Blokada w JS liczy się przy renderze kroku płatności. Przy zmianie koszyka bez przeładowania limit i tak sprawdza serwer.
- Motywy `classic` / `hummingbird` to osobne paczki, więc zmiany są tylko w core.

## Aktualizacja bazy

Skrypt dla modułu autoupgrade (od 9.x upgrade SQL nie jest w core): [`autoupgrade-9.2.1.sql`](autoupgrade-9.2.1.sql).

## Testy (PHPUnit)

```bash
ddev composer create-test-db
ddev exec php vendor/bin/phpunit -c tests/Unit/phpunit.xml tests/Unit/Core/Domain/TradeCredit tests/Unit/Adapter/TradeCredit tests/Unit/Core/Payment
ddev exec php -d memory_limit=-1 vendor/bin/phpunit -c tests/Integration/phpunit.xml tests/Integration/Adapter/TradeCredit tests/Integration/PrestaShopBundle/Entity/Repository/CustomerTradeCreditRepositoryTest.php tests/Integration/PrestaShopBundle/Form/PaymentPreferencesTradeCreditTest.php tests/Integration/PrestaShopBundle/Form/CustomerFormTradeCreditTest.php
```

W `classes/PaymentModule.php` bloki tworzenia zamówień są tylko przeniesione do `try/catch`. Diff najlepiej czytać bez białych znaków (`?w=1`).
