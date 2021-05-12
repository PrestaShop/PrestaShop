# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s currency --tags currency-data
@reset-database-before-feature
Feature: Currency Data
  PrestaShop provides handlers for currency data
  As a BO user
  I must be able to get data for a currency

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "language1" with locale "en-US" exists
    And language "language2" with locale "fr-FR" exists

  @currency-data
  Scenario: Get data from an official currency
    When I request reference data for "EUR"
    Then I should get no error
    And I should get currency data:
      | iso_code         | EUR        |
      | numeric_iso_code | 978        |
      | precision        | 2          |
      | names[en-US]     | Euro       |
      | names[fr-FR]     | euro       |
      | symbols[en-US]   | €          |
      | symbols[fr-FR]   | €          |
      | patterns[en-US]  | ¤#,##0.00  |
      | patterns[fr-FR]  | #,##0.00 ¤ |

  @currency-data
  Scenario: Get data from an unofficial currency
    When I request reference data for "BTC"
    Then I should get error that currency was not found
    When I add new currency "currency1" with following properties:
      | iso_code         | BTC       |
      | name             | Bitcoin   |
      | symbol           | ₿         |
      | exchange_rate    | 0.42      |
      | is_enabled       | 1         |
      | is_unofficial    | 1         |
      | shop_association | shop1     |
      | patterns[fr-FR]  | ¤#,##0.00 |
    When I request reference data for "BTC"
    Then I should get error that currency was not found

  @currency-data
  Scenario: Get data from a customized currency
    When I add new currency "currency1" with following properties:
      | iso_code         | JPY        |
      | name             | Custom Yen |
      | symbol           | YY         |
      | exchange_rate    | 0.08       |
      | is_enabled       | 1          |
      | is_unofficial    | 0          |
      | shop_association | shop1      |
      | patterns[fr-FR]  | ¤ #,##0.00 |
    When I request reference data for "JPY"
    Then I should get currency data:
      | iso_code         | JPY          |
      | numeric_iso_code | 392          |
      | precision        | 0            |
      | names[en-US]     | Japanese Yen |
      | names[fr-FR]     | yen japonais |
      | symbols[en-US]   | ¥            |
      | symbols[fr-FR]   | ¥            |
      | patterns[en-US]  | ¤#,##0.00    |
      | patterns[fr-FR]  | #,##0.00 ¤   |
