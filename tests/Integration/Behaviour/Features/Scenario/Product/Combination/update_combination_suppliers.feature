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

  Scenario: Combinations wholesale price should depend on default supplier price
    And I add product "product2" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product2 type should be combinations
    And I generate combinations for product product2 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    And product "product2" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product2SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product2SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product2MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product2MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And combination "product2SWhite" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 0     |
    When I associate suppliers to product "product2"
      | supplier  | combination_suppliers                                                         |
      | supplier2 | product2SWhite:product2SWhiteSupplier2;product2SBlack:product2SBlackSupplier2 |
      | supplier1 | product2SWhite:product2SWhiteSupplier1;product2SBlack:product2SBlackSupplier1 |
    Given product product2 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And combination "product2SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product2SWhiteSupplier1 | supplier1 |           | USD      | 0                  |
      | product2SWhiteSupplier2 | supplier2 |           | USD      | 0                  |
    And product product2 should have following supplier values:
      | default supplier           | supplier2 |
    # Now I update suppliers values, the wholesale price of the combination should have the same value as the default supplier
    When I update following suppliers for combination "product2SWhite":
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product2SWhiteSupplier1 | supplier1 |           | USD      | 51                 |
      | product2SWhiteSupplier2 | supplier2 |           | USD      | 69                 |
    And I update following suppliers for combination "product2SBlack":
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product2SBlackSupplier1 | supplier1 |           | USD      | 44                 |
      | product2SBlackSupplier2 | supplier2 |           | USD      | 49                 |
    Then combination "product2SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product2SWhiteSupplier1 | supplier1 |           | USD      | 51                 |
      | product2SWhiteSupplier2 | supplier2 |           | USD      | 69                 |
    And combination "product2SBlack" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product2SBlackSupplier1 | supplier1 |           | USD      | 44                 |
      | product2SBlackSupplier2 | supplier2 |           | USD      | 49                 |
    And combination "product2SWhite" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 69    |
    And combination "product2SBlack" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 49    |
    And combination "product2MWhite" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 0     |
    And combination "product2MBlack" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 0     |
    # Change the default supplier the wholesale price should also be updated
    When I set product product2 default supplier to supplier1
    And product product2 should have following supplier values:
      | default supplier           | supplier1 |
      | default supplier reference |           |
    And combination "product2SWhite" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 51    |
    And combination "product2SBlack" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 44    |
    And combination "product2MWhite" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 0     |
    And combination "product2MBlack" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 0     |

  Scenario: Updating combination wholesale price should update default supplier price
    Given I add product "product3" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product3 type should be combinations
    And I generate combinations for product product3 using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    Then product "product3" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product3SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product3SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product3MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product3MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And combination "product3SWhite" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 0     |
    When I associate suppliers to product "product3"
      | supplier  | combination_suppliers                  |
      | supplier2 | product3SWhite:product3SWhiteSupplier2 |
      | supplier1 | product3SWhite:product3SWhiteSupplier1 |
    Then product product3 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And combination "product3SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product3SWhiteSupplier1 | supplier1 |           | USD      | 0                  |
      | product3SWhiteSupplier2 | supplier2 |           | USD      | 0                  |
    And product product3 should have following supplier values:
      | default supplier           | supplier2 |
    # Now I update combination wholesale price it should update the default supplier price
    When I update combination "product3SWhite" prices with following details:
      | wholesale price      | 20  |
    Then combination "product3SWhite" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 20    |
    And combination "product3SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product3SWhiteSupplier1 | supplier1 |           | USD      | 0                  |
      | product3SWhiteSupplier2 | supplier2 |           | USD      | 20                 |
    When I set product product3 default supplier to supplier1
    Then product product3 should have following supplier values:
      | default supplier           | supplier1 |
    # Back to 0 since it's the value for supplier1
    And combination "product3SWhite" should have following prices:
      | combination price detail   | value |
      | eco tax                    | 0     |
      | impact on price            | 0     |
      | impact on price with taxes | 0     |
      | impact on unit price       | 0     |
      | wholesale price            | 0     |
    When I update combination "product3SWhite" prices with following details:
      | wholesale price      | 44  |
    # The new default supplier is updated
    And combination "product3SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product3SWhiteSupplier1 | supplier1 |           | USD      | 44                  |
      | product3SWhiteSupplier2 | supplier2 |           | USD      | 20                 |

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
      | default supplier           | supplier2 |
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

  Scenario: I should be able to associate suppliers even when no combinations has been created
    Given I add product "product5" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product5 type should be combinations
    When I associate suppliers to product "product5"
      | supplier  | product_supplier  |
      | supplier2 | product5supplier2 |
      | supplier1 | product5supplier1 |
    Then product product5 should have the following suppliers assigned:
      | supplier1 |
      | supplier2 |
    And product product5 should have following supplier values:
      | default supplier           | supplier2 |
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
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product5SBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product5MWhite" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
    And combination "product5MBlack" should have following suppliers:
      | supplier  | reference | currency | price_tax_excluded |
      | supplier1 |           | USD      | 0                  |
      | supplier2 |           | USD      | 0                  |
