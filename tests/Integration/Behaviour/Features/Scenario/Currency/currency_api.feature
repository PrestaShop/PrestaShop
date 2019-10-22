# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s currency --tags api
@reset-database-before-feature
Feature: Currency API
  PrestaShop provides an API for currency data
  As a BO user
  I must be able to get data from an API

  @api
  Scenario: Get data from an official currency
    When I request API data for "EUR"
    Then I should get API data:
      | iso_code         | EUR        |
      | numeric_iso_code | 978        |
      | precision        | 2          |
      | names            | en-US:Euro |
      | symbols          | en-US:€    |
      | exchange_rate    | 0.8        |

  @api
  Scenario: Get data from an unofficial currency
    When I request API data for "BTC"
    Then I should get API data:
      | iso_code         | BTC        |
      | numeric_iso_code | null       |
      | precision        | 2          |
      | names            | en-US:BTC  |
      | symbols          | en-US:BTC  |
      | exchange_rate    | 1          |
