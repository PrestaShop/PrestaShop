# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-stock-classic
@reset-database-before-feature
@update-stock
@update-stock-classic
Feature: Update product stock from Back Office (BO)
  As a BO user
  I need to be able to update product stock from BO

  Background:
    Given shop configuration for "PS_ADVANCED_STOCK_MANAGEMENT" is set to 0

  Scenario: I update product stock management
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    And product "product1" should have following values:
      | use_advanced_stock_management | 0           |
    When I update product "product1" stock with following values:
      | use_advanced_stock_management | 1           |
    And I should get error that stock management is disabled
    Then product "product1" should have following values:
      | use_advanced_stock_management | 0           |

  Scenario: I update product depends on stock
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    And product "product1" should have following values:
      | depends_on_stock | 0           |
    When I update product "product1" stock with following values:
      | depends_on_stock | 1           |
    And I should get error that stock management is disabled
    Then product "product1" should have following values:
      | depends_on_stock | 0           |

  Scenario: I update product pack stock type
    Given I add product "productPack1" with following information:
      | name       | en-US: weird sunglasses box |
      | is_virtual | false                       |
    And product "productPack1" type should be standard
    And I add product "product2" with following information:
      | name       | en-US: shady sunglasses     |
      | is_virtual | false                       |
    And product "product2" type should be standard
    When I update pack "productPack1" with following product quantities:
      | product2        | 5                      |
    Then product "productPack1" type should be pack
    And pack "productPack1" should contain products with following quantities:
      | product2        | 5                      |
    And product "productPack1" should have following values:
      | pack_stock_type | stock_type_default |
    When I update product "productPack1" stock with following values:
      | pack_stock_type | stock_type_pack_only |
    Then product "productPack1" should have following values:
      | pack_stock_type | stock_type_pack_only |
    When I update product "productPack1" stock with following values:
      | pack_stock_type | stock_type_products_only |
    Then product "productPack1" should have following values:
      | pack_stock_type | stock_type_products_only |
    When I update product "productPack1" stock with following values:
      | pack_stock_type | stock_type_both |
    Then product "productPack1" should have following values:
      | pack_stock_type | stock_type_both |

