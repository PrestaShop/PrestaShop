# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-customization-fields
@reset-database-before-feature
@update-customization-fields
Feature: Update product customization fields in Back Office (BO)
  As a BO user
  I need to be able to update product customization fields in the BO

  Scenario: I add customization to product
    When I add product "product1" with following information:
      | name       | en-US: nice customizable t-shirt  |
      | is_virtual | false                             |
    And product "product1" type should be standard
    When I update product product1 with following customization fields:
      | reference             | type    | name                    | is required |
      | customization1        | text    | en-US:front-text        | true        |
      | customization2        | text    | en-US:back-text         | false       |
    Then product product1 should have following customization fields:
      | reference             | type    | name                    | is required |
      | customization1        | text    | en-US:front-text        | true        |
      | customization2        | text    | en-US:back-text         | false       |
