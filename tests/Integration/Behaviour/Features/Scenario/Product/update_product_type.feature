# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-product-type
@reset-database-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@update-product-type
Feature: Add basic product from Back Office (BO)
  As a BO user
  I need to be able to add new product with basic information from the BO

  Background:
    Given language with iso code "en" is the default one
    And category "home" in default language named "Home" exists
    And category "home" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists

  Scenario: I update product type to combinations
    When I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    Then product "product1" should be disabled
    And product "product1" type should be standard
    And product "product1" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "product1" should be assigned to following categories:
      | id reference | name[en-US] | is default |
      | home         | Home        | true       |
    When I update product "product1" type to combinations
    Then product "product1" type should be combinations

  Scenario: I update product type to virtual
    When I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    Then product "product1" should be disabled
    And product "product1" type should be standard
    And product "product1" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "product1" should be assigned to following categories:
      | id reference | name[en-US] | is default |
      | home         | Home        | true       |
    When I update product "product1" type to virtual
    Then product "product1" type should be virtual

  Scenario: I update product type to pack
    When I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    Then product "product1" should be disabled
    And product "product1" type should be standard
    And product "product1" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "product1" should be assigned to following categories:
      | id reference | name[en-US] | is default |
      | home         | Home        | true       |
    When I update product "product1" type to pack
    Then product "product1" type should be pack

  Scenario: I update product type to standard
    When I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | virtual        |
    Then product "product1" should be disabled
    And product "product1" type should be virtual
    And product "product1" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "product1" should be assigned to following categories:
      | id reference | name[en-US] | is default |
      | home         | Home        | true       |
    When I update product "product1" type to standard
    Then product "product1" type should be standard

  Scenario: Changing pack type should remove all pack associations
    Given I add product "productPack1" with following information:
      | name[en-US] | weird sunglasses box |
      | type        | pack                 |
    And product "productPack1" type should be pack
    And I add product "product2" with following information:
      | name[en-US] | shady sunglasses |
      | type        | standard         |
    And product "product2" type should be standard
    When I update pack "productPack1" with following product quantities:
      | product  | quantity |
      | product2 | 5        |
    Then product "productPack1" type should be pack
    And pack "productPack1" should contain products with following quantities:
      | product  | quantity |
      | product2 | 5        |
    When I update product "productPack1" type to standard
    Then product "productPack1" type should be standard
    And pack "productPack1" should be empty

  Scenario: Changing combinations type should remove all combinations
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default | combination reference |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |                       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |                       |
      | product1Blue   | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |                       |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |                       |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |                       |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |                       |
    When I update product "product1" type to standard
    Then product "product1" type should be standard
    And product "product1" should have no combinations

  Scenario: Changing virtual type should remove virtual file
    Given I add product "product1" with following information:
      | name[en-US] | puffin icon |
      | type        | virtual     |
    And product "product1" should not have a file
    And product product1 type should be virtual
    And I add virtual product file "file1" to product "product1" with following details:
      | filename reference | filename_file1  |
      | display name       | puffin-logo.png |
      | file name          | app_icon.png    |
    And product "product1" should have a virtual product file "file1" with following details:
      | display name         | puffin-logo.png |
      | access days          | 0               |
      | download times limit | 0               |
      | expiration date      |                 |
    And file "file1" for product "product1" exists in system
    When I update product "product1" type to standard
    And product "product1" should not have a file
    And file "file1" for product "product1" should not exist in system
