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
    When I define following customization field as customizationField1:
      | type               | text                                |
      | name               | en-US: front text                   |
      | is required        | true                                |
    And I define following customization field as customizationField2:
      | type               | text                                |
      | name               | en-US: back text                    |
      | is required        | true                                |
    When I update product product1 customization fields with following defined fields:
      | customizationField1 |
      | customizationField2 |
