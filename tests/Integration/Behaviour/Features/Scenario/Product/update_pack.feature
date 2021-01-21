# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-pack
@reset-database-before-feature
@clear-cache-after-feature
@update-pack
Feature: Add product to pack from Back Office (BO)
  As a BO user
  I need to be able to add product to pack from BO

  Scenario: I add standard product to a pack
    Given I add product "productPack1" with following information:
      | name[en-US] | weird sunglasses box |
      | is_virtual  | false                |
    And product "productPack1" type should be standard
    And I add product "product2" with following information:
      | name[en-US] | shady sunglasses |
      | is_virtual  | false            |
    And product "product2" type should be standard
    When I update pack "productPack1" with following product quantities:
      | product  | quantity |
      | product2 | 5        |
    Then product "productPack1" type should be pack
    And pack "productPack1" should contain products with following quantities:
      | product  | quantity |
      | product2 | 5        |

  Scenario: I add virtual products to a pack
    Given I add product "productPack2" with following information:
      | name[en-US] | street photos |
      | is_virtual  | false         |
    And product "productPack2" type should be standard
    And I add product "product3" with following information:
      | name[en-US] | summerstreet |
      | is_virtual  | true         |
    And I add product "product4" with following information:
      | name[en-US] | winterstreet |
      | is_virtual  | true         |
    And product "product3" type should be virtual
    When I update pack "productPack2" with following product quantities:
      | product  | quantity |
      | product3 | 3        |
      | product4 | 20       |
    Then product "productPack2" type should be pack
    And pack productPack2 should contain products with following quantities:
      | product  | quantity |
      | product3 | 3        |
      | product4 | 20       |

  Scenario: I update pack by removing one of the products
    Given pack productPack2 should contain products with following quantities:
      | product  | quantity |
      | product3 | 3        |
      | product4 | 20       |
    When I update pack "productPack2" with following product quantities:
      | product  | quantity |
      | product3 | 3        |
    And pack productPack2 should contain products with following quantities:
      | product  | quantity |
      | product3 | 3        |

  Scenario: I add pack product to a pack
    Given product "productPack1" type should be pack
    And product "productPack2" type should be pack
    When I update pack productPack2 with following product quantities:
      | product      | quantity |
      | productPack1 | 1        |
    Then I should get error that I cannot add pack into a pack

  Scenario: I add virtual and standard product to the same pack
    Given I add product productPack4 with following information:
      | name[en-US] | mixed pack |
      | is_virtual  | false      |
    Given product "product2" type should be standard
    And product "product3" type should be virtual
    When I update pack productPack4 with following product quantities:
      | product  | quantity |
      | product2 | 2        |
      | product3 | 3        |
    Then product "productPack4" type should be pack
    And pack productPack4 should contain products with following quantities:
      | product  | quantity |
      | product2 | 2        |
      | product3 | 3        |

  Scenario: I add product with negative quantity to a pack
    Given product "product2" type should be standard
    Then product "productPack4" type should be pack
    When I update pack productPack4 with following product quantities:
      | product  | quantity |
      | product2 | -10      |
      | product3 | 3        |
    Then I should get error that product for packing quantity is invalid

  Scenario: I remove all products from existing pack
    Given product "productPack4" type should be pack
    And pack productPack4 should contain products with following quantities:
      | product  | quantity |
      | product2 | 2        |
      | product3 | 3        |
    When I remove all products from pack productPack4
    Then product "productPack4" type should be standard

  Scenario: Add combination product to a pack
    Given I add product "productSkirt1" with following information:
      | name[en-US] | regular skirt |
      | is_virtual  | false         |
    And product "productSkirt1" has following combinations:
      | reference | quantity | attributes         |
      | whiteS    | 15       | Size:S;Color:White |
      | whiteM    | 15       | Size:M;Color:White |
      | blackM    | 13       | Size:M;Color:Black |
    And product productSkirt1 type should be combination
    And product "productPack4" type should be standard
    When I update pack productPack4 with following product quantities:
      | product       | combination | quantity |
      | productSkirt1 | whiteS      | 10       |
      | productSkirt1 | whiteM      | 11       |
      | productSkirt1 | blackM      | 12       |
    Then product "productPack4" type should be pack
    And pack productPack4 should contain products with following quantities:
      | product       | combination | quantity |
      | productSkirt1 | whiteS      | 10       |
      | productSkirt1 | whiteM      | 11       |
      | productSkirt1 | blackM      | 12       |

  Scenario: Add combination & standard product to a pack
    Given product "product2" type should be standard
    And product productSkirt1 type should be combination
    And product "productSkirt1" has following combinations:
      | reference | quantity | attributes         |
      | whiteS    | 15       | Size:S;Color:White |
      | whiteM    | 15       | Size:M;Color:White |
      | blackM    | 13       | Size:M;Color:Black |
    When I update pack productPack4 with following product quantities:
      | product       | combination | quantity |
      | productSkirt1 | whiteS      | 10       |
      | productSkirt1 | whiteM      | 11       |
      | productSkirt1 | blackM      | 12       |
      | product2      |             | 2        |
    Then product "productPack4" type should be pack
    And pack productPack4 should contain products with following quantities:
      | product       | combination | quantity |
      | productSkirt1 | whiteS      | 10       |
      | productSkirt1 | whiteM      | 11       |
      | productSkirt1 | blackM      | 12       |
      | product2      |             | 2        |

  Scenario: I remove one combination of same product from existing pack and change another combination quantity
    Given product "productPack4" type should be pack
    And pack productPack4 should contain products with following quantities:
      | product       | combination | quantity |
      | productSkirt1 | whiteS      | 10       |
      | productSkirt1 | whiteM      | 11       |
      | productSkirt1 | blackM      | 12       |
      | product2      |             | 2        |
    When I update pack productPack4 with following product quantities:
      | product       | combination | quantity |
      | productSkirt1 | whiteS      | 10       |
      | productSkirt1 | blackM      | 9        |
      | product2      |             | 2        |
    Then pack productPack4 should contain products with following quantities:
      | product       | combination | quantity |
      | productSkirt1 | whiteS      | 10       |
      | productSkirt1 | blackM      | 9        |
      | product2      |             | 2        |
    Then product "productPack4" type should be pack

  Scenario: I remove all products from existing pack when it contains combination and standard products
    Given product "productPack4" type should be pack
    And pack productPack4 should contain products with following quantities:
      | product       | combination | quantity |
      | productSkirt1 | whiteS      | 10       |
      | productSkirt1 | blackM      | 9        |
      | product2      |             | 2        |
    When I remove all products from pack productPack4
    Then product "productPack4" type should be standard
