# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-suppliers-multishop
@restore-products-before-feature
@restore-currencies-after-feature
@clear-cache-before-feature
@restore-shops-after-feature
@product-combination
@product-multishop
@update-combination-suppliers-multishop
Feature: Update product combination suppliers in Back Office (BO) when using multi-stire feature
  As an employee
  I need to be able to update product combination suppliers from BO when multi-store feature is enabled

  Background:
    Given shop "shop1" with name "test_shop" exists
    And I enable multishop feature
    And single shop context is loaded
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
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

  Scenario: Product suppliers can be updated when some combinations doesn't exist in default shop
    Given I add new supplier supplier1 with the following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | shops                   | [shop1,shop2]      |
    And I associate attribute group "Size" with shops "shop1,shop2"
    And I associate attribute group "Color" with shops "shop1,shop2"
    And I associate attribute "S" with shops "shop1,shop2"
    And I associate attribute "White" with shops "shop1,shop2"
    And I associate attribute "Black" with shops "shop1,shop2"
    And I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    When I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And default shop for product "product1" is shop1
    And I generate combinations in shop "shop2" for product "product1" using following attributes:
      | Size  | [S]           |
      | Color | [White,Black] |
    And product "product1" should have no combinations for shops "shop1"
    And product "product1" should have the following combinations for shops "shop2":
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
    And product "product1" should not have a default supplier
    And combination "product1SWhite" should not have any suppliers assigned
    And combination "product1SBlack" should not have any suppliers assigned
    When I associate suppliers to product "product1"
      | supplier  | combination_suppliers                                                         |
      | supplier1 | product1SWhite:product1SWhiteSupplier4;product1SBlack:product1SBlackSupplier4 |
    Then product product1 should have the following suppliers assigned:
      | supplier1 |
    Then product product1 should have following supplier values:
      | default supplier | supplier1 |
    And product product1 should have following supplier values:
      | default supplier           | supplier1 |
      | default supplier reference |           |
    And combination "product1SWhite" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product1SWhiteSupplier4 | supplier1 |           | USD      | 0                  |
    And combination "product1SBlack" should have following suppliers:
      | product_supplier        | supplier  | reference | currency | price_tax_excluded |
      | product1SBlackSupplier4 | supplier1 |           | USD      | 0                  |
