# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add-customization-field
@reset-database-before-feature
@add-customization-field
Feature: Add customization fields to product in Back Office (BO)
  As a BO user
  I need to be able to add add new customization fields to product from the BO

  Scenario: I add a product with basic information
    When I add product "product1" with following information:
      | name       | en-US:bottle of beer |
      | is_virtual | false                |
    And product "product1" type should be standard
    When I add following customization field to product product1:
      | type               | text                                |
      | name               | en-US: your custom text can be here |
      | is required        | true                                |
#@todo: test UpdateCustomizationFieldsHandler instead?
