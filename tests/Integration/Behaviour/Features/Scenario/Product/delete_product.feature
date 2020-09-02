# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags delete
@reset-database-before-feature
@clear-cache-after-feature
@delete
Feature: Delete products from Back Office (BO)
  As an employee
  I need to be able to delete product and multiple products at once from BO

  Scenario: I delete product
    Given I add product "product1" with following information:
      | name       | en-US:bottle of ale  |
      | is_virtual | false                |
    And product "product1" type should be standard
    When I delete product product1
    Then product product1 should not exist anymore
