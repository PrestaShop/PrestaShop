# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags discount-amount-exceeding-specific-price
@restore-all-tables-before-feature
@discount-amount-exceeding-specific-price
Feature: Cart level amount discount exceeding the products total of products with a specific price
  As a customer
  I must not pay anything for the products when a discount covers more than their total

  Background:
    Given I enable feature flag "discount"
    And there is a currency named "usd" with iso code "USD" and exchange rate of 1.0
    And there is a zone "europe" named "Europe"
    And there is a country named "france" and iso code "FR" in zone "europe"
    And there is a tax named "tax20" and rate 20.0%
    And there is a tax rule named "fr20" in country "france" where tax "tax20" is applied
    And there is a product in the catalog named "tshirt" with a price of 23.9 and 1000 items in stock
    And product "tshirt" belongs to tax group "fr20"
    And product "tshirt" has a specific price named "tshirt_20_percent" with a discount of 20.0 percent
    And there is a product in the catalog named "tshirt2" with a price of 23.9 and 1000 items in stock
    And product "tshirt2" belongs to tax group "fr20"
    And product "tshirt2" has a specific price named "tshirt2_20_percent" with a discount of 20.0 percent
    And a carrier "default_carrier" with name "My carrier" exists
    And there is a customer named "customer" whose email is "customer-39436@prestashop.com"
    And I add new address to customer "customer" with following details:
      | Address alias | customer-france-address |
      | First name    | Jane                    |
      | Last name     | Doe                     |
      | Address       | 1 rue de la Paix        |
      | City          | Paris                   |
      | Country       | France                  |
      | Postal code   | 75002                   |
    And I create a "cart_level" discount "discount_100" with following properties:
      | name[en-US]            | Discount 100 |
      | active                 | true         |
      | code                   | DISCOUNT_100 |
      | reduction_amount       | 100.0        |
      | reduction_currency     | usd          |
      | reduction_tax_included | false        |

  @reset-all-tables-before-scenario
  Scenario Outline: discount above the products total leaves only the shipping to pay for <quantity> products
    Given I create an empty cart "cart" for customer "customer"
    And cart "cart" delivery and invoice address country for customer "customer" is "FR"
    And I add <quantity> products "tshirt" to the cart "cart"
    And I select carrier "default_carrier" for cart "cart"
    And cart "cart" total with tax included should be '<total_before_discount>'
    When I use a voucher "discount_100" on the cart "cart"
    Then my cart "cart" should have the following details:
      | total_products | <total_products>  |
      | total_discount | -<total_products> |
      | shipping       | $7.00             |
      | total          | $7.00             |

    Examples:
      | quantity | total_products | total_before_discount |
      | 1        | $19.12         | $29.94                |
      | 2        | $38.24         | $52.89                |
      | 3        | $57.36         | $75.83                |

  @reset-all-tables-before-scenario
  Scenario Outline: discount above the products total split on two rows leaves only the shipping to pay for <quantity1> and <quantity2> products
    Given I create an empty cart "cart" for customer "customer"
    And cart "cart" delivery and invoice address country for customer "customer" is "FR"
    And I add <quantity1> products "tshirt" to the cart "cart"
    And I add <quantity2> products "tshirt2" to the cart "cart"
    And I select carrier "default_carrier" for cart "cart"
    When I use a voucher "discount_100" on the cart "cart"
    Then my cart "cart" should have the following details:
      | total_products | <total_products>  |
      | total_discount | -<total_products> |
      | shipping       | $7.00             |
      | total          | $7.00             |

    Examples:
      | quantity1 | quantity2 | total_products |
      | 1         | 1         | $38.24         |
      | 1         | 2         | $57.36         |
      | 1         | 3         | $76.48         |
