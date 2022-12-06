# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-product
@restore-all-tables-before-feature
@reset-product-price-cache
@order-product
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"

  Scenario: Get informations about a customized deleted product
    ## Add a new product
    When I add product "product12345" with following information:
      | name[en-US] | Customizable Postcard |
      | type        | standard              |
    And I update product product12345 with following customization fields:
      | reference   | type | name[en-US] | is required |
      | customField | text | back image  | false       |
    And I update product "product12345" stock with following information:
      | out_of_stock_type             | available |
    And I update product "product12345" with following values:
      | reference | product12345      |
    Then product "product12345" should allow customization
    And product product12345 should have 1 customizable text field
    And product "product12345" should have following stock information:
      | out_of_stock_type             | available |
    ## Add product to cart
    When I add 2 customized products with reference "product12345" with all its customizations to the cart "dummy_cart"
    ## Select address
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    ## Place order
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    ## Check order
    Then order "bo_order1" should contain 2 products "Customizable Postcard"
    ## Check customization
    Then the order "bo_order1" should have following customizations:
      | productReference | type | name       | value |
      | product12345     | text | back image | Toto  |

    ## Remove product
    When I delete product product12345
    Then product product12345 should not exist anymore
    ## Check customization
    Then the order "bo_order1" should have following customizations:
      | productReference | type | name       | value |
      | product12345     | text |            | Toto  |
