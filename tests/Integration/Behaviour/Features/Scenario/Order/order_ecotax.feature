# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-ecotax
@reset-database-before-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@order-ecotax
Feature: Ecotax for Order in Back Office (BO)

  Background:
    Given email sending is disabled
    And shop configuration for "PS_USE_ECOTAX" is set to 1
    And the current currency is "USD"
    And country "US" is enabled
    ## Create Product
    And there is a product in the catalog named "Free Product" with a price of 0.0 and 100 items in stock
    Then the available stock for product "Free Product" should be 100
    ## Create Product
    And there is a product in the catalog named "Test Ecotax Product" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Ecotax Product" should be 100
    ## Enable payment
    And the module "dummy_payment" is installed
    ## Employee
    And I am logged in as "test@prestashop.com" employee
    ## Customer
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "FR" country
    ## Carrier
    And a carrier "default_carrier" with name "My carrier" exists
    ## Cart for Customer
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    When I add 1 products "Free Product" to the cart "dummy_cart"
    ## Create Order
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Add product (without ecotax) to an Order
    ## Add product to cart and Create order
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product |
      | amount        | 2                   |
      | price         | 15.00               |
    ## Check informations
    Then order "bo_order1" should contain 2 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 98
    And order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | ecotax                      | 0.00  |
      | product_price               | 15.00 |
      | original_product_price      | 15.00 |
      | unit_price_tax_excl         | 15.00 |
      | unit_price_tax_incl         | 15.90 |
      | total_price_tax_excl        | 30.00 |
      | total_price_tax_incl        | 31.80 |
    ## Reset
    When I remove product "Test Ecotax Product" from order "bo_order1"
    Then order "bo_order1" should have 1 products in total
    And order "bo_order1" should have 0 invoices

  Scenario: Add product (with ecotax) to an Order
    ## Set EcoTax
    When the product "Test Ecotax Product" ecotax is 5.12
    ## Add product to cart and Create order
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product |
      | amount        | 2                   |
      | price         | 15.00               |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 2 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 98
    And order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | ecotax                      | 5.12  |
      | product_price               | 15.00 |
      | original_product_price      | 20.12 |
      # 20.12 = 15 + 5.12
      | unit_price_tax_excl         | 15.00 |
      # 20.12 = 15 + 5.12
      | unit_price_tax_incl         | 21.02 |
      # 21.02 = (15 + 6%) + 5.12
      | total_price_tax_excl        | 40.24 |
      # 40.24 = 20.12 * 2
      | total_price_tax_incl        | 42.04 |
      # 42.04 = 21.02 * 2
    # Reset
    When I remove product "Test Ecotax Product" from order "bo_order1"
    Then order "bo_order1" should have 1 products in total
    And order "bo_order1" should have 0 invoices
    When the product "Test Ecotax Product" ecotax is 0.00
    Then the ecotax of the product "Test Ecotax Product" should be 0.00

#  Scenario: Add product with modified price (with ecotax) to an Order
#    ## Add products to order
#    When I add products to order "bo_order1" with new invoice and the following products details:
#      | name          | Test Ecotax Product |
#      | amount        | 2                   |
#      | price         | 16                  |
#      | price_tax_incl| 16.96               |
#    ## Check informations
#    Then order "bo_order1" should contain 2 products "Test Ecotax Product"
#    And the available stock for product "Test Ecotax Product" should be 98
#    And order "bo_order1" should have 3 products in total
#    And order "bo_order1" should have 0 invoices
#    And product "Test Ecotax Product" in order "bo_order1" has following details:
#      | product_quantity            | 2         |
#      | ecotax                      | 5.12      |
#      | product_price               | 20.83     |
#      # 20.83 = 16.16 + 5.12
#      | original_product_price      | 20.12     |
#      # 20.12 = 15 + 5.12
#      | unit_price_tax_excl         | 20.830188 |
#      # 20.83 = 16.96 + 5.12
#      | unit_price_tax_incl         | 22.08 |
#      # 22.08 = (16 + 6%) + 5.12
#      | total_price_tax_excl        | 41.66 |
#      # 41.66 = 20.83 * 2
#      | total_price_tax_incl        | 44.16 |
#      # 44.16 = 22.08 * 2
#
#  # Scenario: Update product to an Order
#  # Scenario: Update product with modified price to an Order
#  # Scenario: Update product with modified quantity to an Order
