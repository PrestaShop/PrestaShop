# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-exploratory
@reset-database-before-feature
@order-exploratory
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I should be able to have correct totals for all kind of orders

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "FR" is enabled
    And I add new tax "alien-tax" with following properties:
      | name       | alien Tax (21.8%) |
      | rate       | 21.8              |
      | is_enabled | true              |
    And I add the tax rule group "alien-tax-group" for the tax "alien-tax" with the following conditions:
      | name    | alien Tax (21.8%) |
      | country | FR                |
    And I set tax rule group "alien-tax-group" to product "Mug The best is yet to come"
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "FR" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "FR" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And a carrier "price_carrier" with name "My cheap carrier" exists
    And I enable carrier "price_carrier"
    And I associate the tax rule group "alien-tax-group" to carrier "price_carrier"
    And I select carrier "price_carrier" for cart "dummy_cart"
    And cart "dummy_cart" should have "price_carrier" as a carrier
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    Given order "bo_order1" does not have any invoices
    And product "Mug The best is yet to come" in order "bo_order1" has following details:
      | product_quantity     | 2       |
      | product_price        | 11.90   |
      | unit_price_tax_incl  | 14.4942 |
      | unit_price_tax_excl  | 11.90   |
      | total_price_tax_incl | 28.99   |
      | total_price_tax_excl | 23.80   |

  Scenario: Use two 33% cart rules
    Given there is a cart rule named "DivideItByThree" that applies a percent discount of 33.3% with priority 1, quantity of 1000 and quantity per user 1000
    # Above cart rule DivideItByThree is automatically added to order as it has no conditions
    And I add discount to order "bo_order1" with following details:
      | name  | discount one third |
      | type  | percent            |
      | value | 33.3               |
    When I edit product "Mug The best is yet to come" to order "bo_order1" with following products details:
      | amount         | 2         |
      | price          | 82.101806 |
      | price_tax_incl | 100.00    |
    Then product "Mug The best is yet to come" in order "bo_order1" should have specific price 82.101806
    And order "bo_order1" should have 2 products in total
    And order "bo_order1" should have following details:
      | total_products           | 164.2     |
      #  164.2 - (164.2*1.333) = 109,5214 after 1st 33% discount
      #  109,5214 - (54,6786*1.333) = 73,0507738  after 2nd 33% discount
      #  so total discount is 164.2 - 73,0507738
      | total_discounts_tax_excl | 91.150000 |
    And order "bo_order1" should have cart rule "DivideItByThree" with amount "$54.68"
    And order "bo_order1" should have cart rule "discount one third" with amount "$36.47"
