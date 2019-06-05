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
      | iso_code         | EUR   |
      | exchange_rate    | 0.88  |
      | is_enabled       | 1     |
      | shop_association | shop1 |
    Then currency "currency1" should be "EUR"
    And currency "currency1" exchange rate should be 0.88
    And currency "currency1" should have status enabled
    And currency "currency1" should be available in shop "shop1"
    When I edit currency "currency1" with following properties:
      | iso_code         | GBP   |
      | exchange_rate    | 1.22  |
      | is_enabled       | 0     |
      | shop_association | shop1 |
    Then currency "currency1" should be "GBP"
    And currency "currency1" exchange rate should be 1.22
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
      | exchange_rate    | 0.88  |
      | is_enabled       | 1     |
      | shop_association | shop1 |
    When I delete currency "currency4"
    Then "EUR" currency should be deleted
