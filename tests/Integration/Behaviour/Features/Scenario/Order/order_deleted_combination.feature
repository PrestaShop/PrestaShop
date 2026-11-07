# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-deleted-combination
@restore-all-tables-before-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@order-deleted-combination
Feature: Edit an order whose combination was removed from the catalogue
  In order to keep invoiced amounts stable
  As a BO user
  I need to still be able to edit an order that contains a combination deleted from the catalogue

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And there is a product in the catalog named "Deleted Combination Product" with a price of 10.0 and 100 items in stock
    And product "Deleted Combination Product" has combinations with following details:
      | reference    | quantity | price | attributes |
      | combination1 | 100      | 0.0   | Size:L     |
      | combination2 | 100      | 0.0   | Size:M     |
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And a carrier "default_carrier" with name "My carrier" exists
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "dummy_cart"
    And I add 2 items of combination "combination1" of the product "Deleted Combination Product" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart       |
      | message             | test             |
      | payment module name | dummy_payment    |
      | status              | Payment accepted |

  # The pair is the point: deleting the ordered combination from the catalogue has to leave the order
  # with exactly the amounts the control keeps when the combination is left in place.
  Scenario: CONTROL changing the shipping address of an order whose combination still exists
    Given order "bo_order1" should have following details:
      | total_products | 20.00 |
    And I create customer "controlCombinationCustomer" with following details:
      | firstName | testFirstName                     |
      | lastName  | testLastName                      |
      | email     | control-combination@mailexample.eu |
      | password  | secret                            |
    And I add new address to customer "controlCombinationCustomer" with following details:
      | Address alias | control-combination-address |
      | First name    | testFirstName               |
      | Last name     | testLastName                |
      | Address       | Work address st. 1234567890 |
      | City          | Birmingham                  |
      | Country       | United States               |
      | State         | Alabama                     |
      | Postal code   | 12345                       |
    When I change order "bo_order1" shipping address to "control-combination-address"
    Then order "bo_order1" should have following details:
      | total_products | 20.00 |

  Scenario: Changing the shipping address still works once the ordered combination is deleted
    Given order "bo_order1" should have following details:
      | total_products | 20.00 |
    And I create customer "deletedCombinationCustomer" with following details:
      | firstName | testFirstName                      |
      | lastName  | testLastName                       |
      | email     | deleted-combination@mailexample.eu |
      | password  | secret                             |
    And I add new address to customer "deletedCombinationCustomer" with following details:
      | Address alias | deleted-combination-address |
      | First name    | testFirstName               |
      | Last name     | testLastName                |
      | Address       | Work address st. 1234567890 |
      | City          | Birmingham                  |
      | Country       | United States               |
      | State         | Alabama                     |
      | Postal code   | 12345                       |
    When I delete combination "combination1" of product "Deleted Combination Product" from catalogue
    And I change order "bo_order1" shipping address to "deleted-combination-address"
    Then order "bo_order1" should have following details:
      | total_products | 20.00 |
