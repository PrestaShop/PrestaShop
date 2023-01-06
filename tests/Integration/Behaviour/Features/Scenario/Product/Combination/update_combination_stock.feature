# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-stock
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@update-combination-stock
Feature: Update product combination stock information in Back Office (BO)
  As an employee
  I need to be able to update product combination stock information from BO

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
    And I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And product "product1" combinations list search criteria is set to defaults
    And I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have no stock movements
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1Blue   | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" should have following stock information:
      | pack_stock_type     | default |
      | out_of_stock_type   | default |
      | quantity            | 0       |
      | minimal_quantity    | 1       |
      | location            |         |
      | low_stock_threshold | 0       |
      | low_stock_alert     | false   |
      | available_date      |         |
    And combination "product1SBlack" should have following stock details:
      | combination stock detail   | value |
      | quantity                   | 0     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |

  Scenario: I update combination stock:
    When I update combination "product1SBlack" with following values:
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | available date             | 2021-10-10  |
    And I update combination "product1SBlack" stock with following details:
      | delta quantity             | 100         |
      | location                   | Storage nr1 |
    Then combination "product1SBlack" should have following stock details:
      | combination stock detail   | value       |
      | quantity                   | 100         |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr1 |
      | available date             | 2021-10-10  |
    And combination "product1SBlack" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | 100            |
    And combination "product1SBlack" last stock movement increased by 100
    When I update combination "product1SWhite" with following values:
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | available date             | 2021-10-10  |
    And I update combination "product1SWhite" stock with following details:
      | delta quantity             | 50          |
      | location                   | Storage nr1 |
    Then combination "product1SWhite" should have following stock details:
      | combination stock detail   | value       |
      | quantity                   | 50          |
      | minimal quantity           | 10          |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr1 |
      | available date             | 2021-10-10  |
    And combination "product1SWhite" last stock movement increased by 50
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 50       | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 100      | false      |
      | product1Blue   | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    # Product quantity is the sum of all combinations' quantity
    And product "product1" should have following stock information:
      | quantity | 150 |
    When I update combination "product1SBlack" with following values:
      | minimal quantity    | 1           |
      | low stock threshold | 10          |
    And I update combination "product1SBlack" stock with following details:
      | delta quantity      | -101        |
      | location            | Storage nr2 |
    Then combination "product1SBlack" should have following stock details:
      | combination stock detail   | value       |
      | quantity                   | -1          |
      | minimal quantity           | 1           |
      | low stock threshold        | 10          |
      | low stock alert is enabled | true        |
      | location                   | Storage nr2 |
      | available date             | 2021-10-10  |
    And combination "product1SBlack" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | -101           |
      | Puff Daddy | 100            |
    And combination "product1SBlack" last stock movement decreased by 101
    And product "product1" should have following stock information:
      | quantity | 49 |
    When I update combination "product1SBlack" with following values:
      | minimal quantity           | 0          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | available date             | 2020-01-01 |
    And I update combination "product1SBlack" stock with following details:
      | delta quantity             | 1          |
      | location                   |            |
    Then combination "product1SBlack" should have following stock details:
      | combination stock detail   | value      |
      | quantity                   | 0          |
      | minimal quantity           | 0          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | location                   |            |
      | available date             | 2020-01-01 |
    And combination "product1SBlack" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | 1              |
      | Puff Daddy | -101           |
      | Puff Daddy | 100            |
    And combination "product1SBlack" last stock movement increased by 1
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 50       | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1Blue   | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" should have following stock information:
      | quantity | 50 |
    # Following assert makes sure that 0 delta quantity is valid input for command but is skipped and stock does not move
    When I update combination "product1SBlack" stock with following details:
      | delta quantity | 0 |
    Then combination "product1SBlack" should have following stock details:
      | combination stock detail   | value      |
      | quantity                   | 0          |
      | minimal quantity           | 0          |
      | low stock threshold        | 0          |
      | low stock alert is enabled | false      |
      | location                   |            |
      | available date             | 2020-01-01 |
    And combination "product1SBlack" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | 1              |
      | Puff Daddy | -101           |
      | Puff Daddy | 100            |
    And combination "product1SBlack" last stock movement increased by 1
    And product "product1" should have following stock information:
      | quantity | 50 |

  Scenario: I update combination stock using fixed quantity
    Given combination "product1SBlack" should have following stock details:
      | combination stock detail   | value |
      | quantity                   | 0     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And product "product1" should have no stock movements
    When I update combination "product1SBlack" stock with following details:
      | fixed quantity | 10 |
    Then combination "product1SBlack" should have following stock details:
      | combination stock detail   | value |
      | quantity                   | 10    |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "product1SBlack" last stock movement increased by 10
    And combination "product1SBlack" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | 10             |
    When I update combination "product1SBlack" stock with following details:
      | fixed quantity | -3 |
    Then combination "product1SBlack" should have following stock details:
      | combination stock detail   | value |
      | quantity                   | -3    |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And combination "product1SBlack" last stock movement decreased by 13
    And combination "product1SBlack" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | -13            |
      | Puff Daddy | 10             |

  Scenario: I should not be able to provide both delta and fixed quantities when updating combination stock information
    Given combination "product1SBlack" should have following stock details:
      | combination stock detail   | value |
      | quantity                   | 0     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And product "product1" should have no stock movements
    When I update combination "product1SBlack" stock with following details:
      | fixed quantity | -7 |
      | delta quantity | -5 |
    Then I should get error that it is not allowed to perform update using both - delta and fixed quantity
    And combination "product1SBlack" should have following stock details:
      | combination stock detail   | value |
      | quantity                   | 0     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    And product "product1" should have no stock movements

  Scenario: I should be able to fill product availability labels
    Given combination "product1SBlack" should have following stock details:
      | combination stock detail   | value |
      | quantity                   | 0     |
      | minimal quantity           | 1     |
      | low stock threshold        | 0     |
      | low stock alert is enabled | false |
      | location                   |       |
      | available date             |       |
    When I update combination "product1SBlack" with following values:
      | available now labels[en-US]   | Get it now    |
      | available later labels[en-US] | Too late dude |
    Then combination "product1SBlack" should have following stock details:
      | combination stock detail      | value         |
      | quantity                      | 0             |
      | minimal quantity              | 1             |
      | low stock threshold           | 0             |
      | low stock alert is enabled    | false         |
      | location                      |               |
      | available date                |               |
      | available now labels[en-US]   | Get it now    |
      | available later labels[en-US] | Too late dude |

  Scenario: I should be able to delete product availability labels
    Given combination "product1SBlack" should have following stock details:
      | combination stock detail      | value         |
      | quantity                      | 0             |
      | minimal quantity              | 1             |
      | low stock threshold           | 0             |
      | low stock alert is enabled    | false         |
      | location                      |               |
      | available date                |               |
      | available now labels[en-US]   | Get it now    |
      | available later labels[en-US] | Too late dude |
    When I update combination "product1SBlack" with following values:
      | available now labels[en-US]   |               |
      | available later labels[en-US] |               |
    Then combination "product1SBlack" should have following stock details:
      | combination stock detail      | value         |
      | quantity                      | 0             |
      | minimal quantity              | 1             |
      | low stock threshold           | 0             |
      | low stock alert is enabled    | false         |
      | location                      |               |
      | available date                |               |
      | available now labels[en-US]   |               |
      | available later labels[en-US] |               |

  Scenario: I update product out of stock
    And product "product1" should have following stock information:
      | out_of_stock_type | default |
    And all combinations of product "product1" should have the stock policy to "default"
    When I update product "product1" stock with following information:
      | out_of_stock_type | available |
    Then all combinations of product "product1" should have the stock policy to "available"
    When I update product "product1" stock with following information:
      | out_of_stock_type | default |
    Then all combinations of product "product1" should have the stock policy to "default"
    When I update product "product1" stock with following information:
      | out_of_stock_type | not_available |
    Then all combinations of product "product1" should have the stock policy to "not_available"

  Scenario: I shouldn't be able to add bigger quantity then 2147483647
    When I update combination "product1MBlack" stock with following details:
      | delta quantity             | 2147483648  |
      | location                   | Storage nr1 |
      | low stock threshold        | 0           |
      | low stock alert is enabled | false       |
      | available date             | 2021-10-10  |
    Then I should get error that stock available quantity is invalid
    When I update combination "product1MBlack" stock with following details:
      | delta quantity             | -2147483649 |
      | minimal quantity           | 1           |
      | location                   | Storage nr1 |
      | low stock threshold        | 0           |
      | low stock alert is enabled | false       |
      | available date             | 2021-10-10  |
    Then I should get error that stock available quantity is invalid

  Scenario: Adding biggest and smallest possible combination quantities
    When I update combination "product1MBlue" stock with following details:
      | delta quantity             | 2147483647  |
      | minimal quantity           | 1           |
      | location                   | Storage nr1 |
      | low stock threshold        | 0           |
      | low stock alert is enabled | false       |
    Then combination "product1MBlue" should have following stock details:
      | combination stock detail   | value       |
      | quantity                   | 2147483647  |
      | minimal quantity           | 1           |
      | low stock threshold        | 0           |
      | low stock alert is enabled | false       |
      | location                   | Storage nr1 |
      | available date             |             |
    And combination "product1MBlue" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | 2147483647     |
    When I update combination "product1MBlue" stock with following details:
      | delta quantity             | -4294967295 |
      | minimal quantity           | 1           |
      | location                   | Storage nr1 |
      | low stock threshold        | 0           |
      | low stock alert is enabled | false       |
      | available date             |             |
    Then combination "product1MBlue" should have following stock details:
      | combination stock detail   | value       |
      | quantity                   | -2147483648 |
      | minimal quantity           | 1           |
      | low stock threshold        | 0           |
      | low stock alert is enabled | false       |
      | location                   | Storage nr1 |
      | available date             |             |
    And combination "product1MBlue" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | -4294967295    |
      | Puff Daddy | 2147483647     |
    When I update combination "product1MBlue" stock with following details:
      | delta quantity             | 4294967295  |
      | minimal quantity           | 1           |
      | location                   | Storage nr1 |
      | low stock threshold        | 0           |
      | low stock alert is enabled | false       |
      | available date             |             |
    Then combination "product1MBlue" should have following stock details:
      | combination stock detail   | value       |
      | quantity                   | 2147483647  |
      | minimal quantity           | 1           |
      | low stock threshold        | 0           |
      | low stock alert is enabled | false       |
      | location                   | Storage nr1 |
      | available date             |             |
    And combination "product1MBlue" last stock movements should be:
      | employee   | delta_quantity |
      | Puff Daddy | 4294967295     |
      | Puff Daddy | -4294967295    |
      | Puff Daddy | 2147483647     |
