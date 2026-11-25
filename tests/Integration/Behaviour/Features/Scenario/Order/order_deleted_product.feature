# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-deleted-product
@restore-all-tables-before-feature
@clear-cache-before-feature
@order-deleted-product
Feature: Edit an order whose product was removed from the catalogue
  In order to keep invoiced amounts stable
  As a BO user
  I need order totals to survive editing an order that contains a product deleted from the catalogue

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |

  # Changing the shipping address moves the tax address, so the tax included totals below are the
  # ones the new address produces - the point of the pair is that deleting the product from the
  # catalogue must leave the order with exactly the same amounts as leaving it in place.
  Scenario: CONTROL changing the shipping address of an order whose products all still exist
    Given order "bo_order1" should have following details:
      | total_products    | 23.80 |
      | total_products_wt | 25.23 |
    And I create customer "controlCustomer" with following details:
      | firstName | testFirstName          |
      | lastName  | testLastName           |
      | email     | control@mailexample.eu |
      | password  | secret                 |
    And I add new address to customer "controlCustomer" with following details:
      | Address alias | control-address                          |
      | First name    | testFirstName               |
      | Last name     | testLastName                |
      | Address       | Work address st. 1234567890 |
      | City          | Birmingham                  |
      | Country       | United States               |
      | State         | Alabama                     |
      | Postal code   | 12345                       |
    When I change order "bo_order1" shipping address to "control-address"
    Then order "bo_order1" should have following details:
      | total_products    | 23.80 |
      | total_products_wt | 23.80 |

  Scenario: Changing the shipping address does not zero the totals of an order whose product is gone
    Given order "bo_order1" should have following details:
      | total_products    | 23.80 |
      | total_products_wt | 25.23 |
    And I create customer "deletedCustomer" with following details:
      | firstName | testFirstName          |
      | lastName  | testLastName           |
      | email     | deleted@mailexample.eu |
      | password  | secret                 |
    And I add new address to customer "deletedCustomer" with following details:
      | Address alias | deleted-address                          |
      | First name    | testFirstName               |
      | Last name     | testLastName                |
      | Address       | Work address st. 1234567890 |
      | City          | Birmingham                  |
      | Country       | United States               |
      | State         | Alabama                     |
      | Postal code   | 12345                       |
    When I delete product "Mug The best is yet to come" from catalogue
    And I change order "bo_order1" shipping address to "deleted-address"
    Then order "bo_order1" should have following details:
      | total_products    | 23.80 |
      | total_products_wt | 23.80 |
