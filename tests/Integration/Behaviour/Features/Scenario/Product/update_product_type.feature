# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-product-type
@restore-products-before-feature
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
    And I identify tax rules group named "US-AL Rate (4%)" as "us-al-tax-rate"
    And I identify tax rules group named "US-KS Rate (5.3%)" as "us-ks-tax-rate"

  Scenario: I update product type to combinations (stock is reset to zero)
    When I add product "productCombinations" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    Then product "productCombinations" should be disabled
    And product "productCombinations" type should be standard
    And product "productCombinations" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "productCombinations" should be assigned to following categories:
      | id reference | name | is default |
      | home         | Home | true       |
    When I update product "productCombinations" stock with following information:
      | delta_quantity | 51 |
    And product "productCombinations" should have following stock information:
      | quantity | 51 |
    When I update product "productCombinations" type to combinations
    Then product "productCombinations" type should be combinations
    And product "productCombinations" should have following stock information:
      | quantity | 0 |
    And product "productCombinations" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | -51            |
      | Puff Daddy | 51             |

  Scenario: I update product type to combinations (if stock was zero no problem occurs)
    When I add product "productCombinations2" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    Then product "productCombinations2" should be disabled
    And product "productCombinations2" type should be standard
    And product "productCombinations2" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "productCombinations2" should be assigned to following categories:
      | id reference | name | is default |
      | home         | Home | true       |
    And product "productCombinations2" should have following stock information:
      | quantity | 0 |
    When I update product "productCombinations2" type to combinations
    Then product "productCombinations2" type should be combinations
    And product "productCombinations2" should have following stock information:
      | quantity | 0 |

  Scenario: I update product type to virtual
    When I add product "virtualProduct" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    Then product "virtualProduct" should be disabled
    And product "virtualProduct" type should be standard
    And product "virtualProduct" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "virtualProduct" should be assigned to following categories:
      | id reference | name | is default |
      | home         | Home | true       |
    When I update product "virtualProduct" type to virtual
    Then product "virtualProduct" type should be virtual

  Scenario: I update product type to virtual (ecotax should be reset and impacted on price)
    Given shop configuration for "PS_USE_ECOTAX" is set to 1
    And shop configuration for "PS_ECOTAX_TAX_RULES_GROUP_ID" is set to us-ks-tax-rate
    When I add product "virtualProduct" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    Then product "virtualProduct" should be disabled
    And product "virtualProduct" type should be standard
    And product "virtualProduct" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "virtualProduct" should be assigned to following categories:
      | id reference | name | is default |
      | home         | Home | true       |
    When I update product "virtualProduct" with following values:
      | price              | 51.42           |
      | ecotax             | 8.56            |
      | tax rules group    | US-AL Rate (4%) |
    Then product virtualProduct should have following prices information:
      | price                   | 51.42   |
      # (51.42 + 4% = 53.4768) + (8.56 + 5.3%)
      | price_tax_included      | 62.49048 |
      | ecotax                  | 8.56     |
      | ecotax_tax_included     | 9.01368  |
    When I update product "virtualProduct" type to virtual
    Then product "virtualProduct" type should be virtual
    And product virtualProduct should have following prices information:
      | price                   | 59.98   |
      # 59.98 + 4% = 62.3792
      | price_tax_included      | 62.3792 |
      | ecotax                  | 0.00    |
      | ecotax_tax_included     | 0.00    |

  Scenario: I update product type to pack
    When I add product "packProduct" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    Then product "packProduct" should be disabled
    And product "packProduct" type should be standard
    And product "packProduct" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "packProduct" should be assigned to following categories:
      | id reference | name | is default |
      | home         | Home | true       |
    When I update product "packProduct" type to pack
    Then product "packProduct" type should be pack

  Scenario: I update product type to standard
    When I add product "standardProduct" with following information:
      | name[en-US] | bottle of beer |
      | type        | virtual        |
    Then product "standardProduct" should be disabled
    And product "standardProduct" type should be virtual
    And product "standardProduct" localized "name" should be:
      | locale | value          |
      | en-US  | bottle of beer |
    And product "standardProduct" should be assigned to following categories:
      | id reference | name | is default |
      | home         | Home | true       |
    When I update product "standardProduct" type to standard
    Then product "standardProduct" type should be standard

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
    And pack "productPack1" should contain products with following details:
      | product  | combination | quantity | name             | image url                                              |
      | product2 |             | 5        | shady sunglasses | http://myshop.com/img/p/{no_picture}-small_default.jpg |
    When I update product "productPack1" type to standard
    Then product "productPack1" type should be standard
    And pack "productPack1" should be empty

  Scenario: Changing combinations type should remove all combinations (and stock is reset to zero)
    Given I add product "productCombinations3" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product productCombinations3 type should be combinations
    And I generate combinations for product productCombinations3 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    Then product "productCombinations3" should have following combinations:
      | id reference               | combination name        | reference | attributes           | impact on price | quantity | is default | combination reference |
      | productCombinations3SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |                       |
      | productCombinations3SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |                       |
      | productCombinations3Blue   | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |                       |
      | productCombinations3MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |                       |
      | productCombinations3MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |                       |
      | productCombinations3MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |                       |
    When I update combination "productCombinations3SWhite" stock with following details:
      | delta quantity | 100 |
    Then combination "productCombinations3SWhite" should have following stock details:
      | combination stock detail   | value |
      | quantity                   | 100   |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "productCombinations3SWhite" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | 100            |
    And combination "productCombinations3SWhite" last stock movement increased by 100
    When I update combination "productCombinations3SBlack" stock with following details:
      | delta quantity | 50 |
    Then combination "productCombinations3SBlack" should have following stock details:
      | combination stock detail   | value |
      | quantity                   | 50    |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "productCombinations3SBlack" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | 50             |
    And combination "productCombinations3SBlack" last stock movement increased by 50
    # Product stock is the sum of all combinations
    And product "productCombinations3" should have following stock information:
      | quantity            | 150   |
      | minimal_quantity    | 1     |
      | location            |       |
      | low_stock_threshold | 0     |
      | low_stock_alert     | false |
      | available_date      |       |
    And product "productCombinations3" should have no stock movements
    When I update product "productCombinations3" type to standard
    Then product "productCombinations3" type should be standard
    And product "productCombinations3" should have no combinations
    And product "productCombinations3" should have following stock information:
      | quantity            | 0     |
      | minimal_quantity    | 1     |
      | location            |       |
      | low_stock_threshold | 0     |
      | low_stock_alert     | false |
      | available_date      |       |
    And product "productCombinations3" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | -150           |
    And product "productCombinations3" last stock movement decreased by 150

  Scenario: Changing virtual type should remove virtual file
    Given I add product "virtualProduct2" with following information:
      | name[en-US] | puffin icon |
      | type        | virtual     |
    And product "virtualProduct2" should not have a file
    And product virtualProduct2 type should be virtual
    And I add virtual product file "file1" to product "virtualProduct2" with following details:
      | filename reference | filename_file1  |
      | display name       | puffin-logo.png |
      | file name          | app_icon.png    |
    And product "virtualProduct2" should have a virtual product file "file1" with following details:
      | display name         | puffin-logo.png |
      | access days          | 0               |
      | download times limit | 0               |
      | expiration date      |                 |
    And file "file1" for product "virtualProduct2" exists in system
    When I update product "virtualProduct2" type to standard
    And product "virtualProduct2" should not have a file
    And file "file1" for product "virtualProduct2" should not exist in system
