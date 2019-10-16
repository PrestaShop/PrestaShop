# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s currency --tags reference
@reset-database-before-feature
Feature: Currency API
  PrestaShop provides an API for currency data
  As a BO user
  I must be able to get data from an API

  Background:
    Given shop "shop1" with name "test_shop" exists
      And language "language1" with locale "en-US" exists
      And language "language2" with locale "fr-FR" exists

  @reference
  Scenario: Get data from an official currency
    When I request reference data for "EUR"
    Then I should get no currency error
    And I should get reference data:
      | iso_code         | EUR                   |
      | numeric_iso_code | 978                   |
      | precision        | 2                     |
      | names            | en-US:Euro;fr-FR:euro |
      | symbols          | en-US:€;fr-FR:€       |
      | patterns         | en-US:¤#,##0.00;fr-FR:#,##0.00 ¤ |

  @reference
  Scenario: Get data from an unofficial currency
    When I request reference data for "BTC"
    Then I should get error that currency was not found

  @reference
  Scenario: Get data from a customized API
    When I add new currency "currency1" with following properties:
      | iso_code         | JPY                 |
      | name             | Custom Yen          |
      | symbol           | YY                  |
      | exchange_rate    | 0.08                |
      | is_enabled       | 1                   |
      | is_unofficial    | 0                   |
      | shop_association | shop1               |
      | transformations  | fr-FR:leftWithSpace |
    When I request reference data for "JPY"
    Then I should get reference data:
      | iso_code         | JPY                                   |
      | numeric_iso_code | 392                                   |
      | precision        | 0                                     |
      | names            | en-US:Japanese Yen;fr-FR:yen japonais |
      | symbols          | en-US:¥;fr-FR:¥                       |
      | patterns         | en-US:¤#,##0.00;fr-FR:#,##0.00 ¤      |
