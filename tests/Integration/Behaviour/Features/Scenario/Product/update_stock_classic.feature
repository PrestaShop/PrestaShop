# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-stock-classic
@reset-database-before-feature
@clear-cache-before-feature
@update-stock
@update-stock-classic
Feature: Update product stock from Back Office (BO)
  As a BO user
  I need to be able to update product stock from BO

  Background:
    Given shop "shop1" with name "test_shop" exists
    # Single shop context is required to modify product quantity
    And single shop shop1 context is loaded
    And shop configuration for "PS_ADVANCED_STOCK_MANAGEMENT" is set to 0

  Scenario: I update product stock management
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    And product "product1" should have following stock information:
      | use_advanced_stock_management | 0           |
    When I update product "product1" stock with following information:
      | use_advanced_stock_management | 1           |
    And I should get error that stock management is disabled
    Then product "product1" should have following stock information:
      | use_advanced_stock_management | 0           |

  Scenario: I update product depends on stock
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    And product "product1" should have following stock information:
      | depends_on_stock | 0           |
    When I update product "product1" stock with following information:
      | depends_on_stock | 1           |
    And I should get error that stock management is disabled
    Then product "product1" should have following stock information:
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
      | product  | quantity |
      | product2 | 5        |
    Then product "productPack1" type should be pack
    And pack "productPack1" should contain products with following quantities:
      | product  | quantity |
      | product2 | 5        |
    And product "productPack1" should have following stock information:
      | pack_stock_type | stock_type_default |
    When I update product "productPack1" stock with following information:
      | pack_stock_type | stock_type_pack_only |
    Then product "productPack1" should have following stock information:
      | pack_stock_type | stock_type_pack_only |
    When I update product "productPack1" stock with following information:
      | pack_stock_type | stock_type_products_only |
    Then product "productPack1" should have following stock information:
      | pack_stock_type | stock_type_products_only |
    When I update product "productPack1" stock with following information:
      | pack_stock_type | stock_type_both |
    Then product "productPack1" should have following stock information:
      | pack_stock_type | stock_type_both |
    When I update product "productPack1" stock with following information:
      | pack_stock_type | invalid |
    Then I should get error that pack stock type is invalid
    And product "productPack1" should have following stock information:
      | pack_stock_type | stock_type_both |

  Scenario: I update product out of stock
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    And product "product1" should have following stock information:
      | out_of_stock_type | out_of_stock_default |
    When I update product "product1" stock with following information:
      | out_of_stock_type | out_of_stock_available |
    Then product "product1" should have following stock information:
      | out_of_stock_type | out_of_stock_available |
    When I update product "product1" stock with following information:
      | out_of_stock_type | out_of_stock_not_available |
    Then product "product1" should have following stock information:
      | out_of_stock_type | out_of_stock_not_available |
    When I update product "product1" stock with following information:
      | out_of_stock_type | out_of_stock_default |
    Then product "product1" should have following stock information:
      | out_of_stock_type | out_of_stock_default |
    When I update product "product1" stock with following information:
      | out_of_stock_type | invalid |
    Then I should get error that out of stock type is invalid

  Scenario: Virtual product is available out of stock by default
    Given I add product "product1" with following information:
      | name       | en-US:eBook |
      | is_virtual | true        |
    Then product "product1" should have following stock information:
      | out_of_stock_type | out_of_stock_available |

  Scenario: I update product quantity
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    And product "product1" should have following stock information:
      | quantity | 0 |
    When I update product "product1" stock with following information:
      | quantity | 51 |
    Then product "product1" should have following stock information:
      | quantity | 51 |
    And product "product1" last stock movement has following details:
      | physical_quantity | 51 |
      | sign              | 1  |
    When I update product "product1" stock with following information:
      | quantity | 42 |
    Then product "product1" should have following stock information:
      | quantity | 42 |
    And product "product1" last stock movement has following details:
      | physical_quantity | 9  |
      | sign              | -1 |

  Scenario: I update product quantity specifying if movement must be added or not
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    And product "product1" should have following stock information:
      | quantity     | 0 |
    When I update product "product1" stock with following information:
      | quantity     | 51    |
      | add_movement | false |
    And product "product1" has no stock movements
    When I update product "product1" stock with following information:
      | quantity     | 42   |
      | add_movement | true |
    Then product "product1" should have following stock information:
      | quantity | 42 |
    And product "product1" last stock movement has following details:
      | physical_quantity | 9  |
      | sign              | -1 |

  Scenario: I update product simple stock fields
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    And product "product1" should have following stock information:
      | minimal_quantity       | 1          |
      | location               |            |
      | low_stock_threshold    | 0          |
      | low_stock_alert        | false      |
      | available_date         | 0000-00-00 |
    And product "product1" localized "available_now_labels" should be "en-US:"
    And product "product1" localized "available_later_labels" should be "en-US:"
    When I update product "product1" stock with following information:
      | minimal_quantity       | 12                 |
      | location               | dtc                |
      | low_stock_threshold    | 42                 |
      | low_stock_alert        | true               |
      | available_now_labels   | en-US:get it now   |
      | available_later_labels | en-US:too late bro |
      | available_date         | 1969-07-16         |
    And product "product1" should have following stock information:
      | minimal_quantity       | 12                 |
      | location               | dtc                |
      | low_stock_threshold    | 42                 |
      | low_stock_alert        | true               |
      | available_date         | 1969-07-16         |
    And product "product1" localized "available_now_labels" should be "en-US:get it now"
    And product "product1" localized "available_later_labels" should be "en-US:too late bro"

  Scenario: When I use invalid values update is not authorized
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    And product "product1" should have following stock information:
      | quantity                      | 0          |
      | minimal_quantity              | 1          |
      | location                      |            |
      | low_stock_threshold           | 0          |
      | low_stock_alert               | false      |
      | available_date                | 0000-00-00 |
    And product "product1" localized "available_now_labels" should be "en-US:"
    And product "product1" localized "available_later_labels" should be "en-US:"
    When I update product "product1" stock with following information:
      | minimal_quantity | -1 |
    Then I should get error that product minimal_quantity is invalid
    When I update product "product1" stock with following information:
      | location | ssf> |
    Then I should get error that product location is invalid
    When I update product "product1" stock with following information:
      | available_now_labels | en-US:get it now <3 |
    Then I should get error that product available_now_labels is invalid
    When I update product "product1" stock with following information:
      | available_later_labels | en-US:too late bro<3 |
    Then I should get error that product available_later_labels is invalid
    And product "product1" should have following stock information:
      | quantity                      | 0          |
      | minimal_quantity              | 1          |
      | location                      |            |
      | low_stock_threshold           | 0          |
      | low_stock_alert               | false      |
      | available_date                | 0000-00-00 |
    And product "product1" localized "available_now_labels" should be "en-US:"
    And product "product1" localized "available_later_labels" should be "en-US:"
