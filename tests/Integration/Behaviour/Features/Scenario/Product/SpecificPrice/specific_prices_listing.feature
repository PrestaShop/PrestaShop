# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags specific-prices-listing
@restore-products-before-feature
@clear-cache-before-feature
@specific-prices
@specific-prices-listing

Feature: List specific prices for product in Back Office (BO)
  As an employee
  I need to be able to see all product specific prices from BO

  Background:
    Given language with iso code "en" is the default one
    And shop "testShop" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And country "UnitedStates" with iso code "US" exists
    And group "visitor" named "Visitor" exists
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists

  Scenario: I can see a list of specific prices for standard product
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And product product1 does not have a default combination
    And I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black,Blue] |
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1SBlue  | Size - S, Color - Blue  |           | [Size:S,Color:Blue]  | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
      | product1MBlue  | Size - M, Color - Blue  |           | [Size:M,Color:Blue]  | 0               | 0        | false      |
    And product "product1" should have 0 specific prices
    When I add a specific price price1 to product product1 with following details:
      | reduction type  | amount |
      | reduction value | 111.50 |
      | includes tax    | true   |
      | fixed price     | 400    |
      | from quantity   | 1      |
    And I add a specific price price2 to product product1 with following details:
      | reduction type  | amount       |
      | reduction value | 12.56        |
      | includes tax    | true         |
      | fixed price     | 45.78        |
      | from quantity   | 1            |
      | shop            | testShop     |
      | currency        | usd          |
      | country         | UnitedStates |
      | group           | visitor      |
      | customer        | testCustomer |
    And I add a specific price price3 to product product1 with following details:
      | reduction type  | percentage     |
      | reduction value | 30             |
      | includes tax    | false          |
      | fixed price     | 0              |
      | from quantity   | 1              |
      | combination     | product1SWhite |
    And product "product1" should have 3 specific prices
    Then product "product1" should have following list of specific prices in "en" language:
      | price id | combination             | reduction type | reduction value | includes tax | fixed price | from quantity | shop      | currency | currencyISOCode | country       | group   | customer | from                | to                  |
      | price1   |                         | amount         | 111.50          | true         | 400         | 1             |           |          |                 |               |         |          | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | price2   |                         | amount         | 12.56           | true         | 45.78       | 1             | test_shop | USD      | USD             | United States | Visitor | John DOE | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | price3   | Size - S, Color - White | percentage     | 30              | false        | 0           | 1             |           |          |                 |               |         |          | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
