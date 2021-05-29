#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s image_type
@reset-database-before-feature
Feature: Image types management
  As an employee
  I must be able to add, edit and delete zones

  Scenario: Adding new image type
    When I add new image type "giant" with following properties:
      | name                  | super_hyper_big_type |
      | width                 | 1000                 |
      | height                | 2000                 |
      | products_enabled      | true                 |
      | categories_enabled    | true                 |
      | manufacturers_enabled | true                 |
      | suppliers_enabled     | false                |
      | stores_enabled        | false                |
    Then image type "giant" name should be "super_hyper_big_type"
    And image type "giant" width should be "1000" pixels
    And image type "giant" height should be "2000" pixels
    And image type "giant" products status should be enabled
    And image type "giant" categories status should be enabled
    And image type "giant" manufacturers status should be enabled
    And image type "giant" suppliers status should be disabled
    And image type "giant" stores status should be disabled

  Scenario: Editing image type
    When I edit image type "giant" with following properties:
      | name             | small |
      | width            | 2000  |
      | products_enabled | false |
      | stores_enabled   | true  |
    Then image type "giant" name should be "small"
    And image type "giant" width should be "2000" pixels
    And image type "giant" height should be "2000" pixels
    And image type "giant" products status should be disabled
    And image type "giant" categories status should be enabled
    And image type "giant" manufacturers status should be enabled
    And image type "giant" suppliers status should be disabled
    And image type "giant" stores status should be enabled

  Scenario: Deleting image type
    When I delete image type "giant"
    Then image type "giant" should be deleted
    
  Scenario: Bulk deleting image types
    When I add new image type "first" with following properties:
      | name                  | super_tiny_type      |
      | width                 | 1                    |
      | height                | 2                    |
      | products_enabled      | true                 |
      | categories_enabled    | false                |
      | manufacturers_enabled | true                 |
      | suppliers_enabled     | false                |
      | stores_enabled        | true                 |
    And I add new image type "second" with following properties:
      | name                  | normal               |
      | width                 | 100                  |
      | height                | 200                  |
      | products_enabled      | false                |
      | categories_enabled    | true                 |
      | manufacturers_enabled | false                |
      | suppliers_enabled     | true                 |
      | stores_enabled        | false                |
    Then image type "first" name should be "super_tiny_type"
    And image type "second" name should be "normal"
    When I delete image types: "first, second" using bulk action
    Then image types "first, second" should be deleted
