# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-tax-breakdown-untaxed
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-tax-breakdown-untaxed
Feature: Tax breakdown of an order that mixes a taxed and an untaxed product
  As a merchant
  I must not see a product that carries no tax counted in the base of a rate it does not belong to

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    And there is a product in the catalog named "taxedProduct" with a price of 11.90 and 1000 items in stock
    And there is a product in the catalog named "untaxedProduct" with a price of 10.00 and 1000 items in stock
    And product "untaxedProduct" has following tax rule group id: 0

  Scenario: The untaxed product must not appear in any taxed rate's base
    # 3 units of the untaxed product, because the earlier attempt at this fix subtracted a unit price
    # rather than the line, so it only ever removed one of them.
    Given I am logged in as "test@prestashop.com" employee
    And I create an empty cart "tax_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "tax_cart"
    And I add 1 product "taxedProduct" to the cart "tax_cart"
    And I add 3 products "untaxedProduct" to the cart "tax_cart"
    And I add order "tax_order" with the following details:
      | cart                | tax_cart         |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    Then the product tax breakdown of order "tax_order" should be:
      | rate  | base  | amount |
      | 6.000 | 11.90 | 0.71   |
