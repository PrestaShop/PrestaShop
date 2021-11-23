# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-customer
@reset-database-before-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@order-customer
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is a customer named "testOrderCustomer" whose email is "order+test@prestashop.com"
    And I add new address to customer "testOrderCustomer" with following details:
      | Address alias    | test-customer-address              |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    And customer "testOrderCustomer" has address in "US" country
    And the customer "testOrderCustomer" has SIRET "49791663500061"
    And the customer "testOrderCustomer" has APE "5829C"
    And I create an empty cart "dummy_cart" for customer "testOrderCustomer"
    And I select "US" address as delivery and invoice address for customer "testOrderCustomer" in cart "dummy_cart"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: Check B2B when the B2B mode is disabled
    Given shop configuration for "PS_B2B_ENABLE" is set to 0
    Then the customer of the order "bo_order1" has the APE Code ""
    And the customer of the order "bo_order1" has the SIRET Code ""

  Scenario: Check B2B when the B2B mode is enable
    Given shop configuration for "PS_B2B_ENABLE" is set to 1
    Then the customer of the order "bo_order1" has the APE Code "5829C"
    And the customer of the order "bo_order1" has the SIRET Code "49791663500061"

  Scenario: I check an order after having removed the Customer
    Given email sending is disabled
    And I generate invoice for "bo_order1" order
    And the customer of the order "bo_order1" has been deleted
    Then the customer firstname of the order "bo_order1" is "testFirstName"
    Then the customer lastname of the order "bo_order1" is "testLastName"
    Then the customer id of the order "bo_order1" is "0"
