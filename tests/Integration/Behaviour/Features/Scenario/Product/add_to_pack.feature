# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add-to-pack
@reset-database-before-feature
@add-to-pack
Feature: Add product to pack from Back Office (BO)
  As a BO user
  I need to be able to add product to pack from BO

  Scenario: I add product to pack
    Given I add product "product1" with following information:
      | name       | en-US: weird sunglasses box |
      | is_virtual | false                       |
    And product "product1" should have following values:
      | name       | en-US: weird sunglasses box |
    And product "product1" type should be standard
    And I add product "product2" with following information:
      | name       | en-US: shady sunglasses     |
      | is_virtual | false                       |
    And product "product2" type should be standard
#@todo: finish up
