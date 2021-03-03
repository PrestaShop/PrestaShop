# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-ecotax1
@reset-database-before-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@order-ecotax1
Feature: Ecotax for Order in Back Office (BO)

  Background:
#    Given email sending is disabled
#    And shop configuration for "PS_USE_ECOTAX" is set to 1
#    And the current currency is "USD"
#    And country "FR" is enabled
#    ## Add a tax
#    And I add new tax "odd-tax" with following properties:
#      | name         | Odd Tax (21%) |
#      | rate         | 21            |
#      | is_enabled   | true          |
#    And I add the tax rule group "odd-tax-group" for the tax "odd-tax" with the following conditions:
#      | name         | Odd Tax (21%) |
#      | country      | FR            |
#    ## Create Product
#    And there is a product in the catalog named "Free Product" with a price of 0.0 and 100 items in stock
#    Then the available stock for product "Free Product" should be 100
#    ## Create Product
#    And there is a product in the catalog named "Test Ecotax Product" with a price of 15.0 and 100 items in stock
#    Then the available stock for product "Test Ecotax Product" should be 100
#    ## Create Product
#    And there is a product in the catalog named "Test Ecotax Product Odd Tax" with a price of 15.12 and 100 items in stock
#    And I set tax rule group "odd-tax-group" to product "Test Ecotax Product Odd Tax"
#    Then the available stock for product "Test Ecotax Product Odd Tax" should be 100
#    ## Enable payment
#    And the module "dummy_payment" is installed
#    ## Employee
#    And I am logged in as "test@prestashop.com" employee
#    ## Customer
#    And there is customer "testCustomer" with email "pub@prestashop.com"
#    And customer "testCustomer" has address in "FR" country
#    ## Carrier
#    And a carrier "default_carrier" with name "My carrier" exists
#    ## Cart for Customer
#    And I create an empty cart "dummy_cart" for customer "testCustomer"
#    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
#    When I add 1 products "Free Product" to the cart "dummy_cart"
#    And a carrier "price_carrier" with name "My cheap carrier" exists
#    And I enable carrier "price_carrier"
#    And I associate the tax rule group "odd-tax-group" to carrier "price_carrier"
#    And I select carrier "price_carrier" for cart "dummy_cart"
#    And cart "dummy_cart" should have "price_carrier" as a carrier
#    ## Create Order
#    And I add order "bo_order1" with the following details:
#      | cart                | dummy_cart                 |
#      | message             | test                       |
#      | payment module name | dummy_payment              |
#      | status              | Awaiting bank wire payment |
    Given email sending is disabled
    And the current currency is "USD"
    And country "FR" is enabled
    And I add new tax "odd-tax" with following properties:
      | name         | Odd Tax (21%) |
      | rate         | 21            |
      | is_enabled   | true          |
    And I add the tax rule group "odd-tax-group" for the tax "odd-tax" with the following conditions:
      | name         | Odd Tax (21%) |
      | country      | FR            |
    And there is a product in the catalog named "Test Product With Odd Tax" with a price of 15.12 and 100 items in stock
    And the product "Test Product With Odd Tax" ecotax is 5.12
    And I set tax rule group "odd-tax-group" to product "Test Product With Odd Tax"
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "FR" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 97 products "Test Product With Odd Tax" to the cart "dummy_cart"
    And a carrier "price_carrier" with name "My cheap carrier" exists
    And I enable carrier "price_carrier"
    And I associate the tax rule group "odd-tax-group" to carrier "price_carrier"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And cart "dummy_cart" should have "price_carrier" as a carrier
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Add product (with ecotax) to an Order on a different taxrate
    ## Set EcoTax
    #When
    ## @todo Check tax rule group
    ## Add product to cart and Create order
    #And I add products to order "bo_order1" with new invoice and the following products details:
    #  | name          | Test Product With Odd Tax |
    #  | amount        | 97                          |
    #  | price         | 15.12                       |
    ## Check informations
    Then order "bo_order1" should contain 97 products "Test Product With Odd Tax"
    And the available stock for product "Test Product With Odd Tax" should be 3
    And order "bo_order1" should have 97 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Product With Odd Tax" in order "bo_order1" has following details:
      | product_quantity            | 97    |
      | ecotax                      | 5.12  |
      | product_price               | 20.24 |
      | original_product_price      | 20.24 |
      # 20.12 = 15 + 5.12
      | unit_price_tax_excl         | 20.24 |
      # 20.12 = 15.12 + 5.12
      | unit_price_tax_incl         | 23.4152 |
      # 21.02 = (15.12 + 21%) + 5.12
      | total_price_tax_excl        | 1963.28 |
      # 40.24 = 20.12 * 2
      | total_price_tax_incl        | 2271.27 |
      # 42.04 = 21.02 * 2
    # Reset
    #When I remove product "Test Product With Odd Tax" from order "bo_order1"
    #Then order "bo_order1" should have 1 products in total
    #And order "bo_order1" should have 0 invoices

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
