# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s currency --tags reference
@reset-database-before-feature
Feature: Currency API
  PrestaShop provides an API for currency data
  As a BO user
  I must be able to get data from an API

  @reference
  Scenario: Get data from an official currency
    When I request API data for "EUR"
    Then I should get no currency error
    And I should get API data:
      | iso_code         | EUR        |
      | numeric_iso_code | 978        |
      | precision        | 2          |
      | names            | en-US:Euro |
      | symbols          | en-US:€    |

  @reference
  Scenario: Get data from an unofficial currency
    When I request API data for "BTC"
    Then I should get error that currency was not found
