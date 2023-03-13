# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-prices-multishop
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@update-product-prices
@update-combination-prices-multishop
Feature: Update product combination prices in Back Office (BO) in multi shop context
  As an employee
  I need to be able to update product combination prices from BO for multiple shops

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
    And I identify tax rules group named "US-AL Rate (4%)" as "us-al-tax-rate"
    And I identify tax rules group named "US-KS Rate (5.3%)" as "us-ks-tax-rate"
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

  Scenario: I update combination prices for specific shop:
    Given I update product "product1" for shop "shop1" with following values:
      | price           | 51.49           |
      | ecotax          | 17.78           |
      | tax rules group | US-AL Rate (4%) |
    And I update product "product1" for shop "shop2" with following values:
      | price           | 21.99             |
      | ecotax          | 5.5               |
      | tax rules group | US-KS Rate (5.3%) |
    And combination "product1SWhite" should have following prices for shops "shop1":
      | combination price detail        | value |
      | impact on price                 | 0     |
      | impact on price with taxes      | 0     |
      | impact on unit price            | 0     |
      | impact on unit price with taxes | 0     |
      | eco tax                         | 0     |
      | eco tax with taxes              | 0     |
      | wholesale price                 | 0     |
      | product tax rate                | 4.00  |
      | product price                   | 51.49 |
      | product ecotax                  | 17.78 |
    And combination "product1SWhite" should have following prices for shops "shop2":
      | combination price detail        | value |
      | impact on price                 | 0     |
      | impact on price with taxes      | 0     |
      | impact on unit price            | 0     |
      | impact on unit price with taxes | 0     |
      | eco tax                         | 0     |
      | eco tax with taxes              | 0     |
      | wholesale price                 | 0     |
      | product tax rate                | 5.3   |
      | product price                   | 21.99 |
      | product ecotax                  | 5.5   |
    When I update combination "product1SWhite" with following values for shop "shop1":
      | eco tax              | 0.5 |
      | impact on price      | -5  |
      | impact on unit price | -1  |
      | wholesale price      | 20  |
    And I update combination "product1SWhite" with following values for shop "shop2":
      | eco tax              | 0.2 |
      | impact on price      | -10 |
      | impact on unit price | -2  |
      | wholesale price      | 30  |
    Then combination "product1SWhite" should have following prices for shops "shop1":
      | combination price detail        | value |
      | impact on price                 | -5    |
      | impact on price with taxes      | -5.20 |
      | impact on unit price            | -1    |
      | impact on unit price with taxes | -1.04 |
      | eco tax                         | 0.5   |
      | eco tax with taxes              | 0.5   |
      | wholesale price                 | 20    |
      | product tax rate                | 4.00  |
      | product price                   | 51.49 |
      | product ecotax                  | 17.78 |
    Then combination "product1SWhite" should have following prices for shops "shop2":
      | combination price detail        | value  |
      | impact on price                 | -10    |
      | impact on price with taxes      | -10.53 |
      | impact on unit price            | -2     |
      | impact on unit price with taxes | -2.106 |
      | eco tax                         | 0.2    |
      | eco tax with taxes              | 0.2    |
      | wholesale price                 | 30     |
      | product tax rate                | 5.3    |
      | product price                   | 21.99  |
      | product ecotax                  | 5.5    |
    # Enable ecotax
    When shop configuration for "PS_USE_ECOTAX" is set to 1
    And shop configuration for "PS_ECOTAX_TAX_RULES_GROUP_ID" is set to us-ks-tax-rate
    Then combination "product1SWhite" should have following prices for shops "shop1":
      | combination price detail        | value  |
      | impact on price                 | -5     |
      | impact on price with taxes      | -5.20  |
      | impact on unit price            | -1     |
      | impact on unit price with taxes | -1.04  |
      | eco tax                         | 0.5    |
      | eco tax with taxes              | 0.5265 |
      | wholesale price                 | 20     |
      | product tax rate                | 4.00   |
      | product price                   | 51.49  |
      | product ecotax                  | 17.78  |
    Then combination "product1SWhite" should have following prices for shops "shop2":
      | combination price detail        | value  |
      | impact on price                 | -10    |
      | impact on price with taxes      | -10.53 |
      | impact on unit price            | -2     |
      | impact on unit price with taxes | -2.106 |
      | eco tax                         | 0.2    |
      | eco tax with taxes              | 0.2106 |
      | wholesale price                 | 30     |
      | product tax rate                | 5.3    |
      | product price                   | 21.99  |
      | product ecotax                  | 5.5    |
    # Reset price
    When I update combination "product1SWhite" with following values for shop "shop1":
      | impact on price | 0 |
    Then combination "product1SWhite" should have following prices for shops "shop1":
      | combination price detail        | value  |
      | impact on price                 | 0      |
      | impact on price with taxes      | 0      |
      | impact on unit price            | -1     |
      | impact on unit price with taxes | -1.04  |
      | eco tax                         | 0.5    |
      | eco tax with taxes              | 0.5265 |
      | wholesale price                 | 20     |
      | product tax rate                | 4.00   |
      | product price                   | 51.49  |
      | product ecotax                  | 17.78  |
    And combination "product1SWhite" should have following prices for shops "shop2":
      | combination price detail        | value  |
      | impact on price                 | -10    |
      | impact on price with taxes      | -10.53 |
      | impact on unit price            | -2     |
      | impact on unit price with taxes | -2.106 |
      | eco tax                         | 0.2    |
      | eco tax with taxes              | 0.2106 |
      | wholesale price                 | 30     |
      | product tax rate                | 5.3    |
      | product price                   | 21.99  |
      | product ecotax                  | 5.5    |
    When I update combination "product1SWhite" with following values for shop "shop2":
      | impact on price | 0 |
    Then combination "product1SWhite" should have following prices for shops "shop2":
      | combination price detail        | value  |
      | impact on price                 | 0      |
      | impact on price with taxes      | 0      |
      | impact on unit price            | -2     |
      | impact on unit price with taxes | -2.106 |
      | eco tax                         | 0.2    |
      | eco tax with taxes              | 0.2106 |
      | wholesale price                 | 30     |
      | product tax rate                | 5.3    |
      | product price                   | 21.99  |
      | product ecotax                  | 5.5    |
    # Reset all
    When I update combination "product1SWhite" with following values for shop "shop1":
      | eco tax              | 0 |
      | impact on price      | 0 |
      | impact on unit price | 0 |
      | wholesale price      | 0 |
    And I update product "product1" for shop "shop1" with following values:
      | price           | 0 |
      | ecotax          | 0 |
      | tax rules group |   |
    Then combination "product1SWhite" should have following prices for shops "shop1":
      | combination price detail        | value |
      | impact on price                 | 0     |
      | impact on price with taxes      | 0     |
      | impact on unit price            | 0     |
      | impact on unit price with taxes | 0     |
      | eco tax                         | 0     |
      | eco tax with taxes              | 0     |
      | wholesale price                 | 0     |
      | product tax rate                | 0     |
      | product price                   | 0     |
      | product ecotax                  | 0     |
    And combination "product1SWhite" should have following prices for shops "shop2":
      | combination price detail        | value  |
      | impact on price                 | 0      |
      | impact on price with taxes      | 0      |
      | impact on unit price            | -2     |
      | impact on unit price with taxes | -2.106 |
      | eco tax                         | 0.2    |
      | eco tax with taxes              | 0.2106 |
      | wholesale price                 | 30     |
      | product tax rate                | 5.3    |
      | product price                   | 21.99  |
      | product ecotax                  | 5.5    |
    When I update combination "product1SWhite" with following values for shop "shop2":
      | eco tax              | 0 |
      | impact on price      | 0 |
      | impact on unit price | 0 |
      | wholesale price      | 0 |
    And I update product "product1" for shop "shop2" with following values:
      | price           | 0 |
      | ecotax          | 0 |
      | tax rules group |   |
    Then combination "product1SWhite" should have following prices for shops "shop1,shop2":
      | combination price detail        | value |
      | impact on price                 | 0     |
      | impact on price with taxes      | 0     |
      | impact on unit price            | 0     |
      | impact on unit price with taxes | 0     |
      | eco tax                         | 0     |
      | eco tax with taxes              | 0     |
      | wholesale price                 | 0     |
      | product tax rate                | 0     |
      | product price                   | 0     |
      | product ecotax                  | 0     |

  Scenario: I update combination prices for all shops:
    Given I update product "product1" for shop "shop1" with following values:
      | price           | 51.49           |
      | ecotax          | 17.78           |
      | tax rules group | US-AL Rate (4%) |
    And I update product "product1" for shop "shop2" with following values:
      | price           | 21.99             |
      | ecotax          | 5.5               |
      | tax rules group | US-KS Rate (5.3%) |
    And combination "product1SBlack" should have following prices for shops "shop1":
      | combination price detail        | value |
      | impact on price                 | 0     |
      | impact on price with taxes      | 0     |
      | impact on unit price            | 0     |
      | impact on unit price with taxes | 0     |
      | eco tax                         | 0     |
      | eco tax with taxes              | 0     |
      | wholesale price                 | 0     |
      | product tax rate                | 4.00  |
      | product price                   | 51.49 |
      | product ecotax                  | 17.78 |
    And combination "product1SBlack" should have following prices for shops "shop2":
      | combination price detail        | value |
      | impact on price                 | 0     |
      | impact on price with taxes      | 0     |
      | impact on unit price            | 0     |
      | impact on unit price with taxes | 0     |
      | eco tax                         | 0     |
      | eco tax with taxes              | 0     |
      | wholesale price                 | 0     |
      | product tax rate                | 5.3   |
      | product price                   | 21.99 |
      | product ecotax                  | 5.5   |
    When I update combination "product1SBlack" with following values for all shops:
      | eco tax              | 0.5 |
      | impact on price      | -5  |
      | impact on unit price | -1  |
      | wholesale price      | 20  |
    Then combination "product1SBlack" should have following prices for shops "shop1":
      | combination price detail        | value |
      | impact on price                 | -5    |
      | impact on price with taxes      | -5.20 |
      | impact on unit price            | -1    |
      | impact on unit price with taxes | -1.04 |
      | eco tax                         | 0.5   |
      | eco tax with taxes              | 0.5   |
      | wholesale price                 | 20    |
      | product tax rate                | 4.00  |
      | product price                   | 51.49 |
      | product ecotax                  | 17.78 |
    And combination "product1SBlack" should have following prices for shops "shop2":
      | combination price detail        | value  |
      | impact on price                 | -5     |
      | impact on price with taxes      | -5.265 |
      | impact on unit price            | -1     |
      | impact on unit price with taxes | -1.053 |
      | eco tax                         | 0.5    |
      | eco tax with taxes              | 0.5    |
      | wholesale price                 | 20     |
      | product tax rate                | 5.3    |
      | product price                   | 21.99  |
      | product ecotax                  | 5.5    |
    # Enable ecotax
    When shop configuration for "PS_USE_ECOTAX" is set to 1
    And shop configuration for "PS_ECOTAX_TAX_RULES_GROUP_ID" is set to us-ks-tax-rate
    Then combination "product1SBlack" should have following prices for shops "shop1":
      | combination price detail        | value  |
      | impact on price                 | -5     |
      | impact on price with taxes      | -5.20  |
      | impact on unit price            | -1     |
      | impact on unit price with taxes | -1.04  |
      | eco tax                         | 0.5    |
      | eco tax with taxes              | 0.5265 |
      | wholesale price                 | 20     |
      | product tax rate                | 4.00   |
      | product price                   | 51.49  |
      | product ecotax                  | 17.78  |
    And combination "product1SBlack" should have following prices for shops "shop2":
      | combination price detail        | value  |
      | impact on price                 | -5     |
      | impact on price with taxes      | -5.265 |
      | impact on unit price            | -1     |
      | impact on unit price with taxes | -1.053 |
      | eco tax                         | 0.5    |
      | eco tax with taxes              | 0.5265 |
      | wholesale price                 | 20     |
      | product tax rate                | 5.3    |
      | product price                   | 21.99  |
      | product ecotax                  | 5.5    |
    # Reset price
    When I update combination "product1SBlack" with following values for all shops:
      | impact on price | 0 |
    Then combination "product1SBlack" should have following prices for shops "shop1":
      | combination price detail        | value  |
      | impact on price                 | 0      |
      | impact on price with taxes      | 0      |
      | impact on unit price            | -1     |
      | impact on unit price with taxes | -1.04  |
      | eco tax                         | 0.5    |
      | eco tax with taxes              | 0.5265 |
      | wholesale price                 | 20     |
      | product tax rate                | 4.00   |
      | product price                   | 51.49  |
      | product ecotax                  | 17.78  |
    And combination "product1SBlack" should have following prices for shops "shop2":
      | combination price detail        | value  |
      | impact on price                 | 0      |
      | impact on price with taxes      | 0      |
      | impact on unit price            | -1     |
      | impact on unit price with taxes | -1.053 |
      | eco tax                         | 0.5    |
      | eco tax with taxes              | 0.5265 |
      | wholesale price                 | 20     |
      | product tax rate                | 5.3    |
      | product price                   | 21.99  |
      | product ecotax                  | 5.5    |
    # Reset all
    When I update combination "product1SBlack" with following values for all shops:
      | eco tax              | 0 |
      | impact on price      | 0 |
      | impact on unit price | 0 |
      | wholesale price      | 0 |
    And I update product "product1" for all shops with following values:
      | price           | 0 |
      | ecotax          | 0 |
      | tax rules group |   |
    Then combination "product1SBlack" should have following prices for shops "shop1,shop2":
      | combination price detail        | value |
      | impact on price                 | 0     |
      | impact on price with taxes      | 0     |
      | impact on unit price            | 0     |
      | impact on unit price with taxes | 0     |
      | eco tax                         | 0     |
      | eco tax with taxes              | 0     |
      | wholesale price                 | 0     |
      | product tax rate                | 0     |
      | product price                   | 0     |
      | product ecotax                  | 0     |
