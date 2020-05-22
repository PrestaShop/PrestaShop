# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s address
@reset-database-before-feature
Feature: Address
  PrestaShop allows BO users to manage addresses
  As a BO user
  I should be able to customize addresses

  Background:
    #  from the user point of view manufacturer is brand
    Given I add new manufacturer "testBrand" with following properties:
      | name             | testBrand                          |
      | short_description| Makes best shoes in Europe         |
      | description      | Lorem ipsum dolor sit amets ornare |
      | meta_title       | Perfect quality shoes              |
      | meta_description |                                    |
      | meta_keywords    | Boots, shoes, slippers             |
      | enabled          | true                               |

  Scenario: add brand address
    When I add new brand address "testBrandAddress" with following details:
      | Brand            | testBrand                          |
      | Last name        | testLastName                       |
      | First name       | testFirstName                      |
      | Address          | test street 123                    |
      | City             | Kaunas                             |
      | Country          | Lithuania                          |
    Then brand address "testBrandAddress" should have following details:
      | Brand            | testBrand                          |
      | Last name        | testLastName                       |
      | First name       | testFirstName                      |
      | Address          | test street 123                    |
      | City             | Kaunas                             |
      | Country          | Lithuania                          |

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

  Scenario: edit customer address
    When I edit address "test-customer-address" with following details:
      | Address alias    | test-edited-customer-address       |
      | First name       | testFirstNameuh                    |
      | Last name        | testLastNameuh                     |
      | Address          | Work address st. 1234567890        |
      | City             | Birminghameuh                      |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    Then customer "testFirstName" should have address "test-edited-customer-address" with following details:
      | Address alias    | test-edited-customer-address       |
      | First name       | testFirstNameuh                    |
      | Last name        | testLastNameuh                     |
      | Address          | Work address st. 1234567890        |
      | City             | Birminghameuh                      |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
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
    And customer "testFirstName" should have 1 addresses
    And customer "testFirstName" should have 1 deleted addresses

  Scenario: edit order delivery address
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
    # Now 2 addresses since test-order-address is not deleted, and test-customer-address is the one being modified
    And customer "testFirstName" should have 2 addresses
    And customer "testFirstName" should have 1 deleted addresses
    And order "test-delivery-order" should have "test-customer-address" as a invoice address
    And order "test-delivery-order" should have "test-customer-delivery-address" as a delivery address

  Scenario: edit order invoice address
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
    # Now 2 addresses since test-customer-delivery-address is not deleted, as it's not been modified
    And customer "testFirstName" should have 2 addresses
    # And now 2 deleted addresses since test-order-address is now deleted
    And customer "testFirstName" should have 2 deleted addresses
    And order "test-invoice-order" should have "test-order-address" as a delivery address
    And order "test-invoice-order" should have "test-customer-invoice-address" as a invoice address

  Scenario: delete brand address
    Given I add new brand address "testBrandAddress" with following details:
      | Brand            | testBrand                          |
      | Last name        | testLastName                       |
      | First name       | testFirstName                      |
      | Address          | test street 123                    |
      | City             | Kaunas                             |
      | Country          | Lithuania                          |
    When I delete address "testBrandAddress"
    Then brand address "testBrandAddress" does not exist

  Scenario: bulk delete brand addresses
    Given I add new brand address "testBrandAddress1" with following details:
      | Brand            | testBrand                          |
      | Last name        | testLastName                       |
      | First name       | testFirstName                      |
      | Address          | test street 12                     |
      | City             | Kaunas                             |
      | Country          | Lithuania                          |
    And I add new brand address "testBrandAddress2" with following details:
      | Brand            | testBrand                          |
      | Last name        | testLastNameTwo                    |
      | First name       | testFirstNameTwo                   |
      | Address          | test street 123                    |
      | City             | Kaunas                             |
      | Country          | Lithuania                          |
    When I bulk delete addresses "testBrandAddress1,testBrandAddress2"
    Then brand address testBrandAddress1 does not exist
    Then brand address testBrandAddress2 does not exist

  Scenario: edit brand address
    Given I add new brand address "testBrandAddress" with following details:
      | Brand            | testBrand                          |
      | Last name        | testLastName                       |
      | First name       | testFirstName                      |
      | Address          | test street 123                    |
      | City             | Kaunas                             |
      | Country          | Lithuania                          |
    When I edit brand address "testBrandAddress" with following details:
      | Brand            | testBrand                          |
      | Last name        | testLastNameEdited                 |
      | First name       | testFirstNameEdited                |
      | Address          | test street 123                    |
      | City             | Paris                              |
      | Country          | France                             |
    Then brand address "testBrandAddress" should have following details:
      | Brand            | testBrand                          |
      | Last name        | testLastNameEdited                 |
      | First name       | testFirstNameEdited                |
      | Address          | test street 123                    |
      | City             | Paris                              |
      | Country          | France                             |

