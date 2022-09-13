# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags delete
@restore-products-before-feature
@clear-cache-before-feature
@delete
Feature: Delete products from Back Office (BO)
  As an employee
  I need to be able to delete product and multiple products at once from BO

  Scenario: I delete product
    Given I add product "product1" with following information:
      | name[en-US] | bottle of ale |
      | type        | standard      |
    And product "product1" type should be standard
    When I delete product product1
    Then product product1 should not exist anymore

  Scenario: I bulk delete products
    Given I add product "product1" with following information:
      | name[en-US] | bottle of wine |
      | type        | standard       |
    Given I add product "product2" with following information:
      | name[en-US] | jar of mead |
      | type        | standard    |
    Given I add product "product3" with following information:
      | name[en-US] | gilded axe |
      | type        | standard   |
    And product "product1" type should be standard
    And product "product2" type should be standard
    And product "product3" type should be standard
    When I bulk delete following products:
      | reference |
      | product1  |
      | product2  |
    Then product product1 should not exist anymore
    And product product2 should not exist anymore
    And product "product3" type should be standard
