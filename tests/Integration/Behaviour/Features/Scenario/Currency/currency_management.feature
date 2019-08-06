# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s currency
@reset-database-before-feature
Feature: Currency Management
  PrestaShop allows BO users to manage currencies
  As a BO user
  I must be able to create, edit and delete currencies in my shop

  Background:
    Given shop "shop1" with name "test_shop" exists

  Scenario: Adding and editing currency
    When I add new currency "currency1" with following properties:
      | iso_code         | EUR       |
      | numeric_iso_code | 978       |
      | exchange_rate    | 0.88      |
      | name             | My Euros  |
      | symbol           | €         |
      | is_enabled       | 1         |
      | is_custom        | 0         |
      | shop_association | shop1     |
    Then currency "currency1" should be "EUR"
    And currency "currency1" exchange rate should be 0.88
    And currency "currency1" numeric iso code should be 978
    And currency "currency1" name should be "My Euros"
    And currency "currency1" symbol should be "€"
    And currency "currency1" should have custom false
    And currency "currency1" should have edited true
    And currency "currency1" should have status enabled
    And currency "currency1" should be available in shop "shop1"
    And database contains 1 rows of currency "EUR"
    When I edit currency "currency1" with following properties:
      | iso_code         | GBP           |
      | numeric_iso_code | 826           |
      | exchange_rate    | 1.22          |
      | is_enabled       | 0             |
      | is_custom        | 0             |
      | shop_association | shop1         |
    Then currency "currency1" should be "GBP"
    And currency "currency1" exchange rate should be 1.22
    And currency "currency1" numeric iso code should be 826
    And currency "currency1" name should be "My Euros"
    And currency "currency1" symbol should be "€"
    And currency "currency1" should have custom false
    And currency "currency1" should have edited true
    And currency "currency1" should have status disabled
    When I edit currency "currency1" with following properties:
      | iso_code         | GBP           |
      | numeric_iso_code | 826           |
      | exchange_rate    | 1.22          |
      | name             | British Pound |
      | symbol           | £             |
      | is_enabled       | 0             |
      | is_custom        | 0             |
      | shop_association | shop1         |
    Then currency "currency1" should be "GBP"
    And currency "currency1" exchange rate should be 1.22
    And currency "currency1" numeric iso code should be 826
    And currency "currency1" name should be "British Pound"
    And currency "currency1" symbol should be "£"
    And currency "currency1" should have custom false
    And currency "currency1" should have custom false
    And currency "currency1" should have edited false
    And currency "currency1" should have status disabled

  Scenario: Disabling default currency should not be allowed
    Given currency "currency2" with "USD" exists
    And currency "currency2" is default in "shop1" shop
    When I disable currency "currency2"
    Then I should get error that default currency cannot be disabled

  Scenario: Deleting default currency should not be allowed
    Given currency "currency3" with "USD" exists
    And currency "currency3" is default in "shop1" shop
    When I delete currency "currency3"
    Then I should get error that default currency cannot be deleted

  Scenario: Deleting non default currency should be allowed
    When I add new currency "currency4" with following properties:
      | iso_code         | EUR   |
      | numeric_iso_code | 978   |
      | exchange_rate    | 0.88  |
      | name             | Euro  |
      | symbol           | €     |
      | is_enabled       | 1     |
      | shop_association | shop1 |
    When I delete currency "currency4"
    Then "EUR" currency should be deleted
