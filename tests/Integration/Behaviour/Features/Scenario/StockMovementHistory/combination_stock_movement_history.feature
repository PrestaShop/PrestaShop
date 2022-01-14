# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags combination-stock-movement-history
@restore-all-tables-before-feature
@clear-cache-before-feature
@product-combination
@combination-stock-movement-history
Feature: Search stock movement history from Back Office (BO)
  As a BO user
  I need to be able to search stock movement history for a combination from BO

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists
    And I generate combinations for product "product1" using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1Blue   | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |

  Scenario: I can get the last 5 rows of stock movement history by default and I can paginate
    When I update combination "product1SBlack" stock with following details:
      | delta quantity | 100 |
    When I create an empty cart "dummy_cart1" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart1"
    And I add 2 items of combination "product1SBlack" of the product "product1" to the cart "dummy_cart1"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart1                |
      | message             | order1                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    When I create an empty cart "dummy_cart2" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart2"
    And I add 3 items of combination "product1SBlack" of the product "product1" to the cart "dummy_cart2"
    And I add order "bo_order2" with the following details:
      | cart                | dummy_cart2                |
      | message             | order2                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    When I update combination "product1SBlack" stock with following details:
      | delta quantity | 10 |
    When I create an empty cart "dummy_cart3" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart3"
    And I add 4 items of combination "product1SBlack" of the product "product1" to the cart "dummy_cart3"
    And I add order "bo_order3" with the following details:
      | cart                | dummy_cart3                |
      | message             | order3                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    When I create an empty cart "dummy_cart4" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart4"
    And I add 5 items of combination "product1SBlack" of the product "product1" to the cart "dummy_cart4"
    And I add order "bo_order4" with the following details:
      | cart                | dummy_cart4                |
      | message             | order4                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    When I update combination "product1SBlack" stock with following details:
      | delta quantity | 5 |
    When I create an empty cart "dummy_cart5" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart5"
    And I add 3 items of combination "product1SBlack" of the product "product1" to the cart "dummy_cart5"
    And I add order "bo_order5" with the following details:
      | cart                | dummy_cart5                |
      | message             | order5                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    When I create an empty cart "dummy_cart6" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart6"
    And I add 1 items of combination "product1SBlack" of the product "product1" to the cart "dummy_cart6"
    And I add order "bo_order6" with the following details:
      | cart                | dummy_cart6                |
      | message             | order6                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    When I create an empty cart "dummy_cart7" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart7"
    And I add 2 items of combination "product1SBlack" of the product "product1" to the cart "dummy_cart7"
    And I add order "bo_order7" with the following details:
      | cart                | dummy_cart7                |
      | message             | order7                     |
      | payment module name | dummy_payment              |
      | status              | Delivered                  |
    When I search stock movement history of combination "product1SBlack" I should get following results:
      | type   | first_name | last_name | delta_quantity |
      | group  |            |           | -6             |
      | single | Puff       | Daddy     | 5              |
      | group  |            |           | -9             |
      | single | Puff       | Daddy     | 10             |
      | group  |            |           | -5             |
    When I search stock movement history of combination "product1SBlack" with offset 0 and limit 6 I should get following results:
      | type   | first_name | last_name | delta_quantity |
      | group  |            |           | -6             |
      | single | Puff       | Daddy     | 5              |
      | group  |            |           | -9             |
      | single | Puff       | Daddy     | 10             |
      | group  |            |           | -5             |
      | single | Puff       | Daddy     | 100            |
    When I search stock movement history of combination "product1SBlack" with offset 1 and limit 5 I should get following results:
      | type   | first_name | last_name | delta_quantity |
      | single | Puff       | Daddy     | 5              |
      | group  |            |           | -9             |
      | single | Puff       | Daddy     | 10             |
      | group  |            |           | -5             |
      | single | Puff       | Daddy     | 100            |
    When I search stock movement history of combination "product1SBlack" with offset 2 and limit 3 I should get following results:
      | type   | first_name | last_name | delta_quantity |
      | group  |            |           | -9             |
      | single | Puff       | Daddy     | 10             |
      | group  |            |           | -5             |
