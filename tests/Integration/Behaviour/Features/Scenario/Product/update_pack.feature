# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-pack
@reset-database-before-feature
@clear-cache-after-feature
@update-pack
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
    When I update pack "productPack1" with following product quantities:
      | product2        | 5                      |
    Then product "productPack1" type should be pack
    And pack "productPack1" should contain following product quantities:
      | product2        | 5                      |

  Scenario: I add virtual products to a pack
    Given I add product "productPack2" with following information:
      | name       | en-US: street photos        |
      | is_virtual | false                       |
    And product "productPack2" type should be standard
    And I add product "product3" with following information:
      | name       | en-US: summerstreet         |
      | is_virtual | true                        |
    And I add product "product4" with following information:
      | name       | en-US: winterstreet         |
      | is_virtual | true                        |
    And product "product3" type should be virtual
    When I update pack "productPack2" with following product quantities:
      | product3   | 3                           |
      | product4   | 20                          |
    Then product "productPack2" type should be pack
    And pack productPack2 should contain following product quantities:
      | product3   | 3                           |
      | product4   | 20                          |

  Scenario: I add pack product to a pack
    Given product "productPack1" type should be pack
    And product "productPack2" type should be pack
    When I update pack productPack2 with following product quantities:
      | productPack1   | 1               |
    Then I should get error that I cannot add pack into a pack

#@todo: add combination product to a pack
