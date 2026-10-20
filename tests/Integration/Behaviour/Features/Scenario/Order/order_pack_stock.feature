# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-pack-stock
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-pack-stock
Feature: Stock of a pack ordered and then cancelled
  In order to keep the catalog consistent
  As a BO user
  I need the stock a pack took to come back when the order is cancelled

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    And there is a product in the catalog named "packedMug" with a price of 10.00 and 300 items in stock
    And there is a product in the catalog named "mugPack" with a price of 18.00 and 10 items in stock
    And product "mugPack" is a pack containing 2 items of product "packedMug"
    And the pack "mugPack" decrements both packs and products

  Scenario: Cancelling an order gives back the stock the pack took, for the pack and for what it contains
    Given I am logged in as "test@prestashop.com" employee
    And I create an empty cart "pack_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "pack_cart"
    And I add 1 product "mugPack" to the cart "pack_cart"
    And I add order "pack_order" with the following details:
      | cart                | pack_cart        |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |
    Then the available stock for product "mugPack" should be 9
    And the available stock for product "packedMug" should be 298
    When I update order "pack_order" status to "Canceled"
    Then order "pack_order" has status "Canceled"
    And the available stock for product "mugPack" should be 10
    And the available stock for product "packedMug" should be 300
