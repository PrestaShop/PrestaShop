# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add-to-pack
@reset-database-before-feature
@add-to-pack
Feature: Add product to pack from Back Office (BO)
  As a BO user
  I need to be able to add product to pack from BO

  Scenario: I add product to pack
    Given I add product "productPack1" with following information:
      | name       | en-US: weird sunglasses box |
      | is_virtual | false                       |
    And product "productPack1" type should be standard
    And I add product "product2" with following information:
      | name       | en-US: shady sunglasses     |
      | is_virtual | false                       |
    And product "product2" type should be standard
    When I pack 5 standard product "product2" to a pack of "productPack1"
    Then product "productPack1" type should be pack
