# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s pricing --tags product-price-calculation
@restore-products-before-feature
@clear-cache-before-feature
@product-price-calculation
Feature: Product price calculation through the new pricing pipeline
  As a developer
  I want the pricing calculator to compute product prices correctly
  So that prices are accurate and rounded as expected

  Background:
    Given language with iso code "en" is the default one
    And I add product "product1" with following information:
      | name[en-US] | Basic T-Shirt |
      | type        | standard      |
    And I update product "product1" with following values:
      | price      | 29.990000 |
      | unit_price | 5.500000  |

  Scenario: Compute base price for a simple product
    When I compute the product price for product "product1" with quantity 1 I should get:
      | original_price_tax_excluded | 29.990000 |
      | original_price_tax_included | 29.990000 |
      | unit_price_tax_excluded     | 5.500000  |
      | unit_price_tax_included     | 5.500000  |
      | discount_price_tax_excluded | 0         |
      | discount_price_tax_included | 0         |
      | final_price_tax_excluded    | 30        |
      | final_price_tax_included    | 30        |

  Scenario: Compute base price for a product with combination
    Given I add product "product2" with following information:
      | name[en-US] | Colored T-Shirt |
      | type        | combinations    |
    And I update product "product2" with following values:
      | price      | 100.000000 |
      | unit_price | 10.000000  |
    And attribute group "Size" named "Size" in en language exists
    And attribute "S" named "S" in en language exists
    And I generate combinations for product "product2" using following attributes:
      | Size | [S] |
    And product "product2" should have following combinations:
      | id reference  | combination name | reference | attributes | impact on price | quantity | is default |
      | product2SSize | Size - S         |           | [Size:S]   | 0               | 0        | true       |
    And I update combination "product2SSize" with following values:
      | impact on price      | 15.500000 |
      | impact on unit price | 2.500000  |
    When I compute the product price for product "product2" with combination "product2SSize" and quantity 1 I should get:
      | original_price_tax_excluded | 115.500000 |
      | original_price_tax_included | 115.500000 |
      | unit_price_tax_excluded     | 12.500000  |
      | unit_price_tax_included     | 12.500000  |
      | discount_price_tax_excluded | 0          |
      | discount_price_tax_included | 0          |
      | final_price_tax_excluded    | 116        |
      | final_price_tax_included    | 116        |
