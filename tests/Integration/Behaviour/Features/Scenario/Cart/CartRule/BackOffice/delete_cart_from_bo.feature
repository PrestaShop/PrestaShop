# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags bo-delete-cart
@restore-all-tables-before-feature
@clear-cache-before-feature
@bo-delete-cart
Feature: Cart deleting in BO
  PrestaShop allows BO users to delete carts not already ordered
  As a BO user
  I must be able to delete carts not already ordered in my shop
  Background:
    Given the current currency is "USD"
    And country "US" is enabled
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer1" with email "pub@prestashop.com"
    And there is a customer named "testCustomer2" whose email is "pub2@prestashop.com"
    And there is a customer named "testCustomer3" whose email is "pub3@prestashop.com"

  Scenario: Delete cart not already ordered
    Given I create an empty cart "dummy_cart" for customer "testCustomer1"
    When I delete cart "dummy_cart"
    Then cart "dummy_cart" should be deleted

  Scenario: Block delete cart already ordered
    Given I create an empty cart "dummy_cart" for customer "testCustomer1"
    And I add 3 products "Mountain fox notebook" to the cart "dummy_cart"
    And I add order "bo_order" with the following details:
      | cart                | dummy_cart                 |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    When I delete cart "dummy_cart"
    Then cart "dummy_cart" should exist because cart is already ordered

  Scenario: Bulk delete carts not already ordered
    Given I create an empty cart "dummy_cart1" for customer "testCustomer1"
    And I create an empty cart "dummy_cart2" for customer "testCustomer2"
    And I create an empty cart "dummy_cart3" for customer "testCustomer3"
    When I bulk delete carts "dummy_cart1,dummy_cart2"
    Then cart "dummy_cart1" should be deleted
    And cart "dummy_cart2" should be deleted
    And cart "dummy_cart3" should exist

  Scenario: Bulk delete carts not already ordered but one already ordered
    Given I create an empty cart "dummy_cart1" for customer "testCustomer1"
    And I add 3 products "Mountain fox notebook" to the cart "dummy_cart1"
    And I create an empty cart "dummy_cart2" for customer "testCustomer2"
    And I create an empty cart "dummy_cart3" for customer "testCustomer3"
    And I add order "bo_order" with the following details:
      | cart                | dummy_cart1                |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |
    When I bulk delete carts "dummy_cart1,dummy_cart2"
    Then cart "dummy_cart1" should exist because cart is already ordered
    And cart "dummy_cart2" should exist
    And cart "dummy_cart3" should exist
