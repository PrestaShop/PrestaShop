# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-stock-multishop
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@update-combination-stock-multishop
Feature: Update product combination stock information in Back Office (BO) in multi shop context
  As an employee
  I need to be able to update product combination stock information from BO for multiple shops

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists
    And shop "shop1" with name "test_shop" exists
    And I enable multishop feature
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I associate attribute group "Size" with shops "shop1,shop2"
    And I associate attribute group "Color" with shops "shop1,shop2"
    And I associate attribute "S" with shops "shop1,shop2"
    And I associate attribute "M" with shops "shop1,shop2"
    And I associate attribute "L" with shops "shop1,shop2"
    And I associate attribute "White" with shops "shop1,shop2"
    And I associate attribute "Black" with shops "shop1,shop2"
    And I associate attribute "Blue" with shops "shop1,shop2"
    And I associate attribute "Red" with shops "shop1,shop2"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And Shop group test_second_shop_group shares its stock
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test_shop_without_url" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded
    And I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And I generate combinations in shop "shop1" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have no combinations for shops "shop2"
    And I generate combinations in shop "shop2" for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have the following combinations for shops "shop1,shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" default combination for shop "shop1" should be "product1SWhite"
    And product "product1" default combination for shop "shop2" should be "product1SWhite"
    And combinations "product1SWhite,product1SBlack,product1SBlue,product1MWhite,product1MBlack,product1MBlue" are associated to shop "shop1"
    And combinations "product1SWhite,product1SBlack,product1SBlue,product1MWhite,product1MBlack,product1MBlue" are associated to shop "shop2"
    And product "product1" should have following stock information for shops "shop1,shop2":
      | pack_stock_type     | default |
      | out_of_stock_type   | default |
      | quantity            | 0       |
      | minimal_quantity    | 1       |
      | location            |         |
      | low_stock_threshold | 0       |
      | low_stock_alert     | false   |
      | available_date      |         |
    And combination "product1SBlack" should have following stock details for shops "shop1,shop2":
      | combination stock detail   | value |
      | quantity                   | 0     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |

  Scenario: I update combination stock for specific shop using delta quantity:
    When I update combination "product1SBlack" with following values for shop "shop1":
      | minimal quantity           | 10         |
      | low stock threshold        | 10         |
      | low stock alert is enabled | true       |
      | available date             | 2021-10-10 |
    And I update combination "product1SBlack" stock for shop "shop1" with following details:
      | delta quantity | 100         |
      | location       | Storage nr1 |
    Then combination "product1SBlack" should have following stock details for shops "shop1":
      | combination stock detail   | value       |
      | quantity                   | 100         |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr1 |
      | available date             | 2021-10-10  |
    And combination "product1SBlack" last stock movements for shop "shop1" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 100            |
    And combination "product1SBlack" last stock movement for shop "shop1" increased by 100
    And combination "product1SBlack" should have following stock details for shops "shop2":
      | combination stock detail   | value |
      | quantity                   | 0     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "product1SBlack" should have no stock movements for shop "shop2"
    When I update combination "product1SBlack" with following values for shop "shop2":
      | minimal quantity           | 1          |
      | low stock threshold        | 5          |
      | low stock alert is enabled | true       |
      | available date             | 2022-10-10 |
    And I update combination "product1SBlack" stock for shop "shop2" with following details:
      | delta quantity | 101         |
      | location       | Storage nr2 |
    Then combination "product1SBlack" should have following stock details for shops "shop2":
      | combination stock detail   | value       |
      | quantity                   | 101         |
      | minimal quantity           | 1           |
      | low stock threshold        | 5           |
      | low stock alert is enabled | true        |
      | location                   | Storage nr2 |
      | available date             | 2022-10-10  |
    And combination "product1SBlack" last stock movements for shop "shop2" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 101            |
    And combination "product1SBlack" last stock movement for shop "shop2" increased by 101
    And combination "product1SBlack" should have following stock details for shops "shop1":
      | combination stock detail   | value       |
      | quantity                   | 100         |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr1 |
      | available date             | 2021-10-10  |
    And combination "product1SBlack" last stock movements for shop "shop1" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 100            |
    And combination "product1SBlack" last stock movement for shop "shop1" increased by 100
    When I update combination "product1SWhite" with following values for shop "shop1":
      | minimal quantity           | 10         |
      | low stock threshold        | 10         |
      | low stock alert is enabled | true       |
      | available date             | 2021-10-10 |
    And I update combination "product1SWhite" stock for shop "shop1" with following details:
      | delta quantity | 50          |
      | location       | Storage nr1 |
    And I update combination "product1SWhite" with following values for shop "shop2":
      | minimal quantity           | 10         |
      | low stock threshold        | 10         |
      | low stock alert is enabled | true       |
      | available date             | 2021-10-10 |
    And I update combination "product1SWhite" stock for shop "shop2" with following details:
      | delta quantity | 30          |
      | location       | Storage nr2 |
    Then combination "product1SWhite" should have following stock details for shops "shop1":
      | combination stock detail   | value       |
      | quantity                   | 50          |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr1 |
      | available date             | 2021-10-10  |
    And combination "product1SWhite" should have following stock details for shops "shop2":
      | combination stock detail   | value       |
      | quantity                   | 30          |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr2 |
      | available date             | 2021-10-10  |
    And combination "product1SWhite" last stock movement for shop "shop1" increased by 50
    And combination "product1SWhite" last stock movements for shop "shop1" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 50             |
    And combination "product1SWhite" last stock movement for shop "shop2" increased by 30
    And combination "product1SWhite" last stock movements for shop "shop2" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 30             |

    And product "product1" should have the following combinations for shops "shop1":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 50       | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 100      | false      |
      | product1Blue   | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" should have the following combinations for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 30       | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 101      | false      |
      | product1Blue   | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    # Product quantity is the sum of all combinations' quantity
    And product "product1" should have following stock information for shops "shop1":
      | quantity | 150 |
    And product "product1" should have following stock information for shops "shop2":
      | quantity | 131 |

  Scenario: I update combination stock for specific shop using fixed quantity
    Given combination "product1SBlack" should have following stock details for shops "shop1":
      | combination stock detail   | value |
      | quantity                   | 0     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And product "product1" should have no stock movements for shop "shop1"
    When I update combination "product1SBlack" stock for shop "shop1" with following details:
      | fixed quantity | 10 |
    Then combination "product1SBlack" should have following stock details for shops "shop1":
      | combination stock detail   | value |
      | quantity                   | 10    |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    When I update combination "product1SBlack" stock for shop "shop1" with following details:
      | fixed quantity | -3 |
    Then combination "product1SBlack" should have following stock details for shops "shop1":
      | combination stock detail   | value |
      | quantity                   | -3    |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "product1SBlack" last stock movement for shop "shop1" decreased by 13
    And combination "product1SBlack" last stock movements for shop "shop1" should be:
      | employee   | delta_quantity |
      | Puff Daddy | -13            |
      | Puff Daddy | 10             |
    And combination "product1SBlack" should have following stock details for shops "shop2":
      | combination stock detail   | value |
      | quantity                   | 0     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "product1SBlack" should have no stock movements for shop "shop2"
    When I update combination "product1SBlack" stock for shop "shop2" with following details:
      | fixed quantity | 34 |
    Then combination "product1SBlack" should have following stock details for shops "shop2":
      | combination stock detail   | value |
      | quantity                   | 34    |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "product1SBlack" last stock movement for shop "shop2" increased by 34
    And combination "product1SBlack" last stock movements for shop "shop2" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 34             |
    When I update combination "product1SBlack" stock for shop "shop2" with following details:
      | fixed quantity | 4 |
    Then combination "product1SBlack" should have following stock details for shops "shop2":
      | combination stock detail   | value |
      | quantity                   | 4     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "product1SBlack" last stock movement for shop "shop2" decreased by 30
    And combination "product1SBlack" last stock movements for shop "shop2" should be:
      | employee   | delta_quantity |
      | Puff Daddy | -30            |
      | Puff Daddy | 34             |
    And combination "product1SBlack" should have following stock details for shops "shop1":
      | combination stock detail   | value |
      | quantity                   | -3    |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "product1SBlack" last stock movement for shop "shop1" decreased by 13
    And combination "product1SBlack" last stock movements for shop "shop1" should be:
      | employee   | delta_quantity |
      | Puff Daddy | -13            |
      | Puff Daddy | 10             |

  Scenario: I update combination stock for all shops using delta quantity:
    Given I update combination "product1SBlack" with following values for all shops:
      | minimal quantity           | 10         |
      | low stock threshold        | 10         |
      | low stock alert is enabled | true       |
      | available date             | 2021-10-10 |
    And I update combination "product1SBlack" stock for shop "shop1" with following details:
      | delta quantity | 10          |
      | location       | Storage nr1 |
    And I update combination "product1SBlack" stock for shop "shop2" with following details:
      | delta quantity | 100         |
      | location       | Storage nr2 |
    And combination "product1SBlack" should have following stock details for shops "shop1":
      | combination stock detail   | value       |
      | quantity                   | 10          |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr1 |
      | available date             | 2021-10-10  |
    And combination "product1SBlack" should have following stock details for shops "shop2":
      | combination stock detail   | value       |
      | quantity                   | 100         |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr2 |
      | available date             | 2021-10-10  |
    When I update combination "product1SBlack" stock for all shops with following details:
      | delta quantity | 120         |
      | location       | Storage nr3 |
    Then combination "product1SBlack" should have following stock details for shops "shop1":
      | combination stock detail   | value       |
      | quantity                   | 130         |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr3 |
      | available date             | 2021-10-10  |
    Then combination "product1SBlack" should have following stock details for shops "shop2":
      | combination stock detail   | value       |
      | quantity                   | 220         |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr3 |
      | available date             | 2021-10-10  |
    And combination "product1SBlack" last stock movements for shop "shop1" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 120            |
      | Puff Daddy | 10             |
    And combination "product1SBlack" last stock movement for shop "shop1" increased by 120
    And combination "product1SBlack" last stock movements for shop "shop2" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 120            |
      | Puff Daddy | 100            |
    And combination "product1SBlack" last stock movement for shop "shop2" increased by 120
    And combinations "product1SBlack" are not associated to shop "shop3"
    And combinations "product1SBlack" are not associated to shop "shop4"

  Scenario: I update combination stock for all shops using fixed quantity
    Given I update combination "product1SBlack" stock for shop "shop2" with following details:
      | delta quantity | 10 |
    And combination "product1SBlack" should have following stock details for shops "shop1":
      | combination stock detail   | value |
      | quantity                   | 0     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "product1SBlack" should have following stock details for shops "shop2":
      | combination stock detail   | value |
      | quantity                   | 10    |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And product "product1" should have no stock movements for shop "shop1"
    And combination "product1SBlack" last stock movement for shop "shop2" increased by 10
    And combination "product1SBlack" last stock movements for shop "shop2" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 10             |
    When I update combination "product1SBlack" stock for all shops with following details:
      | fixed quantity | 12 |
    Then combination "product1SBlack" should have following stock details for shops "shop1,shop2":
      | combination stock detail   | value |
      | quantity                   | 12    |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "product1SBlack" last stock movement for shop "shop1" increased by 12
    And combination "product1SBlack" last stock movements for shop "shop1" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 12             |
    And combination "product1SBlack" last stock movement for shop "shop2" increased by 2
    And combination "product1SBlack" last stock movements for shop "shop2" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 2              |
      | Puff Daddy | 10             |
    When I update combination "product1SBlack" stock for all shops with following details:
      | fixed quantity | 5 |
    Then combination "product1SBlack" should have following stock details for shops "shop1,shop2":
      | combination stock detail   | value |
      | quantity                   | 5     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "product1SBlack" last stock movement for shop "shop1" decreased by 7
    And combination "product1SBlack" last stock movements for shop "shop1" should be:
      | employee   | delta_quantity |
      | Puff Daddy | -7             |
      | Puff Daddy | 12             |
    And combination "product1SBlack" last stock movement for shop "shop2" decreased by 7
    And combination "product1SBlack" last stock movements for shop "shop2" should be:
      | employee   | delta_quantity |
      | Puff Daddy | -7             |
      | Puff Daddy | 2              |
      | Puff Daddy | 10             |
    And combinations "product1SBlack" are not associated to shop "shop3"
    And combinations "product1SBlack" are not associated to shop "shop4"

  Scenario: I update product availability labels and expect them to always change in all shops, no matter which shop is being updated
    Given combination "product1SBlack" should have following stock details:
      | combination stock detail   | value |
      | quantity                   | 0     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    When I update combination "product1SBlack" with following values for shop "shop1":
      | available now labels[en-US]   | Get it now    |
      | available later labels[en-US] | Too late dude |
    Then combination "product1SBlack" should have following stock details for shops "shop1,shop2":
      | combination stock detail      | value         |
      | quantity                      | 0             |
      | minimal quantity              | 1             |
      | low stock threshold           | 0             |
      | low stock alert is enabled    | false         |
      | location                      |               |
      | available date                |               |
      | available now labels[en-US]   | Get it now    |
      | available later labels[en-US] | Too late dude |
    When I update combination "product1SBlack" with following values for shop "shop2":
      | available now labels[en-US]   | Get it now2    |
      | available later labels[en-US] | Too late dude2 |
    Then combination "product1SBlack" should have following stock details for shops "shop1,shop2":
      | combination stock detail      | value          |
      | quantity                      | 0              |
      | minimal quantity              | 1              |
      | low stock threshold           | 0              |
      | low stock alert is enabled    | false          |
      | location                      |                |
      | available date                |                |
      | available now labels[en-US]   | Get it now2    |
      | available later labels[en-US] | Too late dude2 |
    When I update combination "product1SBlack" with following values for all shops:
      | available now labels[en-US]   | Get it now all shops    |
      | available later labels[en-US] | Too late dude all shops |
    Then combination "product1SBlack" should have following stock details for shops "shop1,shop2":
      | combination stock detail      | value                   |
      | quantity                      | 0                       |
      | minimal quantity              | 1                       |
      | low stock threshold           | 0                       |
      | low stock alert is enabled    | false                   |
      | location                      |                         |
      | available date                |                         |
      | available now labels[en-US]   | Get it now all shops    |
      | available later labels[en-US] | Too late dude all shops |
    When I update combination "product1SBlack" with following values for all shops:
      | available now labels[en-US]   |  |
      | available later labels[en-US] |  |
    Then combination "product1SBlack" should have following stock details for shops "shop1,shop2":
      | combination stock detail      | value |
      | quantity                      | 0     |
      | minimal quantity              | 1     |
      | low stock threshold           | 0     |
      | low stock alert is enabled    | false |
      | location                      |       |
      | available date                |       |
      | available now labels[en-US]   |       |
      | available later labels[en-US] |       |

  Scenario: I update product out of stock type to see how the combinations stock policy depends on it
    And product "product1" should have following stock information for shops "shop1,shop2":
      | out_of_stock_type | default |
    And all combinations of product "product1" for shops "shop1,shop2" should have the stock policy to "default"
    When I update product "product1" stock for shop "shop1" with following information:
      | out_of_stock_type | available |
    And I update product "product1" stock for shop "shop2" with following information:
      | out_of_stock_type | default |
    Then all combinations of product "product1" for shops "shop1" should have the stock policy to "available"
    Then all combinations of product "product1" for shops "shop2" should have the stock policy to "default"
    When I update product "product1" stock for shop "shop2" with following information:
      | out_of_stock_type | available |
    Then all combinations of product "product1" for shops "shop1,shop2" should have the stock policy to "available"
    When I update product "product1" stock for all shops with following information:
      | out_of_stock_type | not_available |
    Then all combinations of product "product1" for shops "shop1,shop2" should have the stock policy to "not_available"

  Scenario: I copy product to shops belonging to a group that shares stock, modify one shop affects all the shop from the group
    Given I update combination "product1SBlack" stock for shop "shop1" with following details:
      | delta quantity | 50              |
      | location       | location shop 1 |
    And I update combination "product1SBlack" stock for shop "shop1" with following details:
      | delta quantity | 50 |
    # Update another combination just to keep track of the sum on product
    And I update combination "product1SWhite" stock for shop "shop1" with following details:
      | delta quantity | 50 |
    Then product "product1" should have following stock information for shops "shop1":
      | quantity | 150 |
    Then combination "product1SBlack" should have following stock details for shops "shop1":
      | combination stock detail   | value           |
      | quantity                   | 100             |
      | minimal quantity           | 1               |
      | low stock threshold        | 0               |
      | low stock alert is enabled | false           |
      | location                   | location shop 1 |
      | available date             |                 |
    And combination "product1SBlack" last stock movements for shop "shop1" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 50             |
      | Puff Daddy | 50             |
    Given I set following shops for product "product1":
      | source shop | shop1                   |
      | shops       | shop1,shop3,shop4 |
    Then combination "product1SBlack" should have following stock details for shops "shop1,shop3,shop4":
      | combination stock detail   | value           |
      | quantity                   | 100             |
      | minimal quantity           | 1               |
      | low stock threshold        | 0               |
      | low stock alert is enabled | false           |
      | location                   | location shop 1 |
      | available date             |                 |
    And combination "product1SBlack" last stock movements for shop "shop1" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 50             |
      | Puff Daddy | 50             |
    And combination "product1SBlack" last stock movements for shop "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | 100            |
    And product "product1" should have following stock information for shops "shop1,shop3,shop4":
      | quantity | 150 |
    Given I update combination "product1SBlack" stock for shop "shop3" with following details:
      | delta quantity | -42 |
    Then combination "product1SBlack" should have following stock details for shops "shop3,shop4":
      | combination stock detail   | value           |
      | quantity                   | 58              |
      | minimal quantity           | 1               |
      | low stock threshold        | 0               |
      | low stock alert is enabled | false           |
      | location                   | location shop 1 |
      | available date             |                 |
    And combination "product1SBlack" last stock movements for shop "shop3,shop4" should be:
      | employee   | delta_quantity |
      | Puff Daddy | -42            |
      | Puff Daddy | 100            |
    And product "product1" should have following stock information for shops "shop1":
      | quantity | 150 |
    And product "product1" should have following stock information for shops "shop3,shop4":
      | quantity | 108 |
