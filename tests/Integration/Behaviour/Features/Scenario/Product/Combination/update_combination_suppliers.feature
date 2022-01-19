# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-suppliers
@restore-products-before-feature
@restore-currencies-after-feature
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
      | Size  | [S,M]         |
      | Color | [White,Black] |
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And combination "product1SWhite" should not have any suppliers assigned
    And combination "product1SBlack" should not have any suppliers assigned
    And combination "product1MWhite" should not have any suppliers assigned
    And combination "product1MBlack" should not have any suppliers assigned
    # Association and update are performed by two distinct commands, all combinations are associated
    # This the moment to define the references for product_supplier to use them later, after it's too late
    # You can define references for any combination/supplier association, you are not obliged to reference them all
    When I associate suppliers to product "product1"
      | supplier  | combination_suppliers                                                         |
      | supplier2 | product1SWhite:product1SWhiteSupplier2;product1SBlack:product1SBlackSupplier2 |
      | supplier1 | product1SWhite:product1SWhiteSupplier1;product1SBlack:product1SBlackSupplier1 |
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    # Default supplier is the first one associated
    And product product1 should have following supplier values:
      | default supplier           | supplier2 |
      | default supplier reference |           |
    # Every combinations are associated to the supplier
    And combination "product1SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product1SWhiteSupplier1 | supplier1 |           | USD      | 0                  |
      | product1SWhiteSupplier2 | supplier2 |           | USD      | 0                  |
    And combination "product1SBlack" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product1SBlackSupplier1 | supplier1 |           | USD      | 0                  |
      | product1SBlackSupplier2 | supplier2 |           | USD      | 0                  |
    # We didn't define references for product suppliers of these combinations so we don't check them
    And combination "product1MWhite" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product1MBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    # I can update combination content independently (and partially) it does not remove other associations
    When I update following suppliers for combination "product1SWhite":
      | product_supplier        | supplier  | reference           | currency | price_tax_excluded |
      | product1SWhiteSupplier1 | supplier1 | sup white shirt S 1 | USD      | 10                 |
    And combination "product1SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference           | currency | price_tax_excluded |
      | product1SWhiteSupplier1 | supplier1 | sup white shirt S 1 | USD      | 10                 |
      | product1SWhiteSupplier2 | supplier2 |                     | USD      | 0                  |
    And combination "product1SBlack" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product1SBlackSupplier1 | supplier1 |           | USD      | 0                  |
      | product1SBlackSupplier2 | supplier2 |           | USD      | 0                  |
    And combination "product1MWhite" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product1MBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And product product1 should have following supplier values:
      | default supplier           | supplier2           |
      | default supplier reference |                     |
    # Infos are for product form, they should remain empty for combination products
    But product product1 should not have suppliers infos
    When I update following suppliers for combination "product1SWhite":
      | product_supplier        | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1 | supplier1 | new sup white shirt S 1 | USD      | 10                 |
      | product1SWhiteSupplier2 | supplier2 | sup S2                  | EUR      | 20                 |
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And combination "product1SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1 | supplier1 | new sup white shirt S 1 | USD      | 10                 |
      | product1SWhiteSupplier2 | supplier2 | sup S2                  | EUR      | 20                 |
    But product product1 should not have suppliers infos
    # Default supplier was already set it should be the same but reference is updated
    And product product1 should have following supplier values:
      | default supplier           | supplier2 |
      | default supplier reference | sup S2    |
    # Explicitly set default supplier for combination
    When I set combination "product1SWhite" default supplier to supplier1
    And product product1 should have following supplier values:
      | default supplier           | supplier1               |
      | default supplier reference | new sup white shirt S 1 |

  Scenario: Remove one of combination suppliers
    Given combination "product1SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1 | supplier1 | new sup white shirt S 1 | USD      | 10                 |
      | product1SWhiteSupplier2 | supplier2 | sup S2                  | EUR      | 20                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier1               |
      | default supplier reference | new sup white shirt S 1 |
    When I associate suppliers to product "product1"
      | supplier  | combination_suppliers                                                         |
      | supplier1 | product1SWhite:product1SWhiteSupplier1;product1SBlack:product1SBlackSupplier1 |
    Then combination "product1SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1 | supplier1 | new sup white shirt S 1 | USD      | 10                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier1               |
      | default supplier reference | new sup white shirt S 1 |
    # If default supplier is removed, the first one is automatically associated
    When I associate suppliers to product "product1"
      | supplier  | combination_suppliers                     |
      | supplier3 | product1SWhite:product1SWhiteSupplier3    |
      | supplier2 | product1SWhite:product1SWhiteSupplier2bis |
    And I update following suppliers for combination "product1SWhite":
      | product_supplier           | supplier  | reference        | currency | price_tax_excluded |
      | product1SWhiteSupplier2bis | supplier2 | second supplier2 | USD      | 20                 |
      | product1SWhiteSupplier3    | supplier3 | sup S3           | USD      | 5.5                |
    Then product product1 should have the following suppliers assigned:
      | supplier2 |
      | supplier3 |
    And combination "product1SWhite" should have following suppliers:
      | product_supplier           | supplier  | reference        | currency | price_tax_excluded |
      | product1SWhiteSupplier2bis | supplier2 | second supplier2 | USD      | 20                 |
      | product1SWhiteSupplier3    | supplier3 | sup S3           | USD      | 5.5                |
    And product product1 should have following supplier values:
      | default supplier           | supplier3 |
      | default supplier reference | sup S3    |

  Scenario: Updating a combination product with invalid references is impossible
    # Assert initial combination product state
    Given product product1 type should be combinations
    And product product1 should have the following suppliers assigned:
      | supplier2 |
      | supplier3 |
    And combination "product1SWhite" should have following suppliers:
      | product_supplier           | supplier  | reference        | currency | price_tax_excluded |
      | product1SWhiteSupplier2bis | supplier2 | second supplier2 | USD      | 20                 |
      | product1SWhiteSupplier3    | supplier3 | sup S3           | USD      | 5.5                |
    But product product1 should not have suppliers infos
    # Remove association on a supplier, the product supplier will be removed from DB
    When I associate suppliers to product "product1"
      | supplier  | combination_suppliers                     |
      | supplier1 | product1SWhite:product1SWhiteSupplier1Bis |
      | supplier2 | product1SWhite:product1SWhiteSupplier2bis |
    And I update following suppliers for combination "product1SWhite":
      | product_supplier           | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1Bis | supplier1 | new sup white shirt S 1 | USD      | 42                 |
      | product1SWhiteSupplier2bis | supplier2 | second supplier2        | USD      | 10                 |
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And combination "product1SWhite" should have following suppliers:
      | product_supplier           | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1Bis | supplier1 | new sup white shirt S 1 | USD      | 42                 |
      | product1SWhiteSupplier2bis | supplier2 | second supplier2        | USD      | 10                 |
    # Now we try updating a removed product supplier
    When I update following suppliers for combination "product1SWhite":
      | product_supplier           | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1Bis | supplier1 | new sup white shirt S 1 | USD      | 42                 |
      | product1SWhiteSupplier2bis | supplier2 | second supplier2        | USD      | 10                 |
      | product1SWhiteSupplier3    | supplier3 | sup S3                  | USD      | 5.5                |
    Then I should get error that an invalid association has been used
    # And nothing changed
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And combination "product1SWhite" should have following suppliers:
      | product_supplier           | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1Bis | supplier1 | new sup white shirt S 1 | USD      | 42                 |
      | product1SWhiteSupplier2bis | supplier2 | second supplier2        | USD      | 10                 |
    But product product1 should not have suppliers infos

  Scenario: Remove all associated combination suppliers
    Given product product1 type should be combinations
    And combination "product1SWhite" should have following suppliers:
      | product_supplier           | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1Bis | supplier1 | new sup white shirt S 1 | USD      | 42                 |
      | product1SWhiteSupplier2bis | supplier2 | second supplier2        | USD      | 10                 |
    When I remove all associated combination "product1SWhite" suppliers
    And combination "product1SWhite" should not have any suppliers assigned
    And product product1 should not have a default supplier
    And product product1 default supplier reference should be empty
