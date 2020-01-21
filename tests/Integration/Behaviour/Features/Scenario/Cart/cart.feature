# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --name 'Cart management'
@database-scenario
Feature: Cart management
  PrestaShop allows BO users to manage carts
  As a BO user
  I should be able to customize carts

  Background:
    Given I create customer "testCustomer" with following details:
      | firstName        | testFirstName                      |
      | lastName         | testLastName                       |
      | email            | customer@domain.eu                 |
      | password         | secret                             |
    And I create empty cart "dummy_cart" for customer "testCustomer"

  Scenario: remove product from cart
    # later could be changed with "Add product" when it's handler will be migrated
    Given I add 1 products "Mug The best is yet to come" to the cart "dummy_cart"
    Then cart "dummy_cart" has 1 products
    When I delete product "Mug The best is yet to come" from cart "dummy_cart"
    Then cart "dummy_cart" has 0 products
