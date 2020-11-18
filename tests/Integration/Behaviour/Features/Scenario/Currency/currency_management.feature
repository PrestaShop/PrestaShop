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
      | exchange_rate    | 0.88      |
      | is_enabled       | 1         |
      | shop_association | shop1     |
    Then currency "currency1" should be "EUR"
    And currency "currency1" exchange rate should be 0.88
    And currency "currency1" numeric iso code should be 978
    And currency "currency1" name should be "Euro"
    And currency "currency1" symbol should be "€"
    And currency "currency1" should have status enabled
    And currency "currency1" should be available in shop "shop1"
    And there should be 1 currencies of "EUR"
    When I edit currency "currency1" with following properties:
      | iso_code         | EUR       |
      | exchange_rate    | 1.0       |
      | is_enabled       | 0         |
      | shop_association | shop1     |
    Then currency "currency1" should be "EUR"
    And currency "currency1" exchange rate should be 1
    And currency "currency1" numeric iso code should be 978
    And currency "currency1" name should be "Euro"
    And currency "currency1" symbol should be "€"
    And currency "currency1" precision should be 2
    And currency "currency1" should have status disabled
