# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags add-discount
@restore-all-tables-before-feature
@add-discount

Feature: Add discount
  PrestaShop allows BO users to create discounts
  As a BO user
  I must be able to create discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    Given currency "usd" is the default one
    And language with iso code "en" is the default one

  Scenario: Create a simple discount with free shipping
    When I create a free shipping discount "discount_1"
    And discount "discount_1" should have the following properties:
      | free_shipping | true  |
      | active        | false |

  Scenario: Create a complete discount with free shipping
    When I create a free shipping discount "discount_1" with following properties:
      | name[en-US]       | Promotion              |
      | description       | Promotion for holidays |
      | highlight         | false                  |
      | active            | true                   |
      | allow_partial_use | false                  |
      | priority          | 2                      |
      | valid_from        | 2019-01-01 11:05:00    |
      | valid_to          | 2019-12-01 00:00:00    |
      | total_quantity    | 10                     |
      | quantity_per_user | 1                      |
      | code              | PROMO_2019             |
    And discount "discount_1" should have the following properties:
      | name[en-US]       | Promotion              |
      | description       | Promotion for holidays |
      | highlight         | false                  |
      | active            | true                   |
      | allow_partial_use | false                  |
      | priority          | 2                      |
      | valid_from        | 2019-01-01 11:05:00    |
      | valid_to          | 2019-12-01 00:00:00    |
      | total_quantity    | 10                     |
      | quantity_per_user | 1                      |
      | free_shipping     | true                   |
      | code              | PROMO_2019             |

  Scenario: Create a discount with free shipping online but without names should be forbidden
    When I create a free shipping discount "discount_1" with following properties:
      | active | true |
    Then I should get error that discount field name is invalid
