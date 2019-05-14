# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s currency
@reset-database-before-feature
Feature: Currency Management
  PrestaShop allows BO users to manage customers in the Customers > Customers page
  As a BO user
  I must be able to create, save and edit customers

  Background:
    Given shop "shop1" with name "test_shop" exists

  Scenario: Adding new currency
    When I add new currency "currency1" with following properties:
      | iso_code         | EUR   |
      | exchange_rate    | 0.88  |
      | is_enabled       | true  |
      | shop_association | shop1 |
    Then currency "currency1" should be "EUR"
    And currency "currency1" exchange rate should be 0.88
    And currency "currency1" should have status "enabled"
    And currency "currency1" should be available in shop "shop1"
