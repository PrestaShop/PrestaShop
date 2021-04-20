# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-ecotax
@reset-database-before-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@order-ecotax
@order-ecotax-with-tax
Feature: Ecotax for Order in Back Office (BO)

  Background:
    Given email sending is disabled
    And shop configuration for "PS_USE_ECOTAX" is set to 1
    And the current currency is "USD"
    And country "FR" is enabled
    ## Create Tax
    And I add new tax "fr-tax-6" with following properties:
      | name         | FR Tax (6%)   |
      | rate         | 6             |
      | is_enabled   | true          |
    And I add the tax rule group "fr-tax-6-group" for the tax "fr-tax-6" with the following conditions:
      | name         | FR Tax (6%)   |
      | country      | FR            |
    And I add new tax "fr-tax-42" with following properties:
      | name         | FR Tax (42%)  |
      | rate         | 42            |
      | is_enabled   | true          |
    And I add the tax rule group "fr-tax-42-group" for the tax "fr-tax-42" with the following conditions:
      | name         | FR Tax (42%)  |
      | country      | FR            |
    And shop configuration for "PS_ECOTAX_TAX_RULES_GROUP_ID" is set to fr-tax-42-group
    ## Create Product
    And there is a product in the catalog named "Free Product" with a price of 0.0 and 100 items in stock
    Then the available stock for product "Free Product" should be 100
    ## Create Product
    And there is a product in the catalog named "Test Ecotax Product" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Ecotax Product" should be 100
    And I set tax rule group "fr-tax-6-group" to product "Test Ecotax Product"
    ## Enable payment
    And the module "dummy_payment" is installed
    ## Employee
    And I am logged in as "test@prestashop.com" employee
    ## Customer
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "FR" country
    ## Carrier
    And a carrier "default_carrier" with name "My carrier" exists
    And I associate the tax rule group "fr-tax-6-group" to carrier "default_carrier"
    ## Cart for Customer
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    ## Cart > Produits
    When I add 1 products "Free Product" to the cart "dummy_cart"
    ## Cart > Carrier
    And I select carrier "default_carrier" for cart "dummy_cart"
    Then cart "dummy_cart" should have "default_carrier" as a carrier
    ## Create Order
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Add product (without ecotax) to an Order
    When I set tax rule group "fr-tax-6-group" to product "Test Ecotax Product"
    And I add products to order "bo_order1" with new invoice and the following products details:
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

  @order-ecotax-with-tax
  Scenario: Add product (with ecotax) to an Order
    ## Set EcoTax
    When the product "Test Ecotax Product" ecotax is 5.12
    And I set tax rule group "fr-tax-6-group" to product "Test Ecotax Product"
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product |
      | amount        | 2                   |
      | price         | 20.12               |
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
      | ecotax_tax_rate             | 42.00 |
      | product_price               | 20.12 |
      # 20.12 = 15 + 5.12
      | original_product_price      | 20.12 |
      # 20.12 = 15 + 5.12
      | unit_price_tax_excl         | 20.12 |
      # 20.12 = 15 + 5.12
      | unit_price_tax_incl         | 21.02 |
      # 21.02 = (15 + 6%) + 5.12
      | total_price_tax_excl        | 40.24 |
      # 40.24 = 20.12 * 2
      | total_price_tax_incl        | 42.04 |
      # 42.04 = 21.02 * 2
    # Reset
    When the product "Test Ecotax Product" ecotax is 0.00
    Then the ecotax of the product "Test Ecotax Product" should be 0.00
    When I remove product "Test Ecotax Product" from order "bo_order1"
    And order "bo_order1" should have 1 products in total

  Scenario: Add product with modified price (with ecotax) to an existing Order
    ## Set EcoTax
    When the product "Test Ecotax Product" ecotax is 5.12
    And I set tax rule group "fr-tax-6-group" to product "Test Ecotax Product"
    ## Add products to order
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product |
      | amount        | 3                   |
      | price         | 20.83               |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 3 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 97
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity            | 3         |
      | ecotax                      | 5.12      |
      | ecotax_tax_rate             | 42.00     |
      | product_price               | 20.83     |
      # 20.83 = 15.71 + 5.12
      | original_product_price      | 20.12     |
      # 20.12 = 15 + 5.12
      | unit_price_tax_excl         | 20.83     |
      # 20.83 = 15.71 + 5.12
      | unit_price_tax_incl         | 21.7726   |
      # 21.7726 = (15.71 + 6%) + 5.12
      | total_price_tax_excl        | 62.49     |
      # 62.49 = 20.83 * 3
      | total_price_tax_incl        | 65.32     |
      # 65.3178 = 21.7726 * 3
    # Reset
    When the product "Test Ecotax Product" ecotax is 0.00
    Then the ecotax of the product "Test Ecotax Product" should be 0.00
    When I remove product "Test Ecotax Product" from order "bo_order1"
    And order "bo_order1" should have 1 products in total

  Scenario: Update product with modified price to an Order
    ## Set EcoTax
    When the product "Test Ecotax Product" ecotax is 5.12
    And I set tax rule group "fr-tax-6-group" to product "Test Ecotax Product"
    ## Add products to order
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product |
      | amount        | 3                   |
      | price         | 20.83               |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 3 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 97
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoices
    ## Update Product Price
    When I edit product "Test Ecotax Product" to order "bo_order1" with following products details:
      | name          | Test Ecotax Product |
      | amount        | 3                   |
      | price         | 24.99               |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 3 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 97
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity            | 3         |
      | ecotax                      | 5.12      |
      | ecotax_tax_rate             | 42.00     |
      | product_price               | 24.99     |
      # 24.99 = 19.87 + 5.12
      | original_product_price      | 20.12     |
      # 20.12 = 15 + 5.12
      | unit_price_tax_excl         | 24.99     |
      # 24.99 = 19.87 + 5.12
      | unit_price_tax_incl         | 26.1822   |
      # 26.1822 = (15.71 + 6%) + 5.12
      | total_price_tax_excl        | 74.97     |
      # 74.97 = 24.99 * 3
      | total_price_tax_incl        | 78.55     |
      # 78.5466 = 26.1822 * 3
    # Reset
    When the product "Test Ecotax Product" ecotax is 0.00
    Then the ecotax of the product "Test Ecotax Product" should be 0.00
    When I remove product "Test Ecotax Product" from order "bo_order1"
    And order "bo_order1" should have 1 products in total

  Scenario: Update product with modified quantity to an Order
    ## Set EcoTax
    When the product "Test Ecotax Product" ecotax is 5.12
    And I set tax rule group "fr-tax-6-group" to product "Test Ecotax Product"
    ## Add products to order
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product |
      | amount        | 3                   |
      | price         | 20.12               |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 3 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 97
    And order "bo_order1" should have 4 products in total
    And order "bo_order1" should have 0 invoices
    ## Update Product Price
    When I edit product "Test Ecotax Product" to order "bo_order1" with following products details:
      | name          | Test Ecotax Product |
      | amount        | 17                  |
      | price         | 20.12               |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 17 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 83
    And order "bo_order1" should have 18 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity            | 17         |
      | ecotax                      | 5.12       |
      | ecotax_tax_rate             | 42.00      |
      | product_price               | 20.12      |
      # 20.12 = 15 + 5.12
      | original_product_price      | 20.12      |
      # 20.12 = 15 + 5.12
      | unit_price_tax_excl         | 20.12      |
      # 20.12 = 15 + 5.12
      | unit_price_tax_incl         | 21.02      |
      # 21.02 = (15 + 6%) + 5.12
      | total_price_tax_excl        | 342.04     |
      # 342.04 = 20.12 * 17
      | total_price_tax_incl        | 357.34     |
      # 357.34 = 21.02 * 17
    # Reset
    When the product "Test Ecotax Product" ecotax is 0.00
    Then the ecotax of the product "Test Ecotax Product" should be 0.00
    When I remove product "Test Ecotax Product" from order "bo_order1"
    And order "bo_order1" should have 1 products in total
