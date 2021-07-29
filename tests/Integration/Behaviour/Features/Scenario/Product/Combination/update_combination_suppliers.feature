# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-suppliers
@reset-database-before-feature
@clear-cache-before-feature
@product-combination
@update-combination-suppliers
Feature: Update product combination suppliers in Back Office (BO)
  As an employee
  I need to be able to update product combination suppliers from BO

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And there is a currency named "currency1" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "currency3" with iso code "EUR" and exchange rate of 0.63
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists

  Scenario: I update combination suppliers:
    Given I add new supplier supplier1 with following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | meta keywords[en-US]    | sup,1              |
      | shops                   | [shop1]            |
    And I add new supplier supplier2 with following properties:
      | name                    | my supplier 2      |
      | address                 | Donelaicio st. 2   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr two |
      | meta description[en-US] |                    |
      | meta keywords[en-US]    | sup,2              |
      | shops                   | [shop1]            |
    And I add new supplier supplier3 with following properties:
      | name                    | my supplier 3    |
      | address                 | Donelaicio st. 3 |
      | city                    | Kaunas           |
      | country                 | Lithuania        |
      | enabled                 | true             |
      | description[en-US]      | just a 3         |
      | meta title[en-US]       | my third supp    |
      | meta description[en-US] |                  |
      | meta keywords[en-US]    | sup,3            |
      | shops                   | [shop1]          |
    And I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1Blue   | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And combination "product1SWhite" should not have any suppliers assigned
    And combination "product1SBlack" should not have any suppliers assigned
    And combination "product1Blue" should not have any suppliers assigned
    And combination "product1MWhite" should not have any suppliers assigned
    And combination "product1MBlack" should not have any suppliers assigned
    And combination "product1MBlue" should not have any suppliers assigned
    When I set following suppliers for combination "product1SWhite":
      | reference               | supplier reference | combination supplier reference | currency | price tax excluded |
      | product1SWhiteSupplier1 | supplier1          | sup white shirt S 1            | USD      | 10                 |
    Then combination "product1SWhite" should have following suppliers:
      | combination supplier reference | currency | price tax excluded |
      | sup white shirt S 1            | USD      | 10                 |
    And combination "product1SBlack" should not have any suppliers assigned
    And combination "product1Blue" should not have any suppliers assigned
    And combination "product1MWhite" should not have any suppliers assigned
    And combination "product1MBlack" should not have any suppliers assigned
    And combination "product1MBlue" should not have any suppliers assigned
    # Default supplier is the first one
    And product product1 should have following supplier values:
      | default supplier           | supplier1           |
      | default supplier reference | sup white shirt S 1 |
    When I set following suppliers for combination "product1SWhite":
      | reference               | supplier reference | combination supplier reference | currency | price tax excluded |
      | product1SWhiteSupplier1 | supplier1          | new sup white shirt S 1        | USD      | 10                 |
      | product1SWhiteSupplier2 | supplier2          | sup S2                         | USD      | 0                  |
      | product1SWhiteSupplier3 | supplier3          | sup S3                         | USD      | 5.5                |
    Then combination "product1SWhite" should have following suppliers:
      | combination supplier reference | currency | price tax excluded |
      | new sup white shirt S 1        | USD      | 10                 |
      | sup S2                         | USD      | 0                  |
      | sup S3                         | USD      | 5.5                |
    # Default supplier was already set it should be the same but reference is updated
    And product product1 should have following supplier values:
      | default supplier           | supplier1               |
      | default supplier reference | new sup white shirt S 1 |
    # Explicitly set default supplier for product
    When I set product product1 default supplier to supplier2
    When I set combination "product1SWhite" default supplier to supplier2
    And product product1 should have following supplier values:
      | default supplier           | supplier2 |
      | default supplier reference | sup S2    |

  Scenario: Set suppliers for standard product while it has combinations
    Given product product1 type should be combinations
    And product product1 should not have any suppliers assigned
    And combination "product1SWhite" should have following suppliers:
      | combination supplier reference | currency | price tax excluded |
      | new sup white shirt S 1        | USD      | 10                 |
      | sup S2                         | USD      | 0                  |
      | sup S3                         | USD      | 5.5                |
    When I set product product1 suppliers:
      | reference         | supplier reference | product supplier reference      | currency | price tax excluded |
      | product1supplier1 | supplier1          | my first supplier for product1  | USD      | 10                 |
      | product1supplier2 | supplier2          | my second supplier for product1 | EUR      | 11                 |
    Then I should get error that this action is allowed for single product only
    And product product1 should not have any suppliers assigned
    And combination "product1SWhite" should have following suppliers:
      | combination supplier reference | currency | price tax excluded |
      | new sup white shirt S 1        | USD      | 10                 |
      | sup S2                         | USD      | 0                  |
      | sup S3                         | USD      | 5.5                |
    When I set product product1 default supplier to supplier2
    Then I should get error that this action is allowed for single product only
    And product product1 should have following supplier values:
      | default supplier           | supplier2 |
      | default supplier reference | sup S2    |

  Scenario: Remove one of combination suppliers
    Given combination "product1SWhite" should have following suppliers:
      | combination supplier reference | currency | price tax excluded |
      | new sup white shirt S 1        | USD      | 10                 |
      | sup S2                         | USD      | 0                  |
      | sup S3                         | USD      | 5.5                |
    And product product1 should have following supplier values:
      | default supplier           | supplier2 |
      | default supplier reference | sup S2    |
    When I set following suppliers for combination "product1SWhite":
      | reference               | supplier reference | combination supplier reference | currency | price tax excluded |
      | product1SWhiteSupplier1 | supplier1          | sup white shirt S 1            | USD      | 10                 |
      | product1SWhiteSupplier2 | supplier2          | sup S2                         | USD      | 0                  |
    Then combination "product1SWhite" should have following suppliers:
      | combination supplier reference | currency | price tax excluded |
      | sup white shirt S 1            | USD      | 10                 |
      | sup S2                         | USD      | 0                  |
    And product product1 should have following supplier values:
      | default supplier           | supplier2 |
      | default supplier reference | sup S2    |
    # If default supplier is removed another one is automatically associated
    When I set following suppliers for combination "product1SWhite":
      | reference                  | supplier reference | combination supplier reference | currency | price tax excluded |
      | product1SWhiteSupplier3bis | supplier3          | sup S3                         | USD      | 5.5                |
      | product1SWhiteSupplier1    | supplier1          | sup white shirt S 1            | USD      | 10                 |
    Given combination "product1SWhite" should have following suppliers:
      | combination supplier reference | currency | price tax excluded |
      | sup white shirt S 1            | USD      | 10                 |
      | sup S3                         | USD      | 5.5                |
    And product product1 should have following supplier values:
      | default supplier           | supplier3 |
      | default supplier reference | sup S3    |

  Scenario: Remove all associated combination suppliers
    Given product product1 type should be combinations
    Given combination "product1SWhite" should have following suppliers:
      | combination supplier reference | currency | price tax excluded |
      | sup white shirt S 1            | USD      | 10                 |
      | sup S3                         | USD      | 5.5                |
    When I remove all associated combination "product1SWhite" suppliers
    And combination "product1SWhite" should not have any suppliers assigned
    And product product1 should not have a default supplier
    And product product1 default supplier reference should be empty
