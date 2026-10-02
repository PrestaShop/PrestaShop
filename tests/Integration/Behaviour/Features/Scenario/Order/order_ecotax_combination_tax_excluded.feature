# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-ecotax-combination-tax-excluded
@restore-all-tables-before-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@order-ecotax
@order-ecotax-combination-tax-excluded
Feature: Ecotax for a combination in the BO when the group displays prices tax excluded

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
    ## Create Product
    And there is a product in the catalog named "Free Product" with a price of 0.0 and 100 items in stock
    Then the available stock for product "Free Product" should be 100
    ## Create Product
    And there is a product in the catalog named "Test Ecotax Product Combination" with a price of 15.0 and 100 items in stock
    And I set tax rule group "fr-tax-6-group" to product "Test Ecotax Product Combination"
    ## Create combination (remember that price column is actually the impact on price)
    And product "Test Ecotax Product Combination" has combinations with following details:
      | reference    | quantity | price | attributes |
      | combination1 | 100      | 1.0   | Size:L     |
      | combination2 | 100      | 2.0   | Size:M     |
    Then the available stock for combination "combination1" of product "Test Ecotax Product Combination" should be 100
    And the available stock for combination "combination2" of product "Test Ecotax Product Combination" should be 100
    ## Enable payment
    And the module "dummy_payment" is installed
    ## Employee
    And I am logged in as "test@prestashop.com" employee
    ## Customer
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "FR" country
    ## Carrier
    And a carrier "default_carrier" with name "My carrier" exists
    And I set tax rule "fr-tax-6-group" for carrier "default_carrier" I get a new carrier referenced as "default_carrier"
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

  Scenario: Add a combination with ecotax to an order when the group shows prices tax excluded
    Given price display method for the group of the customer having email "pub@prestashop.com" is "tax excluded"
    ## Set EcoTax
    When the ecotax for combination "combination1" of the product "Test Ecotax Product Combination" is 5.12
    And I set tax rule group "fr-tax-6-group" to product "Test Ecotax Product Combination"
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name          | Test Ecotax Product Combination |
      | combination   | combination1                    |
      | amount        | 2                               |
      | price         | 21.12                           |
      | price_tax_incl| 22.08                           |
    ## Check informations
    Then the ecotax for combination "combination1" of the product "Test Ecotax Product Combination" should be 5.12
    And the price for combination "combination1" of the product "Test Ecotax Product Combination" is 16.00
    And order "bo_order1" should contain 2 products "Test Ecotax Product Combination"
    And the available stock for combination "combination1" of product "Test Ecotax Product Combination" should be 98
    And the available stock for combination "combination2" of product "Test Ecotax Product Combination" should be 100
    And order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoices
    And product "Test Ecotax Product Combination" in order "bo_order1" has following details:
      | product_quantity            | 2     |
      | ecotax                      | 5.12  |
      | product_price               | 21.12 |
      # 21.12 = 16 + 5.12
      | original_product_price      | 21.12 |
      # 21.12 = 16 + 5.12
      | unit_price_tax_excl         | 21.12 |
      # 21.12 = 16 + 5.12
      | unit_price_tax_incl         | 22.08 |
      # 22.08 = (16 + 6%) + 5.12
      | total_price_tax_excl        | 42.24 |
      # 42.24 = 21.12 * 2
      | total_price_tax_incl        | 44.16 |
      # 44.16 = 22.08 * 2
    When I generate invoice for "bo_order1" order
    And the first invoice from order "bo_order1" should have following details:
      | total_products              | 42.24 |
      | total_products_wt           | 44.16 |
      | total_discount_tax_excl     | 0.0   |
      | total_discount_tax_incl     | 0.0   |
      | total_paid_tax_excl         | 49.24 |
      | total_paid_tax_incl         | 51.58 |
      | total_shipping_tax_excl     | 7.0   |
      | total_shipping_tax_incl     | 7.42  |
    # Reset
    When the product "Test Ecotax Product Combination" ecotax is 0.00
    Then the ecotax of the product "Test Ecotax Product Combination" should be 0.00
    When I remove product "Test Ecotax Product Combination" from order "bo_order1"
    And order "bo_order1" should have 1 products in total

