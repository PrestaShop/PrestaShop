# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add-to-pack
@reset-database-before-feature
@clear-cache-after-feature
@add-to-pack
Feature: Add product to pack from Back Office (BO)
  As a BO user
  I need to be able to add product to pack from BO

  Scenario: I add standard product to a pack
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

  Scenario: I add virtual product to a pack
    Given I add product "productPack2" with following information:
      | name       | en-US: street photos        |
      | is_virtual | false                       |
    And product "productPack2" type should be standard
    And I add product "product3" with following information:
      | name       | en-US: summerstreet         |
      | is_virtual | true                        |
    And product "product3" type should be virtual
    When I pack 3 standard product "product3" to a pack of "productPack2"
    Then product "productPack2" type should be pack

  Scenario: I add pack product to a pack
    Given product "productPack1" type should be pack
    And product "productPack2" type should be pack
    When I pack 1 pack product "productPack1" to a pack of "productPack2"
    Then I should get error that I cannot add pack into a pack

#@todo: add combination product to a pack
