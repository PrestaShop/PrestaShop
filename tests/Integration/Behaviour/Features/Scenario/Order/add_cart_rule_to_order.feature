# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order
@reset-database-before-feature
Feature: Add discounts to order from Back Office (BO)
  As a BO user
  I need to be able to add discounts to existing orders from the BO

  #  todo: fix the failing scenarios/code
  #  todo: make scenarios independent
  #  todo: change legacy classes with domain where possible
  #  todo: increase code re-use

  Background:
    Given the current currency is "USD"
    And country "US" is enabled
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And customer "testCustomer" has an empty cart "dummy_cart"
    And cart "dummy_cart" delivery and invoice address country for customer "testCustomer" is "US"
    And I set Free shipping to the cart "dummy_cart"
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And Order "bo_order1" has following prices:
      | products      | $22.00   |
      | discounts     | 0        |
      | shipping      | 0        |
      | taxes         | 0        |
      | total         | $22.00   |

  @add-discounts-to-order
  Scenario: Add discount to order which has no invoices
    Given order "bo_order1" does not have any invoices
    When I add discount to order "bo_order1" with following details:
      | name      | discount fpf |
      | type      | amount       |
      | value     | 5.50         |
    Then Order "bo_order1" should have following prices:
      | products      | $22.00    |
      | discounts     | $5.50     |
      | shipping      | 0         |
      | taxes         | 0         |
      | total         | $16.50    |
