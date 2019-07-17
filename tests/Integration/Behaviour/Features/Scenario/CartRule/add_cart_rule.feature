# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule
@reset-database-before-feature
Feature: Add cart rule
  PrestaShop allows BO users to create cart rules
  As a BO user
  I must be able to create cart rules

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "currency2" with iso code "CHF" and exchange rate of 1.25
    Given currency "currency1" is the default one

  Scenario: Create cart rule
    When I create cart rule "cart rule 1" with specified properties:
      | name                                | Cart rule 1               |
      | description                         | Cart rule description     |
      | valid_from                          | 2019-01-01 11:05:00       |
      | valid_to                            | 2019-12-01 00:00:00       |
      | quantity                            | 10                        |
      | quantity_per_user                   | 1                         |
      | priority                            | 2                         |
      | is_partial_use_enabled              | no                        |
      | is_active                           | yes                       |
      | highlight_in_cart                   | no                        |
      | code                                | TEST_CODE                 |
      | minimum_amount                      | 10                        |
      | minimum_amount_currency_iso_code    | CHF                       |
      | minimum_amount_tax_excluded         | 1                         |
      | minimum_amount_shipping_excluded    | 0                         |
      | is_free_shipping                    | no                        |
      | reduction_amount                    | 15                        |
      | reduction_currency_iso_code         | USD                       |
      | is_reduction_tax_excluded           | no                        |
      | reduction_type                      | order_without_shipping    |
    Then cart rule "cart rule 1" name field should be "Cart rule 1"
    And cart rule "cart rule 1" description should be "Cart rule description"
    And cart rule "cart rule 1" valid from date should be "2019-01-01 11:05:00"
    And cart rule "cart rule 1" valid to date should be "2019-12-01 00:00:00"
    And cart rule "cart rule 1" quantity should be "10"
    And cart rule "cart rule 1" quantity per user should be "1"
    And cart rule "cart rule 1" priority should be "2"
    And cart rule "cart rule 1" partial use should be "disabled"
    And cart rule "cart rule 1" status should be "enabled"
    And cart rule "cart rule 1" highlighting in cart should be "disabled"
    And cart rule "cart rule 1" code should be "TEST_CODE"
    And cart rule "cart rule 1" minimum amount should be "10"
    And cart rule "cart rule 1" minimum amount currency iso code should be "CHF"
    And cart rule "cart rule 1" minimum amount tax should be "excluded"
    And cart rule "cart rule 1" minimum amount shipping should be "included"
    And cart rule "cart rule 1" free shipping should be "disabled"
    And cart rule "cart rule 1" reduction amount should be "15"
    And cart rule "cart rule 1" reduction currency iso code should be "USD"
    And cart rule "cart rule 1" reduction tax should be "included"
    And cart rule "cart rule 1" reduction should apply to order without shipping
