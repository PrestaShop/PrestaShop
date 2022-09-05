# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s address --tags customer-address
@restore-all-tables-before-feature
@customer-address
Feature: Address
  PrestaShop allows BO users to manage customer addresses
  As a BO user
  I should be able to customize customer addresses

  # NOTE: these scenario cannot be run independently you need to run them all in the right order
  Scenario: add customer address
    Given I create customer "testFirstName" with following details:
      | firstName        | testFirstName                      |
      | lastName         | testLastName                       |
      | email            | test.davidsonas@invertus.eu        |
      | password         | secret                             |
    When I add new address to customer "testFirstName" with following details:
      | Address alias    | test-customer-address              |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    Then customer "testFirstName" should have address "test-customer-address" with following details:
      | Address alias    | test-customer-address              |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |

  Scenario: edit customer address not assigned to any order
    When I edit address "test-customer-address" with following details:
      | Address alias    | test-edited-customer-address |
      | First name       | testFirstNameuh              |
      | Last name        | testLastNameuh               |
      | Address          | Work address st. 1234567890  |
      | City             | Miami                        |
      | Country          | United States                |
      | State            | Florida                      |
      | Postal code      | 12345                        |
    Then customer "testFirstName" should have address "test-customer-address" with following details:
      | Address alias    | test-edited-customer-address |
      | First name       | testFirstNameuh              |
      | Last name        | testLastNameuh               |
      | Address          | Work address st. 1234567890  |
      | City             | Miami                        |
      | Country          | United States                |
      | State            | Florida                      |
      | Postal code      | 12345                        |
    # The address is not assigned to an order, so it is not duplicated nor deleted, simply updated
    And customer "testFirstName" should have 1 addresses
    And customer "testFirstName" should have 0 deleted addresses

  Scenario: edit customer address assigned to an order
    Given address "test-customer-address" is assigned to an order "test-customer-order" for "testFirstName"
    When I edit address "test-customer-address" with following details:
      | Address alias    | test-order-address                 |
      | First name       | testFirstNameuhmeuh                |
      | Last name        | testLastNameuhmeuh                 |
      | Address          | Work address st. 1234567890        |
      | City             | Birminghameuhmeuh                  |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    Then customer "testFirstName" should have address "test-order-address" with following details:
      | Address alias    | test-order-address                 |
      | First name       | testFirstNameuhmeuh                |
      | Last name        | testLastNameuhmeuh                 |
      | Address          | Work address st. 1234567890        |
      | City             | Birminghameuhmeuh                  |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    # The former address has been copied and is unchanged
    And customer "testFirstName" should have address "test-customer-address" with following details:
      | Address alias    | test-edited-customer-address       |
      | First name       | testFirstNameuh                    |
      | Last name        | testLastNameuh                     |
      | Address          | Work address st. 1234567890        |
      | City             | Miami                              |
      | Country          | United States                      |
      | State            | Florida                            |
      | Postal code      | 12345                              |
    # The former address has been soft deleted, and a new one has been created
    And customer "testFirstName" should have 1 addresses
    And customer "testFirstName" should have 1 deleted addresses

  Scenario: edit order delivery address (already deleted)
    # We assign a deleted address to the order
    Given address "test-customer-address" is assigned to an order "test-delivery-order" for "testFirstName"
    When I edit delivery address for order "test-delivery-order" with following details:
      | Address alias    | test-customer-delivery-address     |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    Then customer "testFirstName" should have address "test-customer-delivery-address" with following details:
      | Address alias    | test-customer-delivery-address     |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    # The initially deleted address is still intact
    And customer "testFirstName" should have address "test-customer-address" with following details:
      | Address alias    | test-edited-customer-address       |
      | First name       | testFirstNameuh                    |
      | Last name        | testLastNameuh                     |
      | Address          | Work address st. 1234567890        |
      | City             | Miami                              |
      | Country          | United States                      |
      | State            | Florida                            |
      | Postal code      | 12345                              |
    # A new address has been created, test-order-address is not deleted but test-customer-address still is
    And customer "testFirstName" should have 2 addresses
    And customer "testFirstName" should have 1 deleted addresses
    And order "test-delivery-order" should have "test-customer-address" as a invoice address
    And order "test-delivery-order" should have "test-customer-delivery-address" as a delivery address

  Scenario: edit order invoice address (address not deleted)
    # We assign a not deleted address to the order
    Given address "test-order-address" is assigned to an order "test-invoice-order" for "testFirstName"
    When I edit invoice address for order "test-invoice-order" with following details:
      | Address alias    | test-customer-invoice-address      |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    Then customer "testFirstName" should have address "test-customer-invoice-address" with following details:
      | Address alias    | test-customer-invoice-address      |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    # test-order-address has not been modified, it is now deleted
    And customer "testFirstName" should have address "test-order-address" with following details:
      | Address alias    | test-order-address                 |
      | First name       | testFirstNameuhmeuh                |
      | Last name        | testLastNameuhmeuh                 |
      | Address          | Work address st. 1234567890        |
      | City             | Birminghameuhmeuh                  |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    # Now 2 addresses the new one edited for an invoice, and this new one edited for delivery
    And customer "testFirstName" should have 2 addresses
    # And now 2 deleted addresses since test-order-address is now deleted
    And customer "testFirstName" should have 2 deleted addresses
    And order "test-invoice-order" should have "test-order-address" as a delivery address
    And order "test-invoice-order" should have "test-customer-invoice-address" as a invoice address

  Scenario: edit order delivery address change country (not deleted address)
    Given address "test-customer-invoice-address" is assigned to an order "test-country-order" for "testFirstName"
    # We assign a not deleted address to the order
    When I edit delivery address for order "test-country-order" with following details:
      | Address alias    | test-customer-france-address |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | Work address st. 1234567890  |
      | City             | Birmingham                   |
      | Country          | France                       |
      | Postal code      | 12345                        |
    # The state is automatically reset because France has no states
    Then customer "testFirstName" should have address "test-customer-france-address" with following details:
      | Address alias    | test-customer-france-address |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | Work address st. 1234567890  |
      | City             | Birmingham                   |
      | Country          | France                       |
      | State            |                              |
      | Postal code      | 12345                        |
    # Now 3 addresses since test-customer-invoice-address is now deleted
    And customer "testFirstName" should have 2 addresses
    And customer "testFirstName" should have 3 deleted addresses
    And order "test-country-order" should have "test-customer-france-address" as a delivery address
    And order "test-country-order" should have "test-customer-invoice-address" as a invoice address

  Scenario: edit cart delivery address (not deleted, assigned to an order)
    Given address "test-customer-france-address" is assigned to a cart "test-delivery-cart" for "testFirstName"
    When I edit delivery address for cart "test-delivery-cart" with following details:
      | Address alias    | test-customer-cart-address         |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    Then customer "testFirstName" should have address "test-customer-cart-address" with following details:
      | Address alias    | test-customer-cart-address         |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    # The initially deleted address is still intact
    And customer "testFirstName" should have address "test-customer-address" with following details:
      | Address alias    | test-edited-customer-address       |
      | First name       | testFirstNameuh                    |
      | Last name        | testLastNameuh                     |
      | Address          | Work address st. 1234567890        |
      | City             | Miami                              |
      | Country          | United States                      |
      | State            | Florida                            |
      | Postal code      | 12345                              |
    # A new address has been created, test-customer-cart-address has been deleted
    And customer "testFirstName" should have 2 addresses
    And customer "testFirstName" should have 4 deleted addresses
    And cart "test-delivery-cart" should have "test-customer-france-address" as a invoice address
    And cart "test-delivery-cart" should have "test-customer-cart-address" as a delivery address

  Scenario: edit cart invoice address (already deleted)
    # We assign a deleted address to the order
    Given address "test-customer-france-address" is assigned to a cart "test-delivery-cart" for "testFirstName"
    When I edit invoice address for cart "test-delivery-cart" with following details:
      | Address alias    | test-invoice-cart-address          |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    Then customer "testFirstName" should have address "test-invoice-cart-address" with following details:
      | Address alias    | test-invoice-cart-address          |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    # The initially deleted address is still intact
    And customer "testFirstName" should have address "test-customer-address" with following details:
      | Address alias    | test-edited-customer-address       |
      | First name       | testFirstNameuh                    |
      | Last name        | testLastNameuh                     |
      | Address          | Work address st. 1234567890        |
      | City             | Miami                              |
      | Country          | United States                      |
      | State            | Florida                            |
      | Postal code      | 12345                              |
    # A new address has been created, the edited address was already deleted so the number of deleted addresses remains the same
    And customer "testFirstName" should have 3 addresses
    And customer "testFirstName" should have 4 deleted addresses
    And cart "test-delivery-cart" should have "test-invoice-cart-address" as a invoice address
    And cart "test-delivery-cart" should have "test-customer-france-address" as a delivery address
