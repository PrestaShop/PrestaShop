# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags generate-combinations
@reset-database-before-feature
@clear-cache-before-feature
@generate-combinations
Feature: Generate attribute combinations for product in Back Office (BO)
  As an employee
  I need to be able to generate product attribute combinations from BO

  Background:
    Given language with iso code "en" is the default one
    And attribute group "size" named "Size" in en language exists
    And attribute group "color" named "Color" in en language exists
    And attribute "s" named "S" in en language exists
    And attribute "m" named "M" in en language exists
    And attribute "l" named "L" in en language exists
    And attribute "white" named "White" in en language exists
    And attribute "black" named "Black" in en language exists
    And attribute "blue" named "Blue" in en language exists
    And attribute "red" named "Red" in en language exists

  Scenario: Generate product combinations
    When I add product "product1" with following information:
      | name       | en-US:universal T-shirt |
      | is_virtual | false                   |
    Then product product1 type should be standard
    When I generate combinations for product product1 using following attributes:
      | size       | [s,m,l]                 |
      | color      | [white,black,blue,red]  |
    Then product product1 should have following combinations:
