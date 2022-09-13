# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order --tags order-address
@restore-all-tables-before-feature
@order-address
@clear-cache-before-feature
Feature: Order from Back Office (BO)
  In order to manage orders for FO customers
  As a BO user
  I need to be able to customize orders from the BO

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And country "FR" is enabled
    And country "ES" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And I create an empty cart "dummy_cart" for customer "testCustomer"
    And I add 2 products "Mug The best is yet to come" to the cart "dummy_cart"

  Scenario: Check address when DNI is not defined
    Given I add new address to customer "testCustomer" with following details:
      | Address alias    | test-customer-france-address |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Paris                        |
      | Country          | France                       |
      | Postal code      | 75008                        |
      | DNI              | 12345                        |
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I change order "bo_order1" shipping address to "test-customer-france-address"
    And I change order "bo_order1" invoice address to "test-customer-france-address"
    Then the preview order "bo_order1" has following shipping address
      | Fullname         | testFirstName testLastName   |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Paris                        |
      | Country          | France                       |
      | Postal code      | 75008                        |
      | DNI              |                              |
    And the preview order "bo_order1" has following invoice address
      | Fullname         | testFirstName testLastName   |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Paris                        |
      | Country          | France                       |
      | Postal code      | 75008                        |
      | DNI              |                              |
    And the order "bo_order1" has following shipping address
      | Fullname         | testFirstName testLastName   |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Paris                        |
      | Country          | France                       |
      | Postal code      | 75008                        |
      | DNI              |                              |
    And the order "bo_order1" has the following formatted shipping address
    """
    testFirstName testLastName
    36 Avenue des Champs Elysees
    75008 Paris
    France
    """
    And the order "bo_order1" preview has the following formatted shipping address
    """
    testFirstName testLastName
    36 Avenue des Champs Elysees
    75008 Paris
    France
    """
    And the order "bo_order1" has following invoice address
      | Fullname         | testFirstName testLastName   |
      | Address          | 36 Avenue des Champs Elysees |
      | City             | Paris                        |
      | Country          | France                       |
      | Postal code      | 75008                        |
      | DNI              |                              |
    And the order "bo_order1" has the following formatted invoice address
    """
    testFirstName testLastName
    36 Avenue des Champs Elysees
    75008 Paris
    France
    """
    And the order "bo_order1" preview has the following formatted invoice address
    """
    testFirstName testLastName
    36 Avenue des Champs Elysees
    75008 Paris
    France
    """

  Scenario: Check address when DNI not defined
    Given I add new address to customer "testCustomer" with following details:
      | Address alias    | test-customer-spain-address  |
      | First name       | testFirstName                |
      | Last name        | testLastName                 |
      | Address          | Calle de Bailén              |
      | City             | Madrid                       |
      | Country          | Spain                        |
      | Postal code      | 28071                        |
      | DNI              | 12345                        |
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I change order "bo_order1" shipping address to "test-customer-spain-address"
    And I change order "bo_order1" invoice address to "test-customer-spain-address"
    Then the preview order "bo_order1" has following shipping address
      | Fullname         | testFirstName testLastName   |
      | Address          | Calle de Bailén              |
      | City             | Madrid                       |
      | Country          | Spain                        |
      | Postal code      | 28071                        |
      | DNI              | 12345                        |
    And the preview order "bo_order1" has following invoice address
      | Fullname         | testFirstName testLastName   |
      | Address          | Calle de Bailén              |
      | City             | Madrid                       |
      | Country          | Spain                        |
      | Postal code      | 28071                        |
      | DNI              | 12345                        |
    And the order "bo_order1" has following shipping address
      | Fullname         | testFirstName testLastName   |
      | Address          | Calle de Bailén              |
      | City             | Madrid                       |
      | Country          | Spain                        |
      | Postal code      | 28071                        |
      | DNI              | 12345                        |
    And the order "bo_order1" has the following formatted shipping address
    """
    testFirstName testLastName
    Calle de Bailén
    28071 Madrid
    Spain
    12345
    """
    And the order "bo_order1" has following invoice address
      | Fullname         | testFirstName testLastName   |
      | Address          | Calle de Bailén              |
      | City             | Madrid                       |
      | Country          | Spain                        |
      | Postal code      | 28071                        |
      | DNI              | 12345                        |
    And the order "bo_order1" has the following formatted invoice address
    """
    testFirstName testLastName
    Calle de Bailén
    28071 Madrid
    Spain
    12345
    """

  Scenario: Check shipping details after updating carrier tracking number & url
    Given there is a carrier named "tracking-carrier"
    And I enable carrier "tracking-carrier"
    And I add order "bo_order1" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    And I update order "bo_order1" Tracking number to "" and Carrier to "tracking-carrier"
    Then order "bo_order1" should have "tracking-carrier" as a carrier
    And the preview order "bo_order1" has following shipping details
      | Tracking number  |                              |
      | Tracking URL     |                              |
    Given I update order "bo_order1" Tracking number to "42424242" and Carrier to "tracking-carrier"
    And the carrier "tracking-carrier" uses "http://tracking.url" as tracking url
    Then order "bo_order1" should have "tracking-carrier" as a carrier
    And the preview order "bo_order1" has following shipping details
      | Tracking number  | 42424242                     |
      | Tracking URL     | http://tracking.url          |
    Given the carrier "tracking-carrier" uses "http://tracking.url/@" as tracking url
    Then the preview order "bo_order1" has following shipping details
      | Tracking number  | 42424242                     |
      | Tracking URL     | http://tracking.url/42424242 |
