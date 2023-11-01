# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-ecotax-cartrule
@restore-all-tables-before-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@order-ecotax
@order-ecotax-cartrule
Feature: Ecotax for Order in Back Office (BO)

  Background:
    Given email sending is disabled
    And shop configuration for "PS_USE_ECOTAX" is set to 1
    And the current currency is "USD"
    And country "FR" is enabled
    ## Create Tax
    And I add new tax "fr-tax-6" with following properties:
      | name       | FR Tax (6%) |
      | rate       | 6           |
      | is_enabled | true        |
    And I add the tax rule group "fr-tax-6-group" for the tax "fr-tax-6" with the following conditions:
      | name    | FR Tax (6%) |
      | country | FR          |
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

  @restore-cart-rules-after-scenario
  Scenario: Add product (without ecotax) to an Order
    ## Set Cart Rule
    And there is a cart rule "cartRuleFiftyPercent" with following properties:
      | name[en-US]               | cartRuleFiftyPercent |
      | discount_percentage       | 50                   |
      | priority                  | 2                    |
      | free_shipping             | false                |
      | discount_application_type | specific_product     |
      | discount_product          | Test Ecotax Product  |
    And cart rule "cartRuleFiftyPercent" is restricted to product "Test Ecotax Product"
    When I set tax rule group "fr-tax-6-group" to product "Test Ecotax Product"
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name           | Test Ecotax Product |
      | amount         | 2                   |
      | price          | 15.00               |
      | price_tax_incl | 15.90               |
    ## Check informations
    Then order "bo_order1" should contain 2 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 98
    And order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 1 cart rule
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity       | 2     |
      | ecotax                 | 0.00  |
      | product_price          | 15.00 |
      | original_product_price | 15.00 |
      | unit_price_tax_excl    | 15.00 |
      | unit_price_tax_incl    | 15.90 |
      | total_price_tax_excl   | 30.00 |
      | total_price_tax_incl   | 31.80 |
    When I generate invoice for "bo_order1" order
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 30.00 |
      | total_products_wt       | 31.80 |
      | total_discount_tax_excl | 15.00 |
      | total_discount_tax_incl | 15.90 |
      | total_paid_tax_excl     | 22.00 |
      | total_paid_tax_incl     | 23.32 |
      | total_shipping_tax_excl | 7.0   |
      | total_shipping_tax_incl | 7.42  |
    ## Reset
    When I remove product "Test Ecotax Product" from order "bo_order1"
    Then order "bo_order1" should have 1 products in total
    And order "bo_order1" should have 0 cart rule

  Scenario: Add product (with ecotax) to an Order
    ## Set Cart Rule
#    When there is a cart rule named "cartRuleFiftyPercent" that applies a percent discount of 50.0% with priority 2, quantity of 1000 and quantity per user 1000
#    And cart rule "cartRuleFiftyPercent" is restricted to product "Test Ecotax Product"
    And there is a cart rule "cartRuleFiftyPercent" with following properties:
      | name[en-US]               | cartRuleFiftyPercent |
      | discount_percentage       | 50                   |
      | priority                  | 2                    |
      | free_shipping             | false                |
      | discount_application_type | specific_product     |
      | discount_product          | Test Ecotax Product  |
    And cart rule "cartRuleFiftyPercent" is restricted to product "Test Ecotax Product"
    ## Set EcoTax
    When the product "Test Ecotax Product" ecotax is 5.12
    And I set tax rule group "fr-tax-6-group" to product "Test Ecotax Product"
    And I add products to order "bo_order1" with new invoice and the following products details:
      | name           | Test Ecotax Product |
      | amount         | 2                   |
      | price          | 20.12               |
      | price_tax_incl | 21.02               |
    ## Check informations
    Then the ecotax of the product "Test Ecotax Product" should be 5.12
    And product "Test Ecotax Product" price is 15.00
    And order "bo_order1" should contain 2 products "Test Ecotax Product"
    And the available stock for product "Test Ecotax Product" should be 98
    And order "bo_order1" should have 3 products in total
    And order "bo_order1" should have 0 invoices
    And order "bo_order1" should have 1 cart rule
    And product "Test Ecotax Product" in order "bo_order1" has following details:
      | product_quantity       | 2     |
      | ecotax                 | 5.12  |
      | product_price          | 20.12 |
      # 20.12 = 15 + 5.12
      | original_product_price | 20.12 |
      # 20.12 = 15 + 5.12
      | unit_price_tax_excl    | 20.12 |
      # 20.12 = 15 + 5.12
      | unit_price_tax_incl    | 21.02 |
      # 21.02 = (15 + 6%) + 5.12
      | total_price_tax_excl   | 40.24 |
      # 40.24 = 20.12 * 2
      | total_price_tax_incl   | 42.04 |
      # 42.04 = 21.02 * 2
    When I generate invoice for "bo_order1" order
    And the first invoice from order "bo_order1" should have following details:
      | total_products          | 40.24 |
      | total_products_wt       | 42.04 |
      | total_discount_tax_excl | 20.12 |
      | total_discount_tax_incl | 21.02 |
      | total_paid_tax_excl     | 27.12 |
      | total_paid_tax_incl     | 28.44 |
      | total_shipping_tax_excl | 7.0   |
      | total_shipping_tax_incl | 7.42  |
    # Reset
    When the product "Test Ecotax Product" ecotax is 0.00
    Then the ecotax of the product "Test Ecotax Product" should be 0.00
    When I remove product "Test Ecotax Product" from order "bo_order1"
    And order "bo_order1" should have 1 products in total
    And order "bo_order1" should have 0 cart rule
