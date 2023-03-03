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
    Given I add new supplier supplier1 with the following properties:
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
    And I add new supplier supplier2 with the following properties:
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
    And I add new supplier supplier3 with the following properties:
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
      | default supplier           | supplier2 |
      | default supplier reference |           |
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
    When I set product product1 default supplier to supplier1
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

  Scenario: Remove all associated combination suppliers
    Given product product1 type should be combinations
    And product product1 should have the following suppliers assigned:
      | supplier2 |
      | supplier3 |
    And combination "product1SWhite" should have following suppliers:
      | product_supplier           | supplier  | reference        | currency | price_tax_excluded |
      | product1SWhiteSupplier2bis | supplier2 | second supplier2 | USD      | 20                 |
      | product1SWhiteSupplier3    | supplier3 | sup S3           | USD      | 5.5                |
    And product product1 should have following supplier values:
      | default supplier           | supplier3 |
      | default supplier reference | sup S3    |
    When I remove all associated product product1 suppliers
    And combination "product1SWhite" should not have any suppliers assigned
    And combination "product1SBlack" should not have any suppliers assigned
    And combination "product1MWhite" should not have any suppliers assigned
    And combination "product1MBlack" should not have any suppliers assigned
    And product product1 should not have a default supplier
    And product product1 default supplier reference should be empty

  Scenario: Update product suppliers without specifying the productSupplierId should also work
    When I associate suppliers to product "product1"
      | supplier  | combination_suppliers                     |
      | supplier2 | product1SWhite:product1SWhiteSupplier2Ter |
      | supplier1 | product1SWhite:product1SWhiteSupplier1Ter |
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    # Default supplier is the first one associated
    And product product1 should have following supplier values:
      | default supplier           | supplier2 |
      | default supplier reference |           |
    # Suppliers are associated to all combinations but only product1SWhite has references for product supplier
    And combination "product1SWhite" should have following suppliers:
      | product_supplier           | supplier  | reference | currency | price_tax_excluded |
      | product1SWhiteSupplier1Ter | supplier1 |           | USD      | 0                  |
      | product1SWhiteSupplier2Ter | supplier2 |           | USD      | 0                  |
    And combination "product1SBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product1MWhite" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product1MBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    # We can update combination suppliers even without specifying the productSupplierId, the matching works based on the
    # productId,combinationId,supplierId triplet
    When I update following suppliers for combination "product1SWhite":
      | supplier  | reference               | currency | price_tax_excluded |
      | supplier1 | new sup white shirt S 1 | USD      | 51                 |
      | supplier2 | second supplier2        | USD      | 69                 |
    And combination "product1SWhite" should have following suppliers:
      | product_supplier           | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1Ter | supplier1 | new sup white shirt S 1 | USD      | 51                 |
      | product1SWhiteSupplier2Ter | supplier2 | second supplier2        | USD      | 69                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier2        |
      | default supplier reference | second supplier2 |

  Scenario: Updating a supplier not associated is forbidden
    # Assert initial combination product state
    Given product product1 type should be combinations
    And product product1 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And combination "product1SWhite" should have following suppliers:
      | product_supplier           | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1Ter | supplier1 | new sup white shirt S 1 | USD      | 51                 |
      | product1SWhiteSupplier2Ter | supplier2 | second supplier2        | USD      | 69                 |
    But product product1 should not have suppliers infos
    # Now we try updating a supplier not associated
    When I update following suppliers for combination "product1SWhite":
      | supplier  | reference               | currency | price_tax_excluded |
      | supplier1 | new sup white shirt S 1 | USD      | 51                 |
      | supplier2 | second supplier2        | USD      | 69                 |
      | supplier3 | sup S3                  | USD      | 5.5                |
    Then I should get error that supplier is not associated with product
    # And nothing changed
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And combination "product1SWhite" should have following suppliers:
      | product_supplier           | supplier  | reference               | currency | price_tax_excluded |
      | product1SWhiteSupplier1Ter | supplier1 | new sup white shirt S 1 | USD      | 51                 |
      | product1SWhiteSupplier2Ter | supplier2 | second supplier2        | USD      | 69                 |
    And product product1 should have following supplier values:
      | default supplier           | supplier2        |
      | default supplier reference | second supplier2 |
    But product product1 should not have suppliers infos

  Scenario: When new combinations are generated the suppliers must be associated to them
    Given I add product "product4" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product4 type should be combinations
    And I generate combinations for product product4 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    Then product "product4" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product4SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product4SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product4MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product4MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    When I associate suppliers to product "product4"
      | supplier  | combination_suppliers                  |
      | supplier2 | product4SWhite:product4SWhiteSupplier2 |
      | supplier1 | product4SWhite:product4SWhiteSupplier1 |
    And I update following suppliers for combination "product4SWhite":
      | product_supplier        | supplier  | reference          | currency | price_tax_excluded |
      | product4SWhiteSupplier1 | supplier1 | white S supplier 1 | USD      | 51                 |
      | product4SWhiteSupplier2 | supplier2 | white S supplier 2 | EUR      | 69                 |
    Then product product4 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And product product4 should have following supplier values:
      | default supplier | supplier2 |
    And combination "product4SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference          | currency | price_tax_excluded |
      | product4SWhiteSupplier1 | supplier1 | white S supplier 1 | USD      | 51                 |
      | product4SWhiteSupplier2 | supplier2 | white S supplier 2 | EUR      | 69                 |
    And combination "product4SBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product4MWhite" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product4MBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    # Now I generate new combinations
    When I generate combinations for product product4 using following attributes:
      | Size  | [S,M]  |
      | Color | [Blue] |
    Then product "product4" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product4SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product4SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product4MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product4MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product4SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product4MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And combination "product4SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference          | currency | price_tax_excluded |
      | product4SWhiteSupplier1 | supplier1 | white S supplier 1 | USD      | 51                 |
      | product4SWhiteSupplier2 | supplier2 | white S supplier 2 | EUR      | 69                 |
    And combination "product4SBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product4MWhite" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product4MBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product4SBlue" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product4MBlue" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |

  Scenario: I should be able to associate suppliers (and default supplier) even when no combinations has been created
    # We create new empty suppliers which have no other products
    Given I add new supplier supplier4 with the following properties:
      | name                    | my supplier 4       |
      | address                 | Donelaicio st. 4    |
      | city                    | Kaunas              |
      | country                 | Lithuania           |
      | enabled                 | true                |
      | description[en-US]      | just a supplier     |
      | meta title[en-US]       | my supplier nr four |
      | meta description[en-US] |                     |
      | meta keywords[en-US]    | sup,4               |
      | shops                   | [shop1]             |
    And I add new supplier supplier5 with the following properties:
      | name                    | my supplier 5       |
      | address                 | Donelaicio st. 5    |
      | city                    | Kaunas              |
      | country                 | Lithuania           |
      | enabled                 | true                |
      | description[en-US]      | just a supplier     |
      | meta title[en-US]       | my supplier nr five |
      | meta description[en-US] |                     |
      | meta keywords[en-US]    | sup,5               |
      | shops                   | [shop1]             |
    And I add product "product5" with following information:
      | name[en-US] | really unique T-shirt |
      | type        | combinations          |
    And product product5 type should be combinations
    But product product5 should have no combinations
    When I associate suppliers to product "product5"
      | supplier  | product_supplier  |
      | supplier5 | product5supplier5 |
      | supplier4 | product5supplier4 |
    And I set product product5 default supplier to supplier4
    Then product product5 should have the following suppliers assigned:
      | supplier4 |
      | supplier5 |
    And product product5 should have following supplier values:
      | default supplier | supplier4 |
    And supplier "supplier4" should have 1 products associated
    And supplier "supplier5" should have 1 products associated
    # No combinations but suppliers display the product regardless
    And supplier "supplier4" should have following details for product "really unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product5          |       |     | 0        |
    And supplier "supplier5" should have following details for product "really unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product5          |       |     | 0        |
    # Event if association is present no details are provided in product form
    But product product5 should not have suppliers infos
    # Now I generate combinations, since the supplier's associations are existent the combination will also be associated
    When I generate combinations for product product5 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    Then product "product5" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product5SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product5SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product5MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product5MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And combination "product5SWhite" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier4 |           | USD      | 0                  |
      | supplier5 |           | USD      | 0                  |
    And combination "product5SBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier4 |           | USD      | 0                  |
      | supplier5 |           | USD      | 0                  |
    And combination "product5MWhite" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier4 |           | USD      | 0                  |
      | supplier5 |           | USD      | 0                  |
    And combination "product5MBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier4 |           | USD      | 0                  |
      | supplier5 |           | USD      | 0                  |
    And supplier "supplier4" should have 1 products associated
    And supplier "supplier5" should have 1 products associated
    # Now that combinations are present only them are displayed
    And supplier "supplier4" should have following details for product "really unique T-shirt":
      | attribute name          | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      | Size - S, Color - White |                    | $0.00           | product5          |       |     | 0        |
      | Size - S, Color - Black |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - White |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - Black |                    | $0.00           | product5          |       |     | 0        |
    And supplier "supplier5" should have following details for product "really unique T-shirt":
      | attribute name          | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      | Size - S, Color - White |                    | $0.00           | product5          |       |     | 0        |
      | Size - S, Color - Black |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - White |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - Black |                    | $0.00           | product5          |       |     | 0        |
    # Now I delete a combination its association should disappear
    When I delete combination product5SWhite
    Then product "product5" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product5SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | true       |
      | product5MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product5MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And combination "product5SBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier4 |           | USD      | 0                  |
      | supplier5 |           | USD      | 0                  |
    And combination "product5MWhite" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier4 |           | USD      | 0                  |
      | supplier5 |           | USD      | 0                  |
    And combination "product5MBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier4 |           | USD      | 0                  |
      | supplier5 |           | USD      | 0                  |
    And supplier "supplier4" should have 1 products associated
    And supplier "supplier5" should have 1 products associated
    # Now that combinations are present only them are displayed
    And supplier "supplier4" should have following details for product "really unique T-shirt":
      | attribute name          | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      | Size - S, Color - Black |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - White |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - Black |                    | $0.00           | product5          |       |     | 0        |
    And supplier "supplier5" should have following details for product "really unique T-shirt":
      | attribute name          | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      | Size - S, Color - Black |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - White |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - Black |                    | $0.00           | product5          |       |     | 0        |
    When I delete following combinations of product product5:
      | id reference   |
      | product5SBlack |
      | product5MWhite |
      | product5MBlack |
    Then product product5 should have no combinations
    # Suppliers association are still present
    But product product5 should have the following suppliers assigned:
      | supplier4 |
      | supplier5 |
    And product product5 should have following supplier values:
      | default supplier | supplier4 |
    And supplier "supplier4" should have 1 products associated
    And supplier "supplier5" should have 1 products associated
    # No combinations but suppliers display the product regardless
    And supplier "supplier4" should have following details for product "really unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product5          |       |     | 0        |
    And supplier "supplier5" should have following details for product "really unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product5          |       |     | 0        |
    When I delete product product5
    Then product product5 should not exist anymore
    And supplier "supplier4" should have 0 products associated
    And supplier "supplier5" should have 0 products associated

  Scenario: Supplier associations should still be present if I change the product type
    When I add product "product6" with following information:
      | name[en-US] | even more unique T-shirt |
      | type        | combinations             |
    And product product6 type should be combinations
    But product product6 should have no combinations
    When I associate suppliers to product "product6"
      | supplier  | product_supplier  |
      | supplier5 | product6supplier5 |
      | supplier4 | product6supplier4 |
    Then product product6 should have the following suppliers assigned:
      | supplier4 |
      | supplier5 |
    And product product6 should have following supplier values:
      | default supplier | supplier5 |
    And supplier "supplier4" should have 1 products associated
    And supplier "supplier5" should have 1 products associated
    And supplier "supplier4" should have following details for product "even more unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product6          |       |     | 0        |
    And supplier "supplier5" should have following details for product "even more unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product6          |       |     | 0        |
    # Now I change the product type
    When I update product "product6" type to standard
    # Nothing should change
    Then product product6 should have the following suppliers assigned:
      | supplier4 |
      | supplier5 |
    And product product6 should have following supplier values:
      | default supplier | supplier5 |
    And supplier "supplier4" should have 1 products associated
    And supplier "supplier5" should have 1 products associated
    And supplier "supplier4" should have following details for product "even more unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product6          |       |     | 0        |
    And supplier "supplier5" should have following details for product "even more unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product6          |       |     | 0        |
    # Let's try again with a product that has combinations
    When I update product "product6" type to combinations
    When I generate combinations for product product6 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    Then product "product6" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product6SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product6SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product6MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product6MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And supplier "supplier4" should have following details for product "even more unique T-shirt":
      | attribute name          | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      | Size - S, Color - White |                    | $0.00           | product5          |       |     | 0        |
      | Size - S, Color - Black |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - White |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - Black |                    | $0.00           | product5          |       |     | 0        |
    And supplier "supplier5" should have following details for product "even more unique T-shirt":
      | attribute name          | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      | Size - S, Color - White |                    | $0.00           | product5          |       |     | 0        |
      | Size - S, Color - Black |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - White |                    | $0.00           | product5          |       |     | 0        |
      | Size - M, Color - Black |                    | $0.00           | product5          |       |     | 0        |
    # We switch the type again
    When I update product "product6" type to standard
    # Still no changes, associations are still present
    Then product product6 should have the following suppliers assigned:
      | supplier4 |
      | supplier5 |
    And product product6 should have following supplier values:
      | default supplier | supplier5 |
    And supplier "supplier4" should have 1 products associated
    And supplier "supplier5" should have 1 products associated
    And supplier "supplier4" should have following details for product "even more unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product6          |       |     | 0        |
    And supplier "supplier5" should have following details for product "even more unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product6          |       |     | 0        |
    When I delete product product6
    Then product product6 should not exist anymore
    And supplier "supplier4" should have 0 products associated
    And supplier "supplier5" should have 0 products associated

  Scenario: Supplier associations should still be present if I remove combinations
    When I add product "product7" with following information:
      | name[en-US] | even more unique T-shirt |
      | type        | combinations             |
    And product product7 type should be combinations
    But product product7 should have no combinations
    When I associate suppliers to product "product7"
      | supplier  | product_supplier  |
      | supplier5 | product7supplier5 |
      | supplier4 | product7supplier4 |
    Then product product7 should have the following suppliers assigned:
      | supplier4 |
      | supplier5 |
    And product product7 should have following supplier values:
      | default supplier | supplier5 |
    And supplier "supplier4" should have 1 products associated
    And supplier "supplier5" should have 1 products associated
    And supplier "supplier4" should have following details for product "even more unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product7          |       |     | 0        |
    And supplier "supplier5" should have following details for product "even more unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product7          |       |     | 0        |
    And I generate combinations for product product7 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    Then product "product7" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product7SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product7SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product7MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product7MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    # This is mainly used to assign references to product suppliers
    When I associate suppliers to product "product7"
      | supplier  | combination_suppliers                                                         |
      | supplier4 | product7SWhite:product7SWhiteSupplier4;product7SBlack:product7SBlackSupplier4 |
      | supplier5 | product7SWhite:product7SWhiteSupplier5;product7SBlack:product7SBlackSupplier5 |
    And combination "product7SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product7SWhiteSupplier4 | supplier4 |           | USD      | 0                  |
      | product7SWhiteSupplier5 | supplier5 |           | USD      | 0                  |
    # We update combination details to make sure it doesn't remove associations at this moment (previous implementation used to remove on each update)
    When I update following suppliers for combination "product7SWhite":
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product7SWhiteSupplier4 | supplier4 |           | USD      | 51                 |
      | product7SWhiteSupplier5 | supplier5 |           | USD      | 69                 |
    And combination "product7SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product7SWhiteSupplier4 | supplier4 |           | USD      | 51                 |
      | product7SWhiteSupplier5 | supplier5 |           | USD      | 69                 |
    # Now supplier list combinations
    And supplier "supplier4" should have following details for product "even more unique T-shirt":
      | attribute name          | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      | Size - S, Color - White |                    | $51.00          | product7          |       |     | 0        |
      | Size - S, Color - Black |                    | $0.00           | product7          |       |     | 0        |
      | Size - M, Color - White |                    | $0.00           | product7          |       |     | 0        |
      | Size - M, Color - Black |                    | $0.00           | product7          |       |     | 0        |
    And supplier "supplier5" should have following details for product "even more unique T-shirt":
      | attribute name          | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      | Size - S, Color - White |                    | $69.00          | product7          |       |     | 0        |
      | Size - S, Color - Black |                    | $0.00           | product7          |       |     | 0        |
      | Size - M, Color - White |                    | $0.00           | product7          |       |     | 0        |
      | Size - M, Color - Black |                    | $0.00           | product7          |       |     | 0        |
    # I delete all product combinations
    When I delete following combinations of product product7:
      | id reference   |
      | product7SWhite |
      | product7SBlack |
      | product7MWhite |
      | product7MBlack |
    Then product product7 should have no combinations
    And product product7 should have the following suppliers assigned:
      | supplier4 |
      | supplier5 |
    And product product7 should have following supplier values:
      | default supplier | supplier5 |
    # Now supplier can only list product, not combinations anymore
    And supplier "supplier4" should have following details for product "even more unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product7          |       |     | 0        |
    And supplier "supplier5" should have following details for product "even more unique T-shirt":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                |                    | $0.00           | product7          |       |     | 0        |
    When I delete product product7
    Then product product7 should not exist anymore
    And supplier "supplier4" should have 0 products associated
    And supplier "supplier5" should have 0 products associated
