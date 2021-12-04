# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags forbidden-combination-actions
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@forbidden-combination-actions
Feature: Combination actions are forbidden on product which do not have combinations type (BO)
  As an employee
  I can not perform combination actions on standard, pack or virtual products

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists

  Scenario: Combination commands can only be done on product with type combinations
    When I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | standard          |
    Then product product1 type should be standard
    And product product1 does not have a default combination
    When I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then I should get error that this action is allowed for combinations product only
    And product "product1" type should be standard
    And product "product1" should have no attribute groups
