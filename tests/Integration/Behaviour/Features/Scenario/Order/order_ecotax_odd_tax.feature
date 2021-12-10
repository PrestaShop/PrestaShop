# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags odd-order-ecotax
@restore-all-tables-before-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@order-ecotax
@odd-order-ecotax
Feature: Ecotax for Order in Back Office (BO)

  Background:
    Given email sending is disabled
    And shop configuration for "PS_USE_ECOTAX" is set to 1
    And the current currency is "USD"
    And country "FR" is enabled
    ## Create Tax
    And I add new tax "fr-tax-21" with following properties:
      | name         | FR Tax (21%)  |
      | rate         | 21            |
      | is_enabled   | true          |
    And I add the tax rule group "fr-tax-21-group" for the tax "fr-tax-21" with the following conditions:
      | name         | FR Tax (21%)  |
      | country      | FR            |
    ## Create Product
    And there is a product in the catalog named "Free Product" with a price of 0.0 and 100 items in stock
    Then the available stock for product "Free Product" should be 100
    ## Create Product
    And there is a product in the catalog named "Test Ecotax Product" with a price of 15.0 and 100 items in stock
    Then the available stock for product "Test Ecotax Product" should be 100
    And I set tax rule group "fr-tax-21-group" to product "Test Ecotax Product"
    ## Enable payment
    And the module "dummy_payment" is installed
    ## Employee
    And I am logged in as "test@prestashop.com" employee
    ## Customer
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "FR" country
    ## Carrier
    And a carrier "default_carrier" with name "My carrier" exists
    And I associate the tax rule group "fr-tax-21-group" to carrier "default_carrier"
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
    When I set tax rule group "fr-tax-21-group" to product "Test Ecotax Product"
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product |
      | amount        | 97                  |
      | price         | 15.00               |
      | price_tax_incl| 18.15               |
    ## Check informations
    Then order "bo_order1" should contain 97 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 3
    And order "bo_order1" should have 98 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity            | 97      |
      | ecotax                      | 0.00    |
      | product_price               | 15.00   |
      | original_product_price      | 15.00   |
      | unit_price_tax_excl         | 15.00   |
      | unit_price_tax_incl         | 18.15   |
      | total_price_tax_excl        | 1455.00 |
      | total_price_tax_incl        | 1760.55 |
    When I generate invoice for "bo_order1" order
    And the first invoice from order "bo_order1" should have following details:
      | total_products              | 1455.00 |
      | total_products_wt           | 1760.55 |
      | total_discount_tax_excl     | 0.0     |
      | total_discount_tax_incl     | 0.0     |
      | total_paid_tax_excl         | 1462.00 |
      | total_paid_tax_incl         | 1769.02 |
      | total_shipping_tax_excl     | 7.0     |
      | total_shipping_tax_incl     | 8.47    |
    ## Reset
    When I remove product "Test Ecotax Product" from order "bo_order1"
    Then order "bo_order1" should have 1 products in total

  Scenario: Add product (with ecotax) to an Order
    ## Set EcoTax
    When the product "Test Ecotax Product" ecotax is 5.12
    And I set tax rule group "fr-tax-21-group" to product "Test Ecotax Product"
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product |
      | amount        | 97                  |
      | price         | 20.12               |
      | price_tax_incl| 23.27               |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 97 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 3
    And order "bo_order1" should have 98 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity            | 97      |
      | ecotax                      | 5.12    |
      | product_price               | 20.12   |
      # 20.12 = 15 + 5.12
      | original_product_price      | 20.12   |
      # 20.12 = 15 + 5.12
      | unit_price_tax_excl         | 20.12   |
      # 20.12 = 15 + 5.12
      | unit_price_tax_incl         | 23.27   |
      # 21.02 = (15 + 21%) + 5.12
      | total_price_tax_excl        | 1951.64 |
      # 1951.64 = 20.12 * 97
      | total_price_tax_incl        | 2257.19 |
      # 2257.19 = 23.27 * 97
    When I generate invoice for "bo_order1" order
    And the first invoice from order "bo_order1" should have following details:
      | total_products              | 1951.64 |
      | total_products_wt           | 2257.19 |
      | total_discount_tax_excl     | 0.0     |
      | total_discount_tax_incl     | 0.0     |
      | total_paid_tax_excl         | 1958.64 |
      | total_paid_tax_incl         | 2265.66 |
      | total_shipping_tax_excl     | 7.0     |
      | total_shipping_tax_incl     | 8.47    |
    # Reset
    When the product "Test Ecotax Product" ecotax is 0.00
    Then the ecotax of the product "Test Ecotax Product" should be 0.00
    When I remove product "Test Ecotax Product" from order "bo_order1"
    And order "bo_order1" should have 1 products in total

  Scenario: Add product with modified price (with ecotax) to an existing Order
    ## Set EcoTax
    When the product "Test Ecotax Product" ecotax is 5.12
    And I set tax rule group "fr-tax-21-group" to product "Test Ecotax Product"
    ## Add products to order
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product |
      | amount        | 98                  |
      | price         | 20.83               |
      | price_tax_incl| 24.1291             |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 98 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 2
    And order "bo_order1" should have 99 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity            | 98        |
      | ecotax                      | 5.12      |
      | product_price               | 20.83     |
      # 20.83 = 15.71 + 5.12
      | original_product_price      | 20.12     |
      # 20.12 = 15 + 5.12
      | unit_price_tax_excl         | 20.83     |
      # 20.83 = 15.71 + 5.12
      | unit_price_tax_incl         | 24.1291   |
      # 24.1291 = (15.71 + 21%) + 5.12
      | total_price_tax_excl        | 2041.34   |
      # 2041.34 = 20.83 * 98
      | total_price_tax_incl        | 2364.65   |
      # 2364.6518 = 24.1291 * 98
    When I generate invoice for "bo_order1" order
    And the first invoice from order "bo_order1" should have following details:
      | total_products              | 2041.34   |
      | total_products_wt           | 2364.65   |
      | total_discount_tax_excl     | 0.0       |
      | total_discount_tax_incl     | 0.0       |
      | total_paid_tax_excl         | 2048.34   |
      | total_paid_tax_incl         | 2373.12   |
      | total_shipping_tax_excl     | 7.0       |
      | total_shipping_tax_incl     | 8.47      |
    # Reset
    When the product "Test Ecotax Product" ecotax is 0.00
    Then the ecotax of the product "Test Ecotax Product" should be 0.00
    When I remove product "Test Ecotax Product" from order "bo_order1"
    And order "bo_order1" should have 1 products in total

  Scenario: Update product with modified price to an Order
    ## Set EcoTax
    When the product "Test Ecotax Product" ecotax is 5.12
    And I set tax rule group "fr-tax-21-group" to product "Test Ecotax Product"
    ## Add products to order
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product |
      | amount        | 98                  |
      | price         | 20.83               |
      | price_tax_incl| 24.1291             |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 98 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 2
    And order "bo_order1" should have 99 products in total
    And order "bo_order1" should have 0 invoices
    ## Update Product Price
    When I edit product "Test Ecotax Product" to order "bo_order1" with following products details:
      | name          | Test Ecotax Product |
      | amount        | 98                  |
      | price         | 24.99               |
      | price_tax_incl| 29.1627             |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 98 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 2
    And order "bo_order1" should have 99 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity            | 98        |
      | ecotax                      | 5.12      |
      | product_price               | 24.99     |
      # 24.99 = 19.87 + 5.12
      | original_product_price      | 20.12     |
      # 20.12 = 15 + 5.12
      | unit_price_tax_excl         | 24.99     |
      # 24.99 = 19.87 + 5.12
      | unit_price_tax_incl         | 29.1627   |
      # 29.1627 = (19.87 + 21%) + 5.12
      | total_price_tax_excl        | 2449.02   |
      # 2449.02 = 24.99 * 98
      | total_price_tax_incl        | 2857.94   |
      # 2857.9446 = 29.1627 * 98
    When I generate invoice for "bo_order1" order
    And the first invoice from order "bo_order1" should have following details:
      | total_products              | 2449.02   |
      | total_products_wt           | 2857.94   |
      | total_discount_tax_excl     | 0.0       |
      | total_discount_tax_incl     | 0.0       |
      | total_paid_tax_excl         | 2456.02   |
      | total_paid_tax_incl         | 2866.41   |
      | total_shipping_tax_excl     | 7.0       |
      | total_shipping_tax_incl     | 8.47      |
    # Reset
    When the product "Test Ecotax Product" ecotax is 0.00
    Then the ecotax of the product "Test Ecotax Product" should be 0.00
    When I remove product "Test Ecotax Product" from order "bo_order1"
    And order "bo_order1" should have 1 products in total

  Scenario: Update product with modified quantity to an Order
    ## Set EcoTax
    When the product "Test Ecotax Product" ecotax is 5.12
    And I set tax rule group "fr-tax-21-group" to product "Test Ecotax Product"
    ## Add products to order
    When I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product |
      | amount        | 98                  |
      | price         | 20.12               |
      | price_tax_incl| 23.27               |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 98 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 2
    And order "bo_order1" should have 99 products in total
    And order "bo_order1" should have 0 invoices
    ## Update Product Price
    When I edit product "Test Ecotax Product" to order "bo_order1" with following products details:
      | name          | Test Ecotax Product |
      | amount        | 99                  |
      | price         | 20.12               |
      | price_tax_incl| 23.27               |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 99 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 1
    And order "bo_order1" should have 100 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity            | 99         |
      | ecotax                      | 5.12    |
      | product_price               | 20.12   |
      # 20.12 = 15 + 5.12
      | original_product_price      | 20.12   |
      # 20.12 = 15 + 5.12
      | unit_price_tax_excl         | 20.12   |
      # 20.12 = 15 + 5.12
      | unit_price_tax_incl         | 23.27   |
      # 21.02 = (15 + 21%) + 5.12
      | total_price_tax_excl        | 1991.88 |
      # 1991.88 = 20.12 * 99
      | total_price_tax_incl        | 2303.73 |
      # 2303.73 = 23.27 * 99
    When I generate invoice for "bo_order1" order
    And the first invoice from order "bo_order1" should have following details:
      | total_products              | 1991.88 |
      | total_products_wt           | 2303.73 |
      | total_discount_tax_excl     | 0.0     |
      | total_discount_tax_incl     | 0.0     |
      | total_paid_tax_excl         | 1998.88 |
      | total_paid_tax_incl         | 2312.20 |
      | total_shipping_tax_excl     | 7.0     |
      | total_shipping_tax_incl     | 8.47    |
    # Reset
    When the product "Test Ecotax Product" ecotax is 0.00
    Then the ecotax of the product "Test Ecotax Product" should be 0.00
    When I remove product "Test Ecotax Product" from order "bo_order1"
    And order "bo_order1" should have 1 products in total
