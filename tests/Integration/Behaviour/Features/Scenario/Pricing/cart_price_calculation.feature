# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s pricing --tags cart-price-calculation
@restore-all-tables-before-feature
@clear-cache-before-feature
@cart-price-calculation
Feature: Cart price calculation through the new pricing pipeline
  As a developer
  I want the cart calculator to compute cart totals correctly
  So that cart prices are accurate

  Background:
    Given language with iso code "en" is the default one
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And there is a product in the catalog named "Basic T-Shirt" with a price of 29.99 and 1000 items in stock
    And there is a product in the catalog named "Premium Hoodie" with a price of 50.00 and 1000 items in stock

  Scenario: Compute cart total for a single product
    Given I create an empty cart "cart1" for customer "testCustomer"
    And I add 2 products "Basic T-Shirt" to the cart "cart1"
    When I compute the cart price for cart "cart1" I should get:
      | product_count               | 1  |
      | product_total_tax_excluded  | 60 |
      | product_total_tax_included  | 60 |
      | cart_total_tax_excluded     | 60 |
      | cart_total_tax_included     | 60 |

  Scenario: Compute cart total for multiple products
    Given I create an empty cart "cart2" for customer "testCustomer"
    And I add 1 products "Basic T-Shirt" to the cart "cart2"
    And I add 3 products "Premium Hoodie" to the cart "cart2"
    When I compute the cart price for cart "cart2" I should get:
      | product_count               | 2   |
      | product_total_tax_excluded  | 180 |
      | product_total_tax_included  | 180 |
      | cart_total_tax_excluded     | 180 |
      | cart_total_tax_included     | 180 |

  Scenario: Compute cart total for empty cart
    Given I create an empty cart "cart3" for customer "testCustomer"
    When I compute the cart price for cart "cart3" I should get:
      | product_count               | 0 |
      | product_total_tax_excluded  | 0 |
      | product_total_tax_included  | 0 |
      | cart_total_tax_excluded     | 0 |
      | cart_total_tax_included     | 0 |

  Scenario: Compute cart total for a product with combination
    Given I add product "combinationProduct" with following information:
      | name[en-US] | Colored T-Shirt |
      | type        | combinations    |
    And I update product "combinationProduct" with following values:
      | price      | 100.000000 |
      | unit_price | 10.000000  |
    And attribute group "Size" named "Size" in en language exists
    And attribute "S" named "S" in en language exists
    And I generate combinations for product "combinationProduct" using following attributes:
      | Size | [S] |
    And product "combinationProduct" should have following combinations:
      | id reference          | combination name | reference | attributes | impact on price | quantity | is default |
      | combinationProductS   | Size - S         |           | [Size:S]   | 0               | 0        | true       |
    And I update combination "combinationProductS" with following values:
      | impact on price      | 15.500000 |
      | impact on unit price | 2.500000  |
    And I update combination "combinationProductS" stock with following details:
      | delta quantity | 100 |
    And I create an empty cart "cart4" for customer "testCustomer"
    And I add 2 combinations "combinationProductS" from product "combinationProduct" to the cart "cart4"
    When I compute the cart price for cart "cart4" I should get:
      | product_count               | 1   |
      | product_total_tax_excluded  | 232 |
      | product_total_tax_included  | 232 |
      | cart_total_tax_excluded     | 232 |
      | cart_total_tax_included     | 232 |
