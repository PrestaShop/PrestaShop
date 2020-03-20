# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags bo-delete-product
@reset-database-before-feature
Feature: Delete product from cart in Back Office (BO)
  As a BO user I must be able to delete products from cart
  Background:
    Given the current currency is "USD"
    And country "US" is enabled
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"

  @bo-delete-product
  Scenario: Delete standard product from cart
    Given I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And cart "dummy_cart" contains product "Mug The best is yet to come"
    When I delete product "Mug The best is yet to come" from cart "dummy_cart"
    Then cart "dummy_cart" should not contain product "Mug The best is yet to come"
