# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-stock
@reset-database-before-feature
@clear-cache-before-feature
@reboot-kernel-before-feature
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

  Scenario: I check default stock values
    When I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    Then product "product1" should have following stock information:
      | pack_stock_type               | default |
      | out_of_stock_type             | default |
      | quantity                      | 0       |
      | minimal_quantity              | 1       |
      | location                      |         |
      | low_stock_threshold           | 0       |
      | low_stock_alert               | false   |
      | available_date                |         |

  Scenario: I check default stock values for virtual product
    When I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | virtual       |
    Then product "product1" should have following stock information:
      | pack_stock_type               | default   |
      | out_of_stock_type             | available |
      | quantity                      | 0         |
      | minimal_quantity              | 1         |
      | location                      |           |
      | low_stock_threshold           | 0         |
      | low_stock_alert               | false     |
      | available_date                |           |

  Scenario: I update product pack stock type
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
    And pack "productPack1" should contain products with following quantities:
      | product  | quantity |
      | product2 | 5        |
    And product "productPack1" should have following stock information:
      | pack_stock_type | default |
    When I update product "productPack1" stock with following information:
      | pack_stock_type | pack_only |
    Then product "productPack1" should have following stock information:
      | pack_stock_type | pack_only |
    When I update product "productPack1" stock with following information:
      | pack_stock_type | products_only |
    Then product "productPack1" should have following stock information:
      | pack_stock_type | products_only |
    When I update product "productPack1" stock with following information:
      | pack_stock_type | both |
    Then product "productPack1" should have following stock information:
      | pack_stock_type | both |
    When I update product "productPack1" stock with following information:
      | pack_stock_type | invalid |
    Then I should get error that pack stock type is invalid
    And product "productPack1" should have following stock information:
      | pack_stock_type | both |

  Scenario: I update product out of stock
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have following stock information:
      | out_of_stock_type | default |
    When I update product "product1" stock with following information:
      | out_of_stock_type | available |
    Then product "product1" should have following stock information:
      | out_of_stock_type | available |
    When I update product "product1" stock with following information:
      | out_of_stock_type | not_available |
    Then product "product1" should have following stock information:
      | out_of_stock_type | not_available |
    When I update product "product1" stock with following information:
      | out_of_stock_type | default |
    Then product "product1" should have following stock information:
      | out_of_stock_type | default |
    When I update product "product1" stock with following information:
      | out_of_stock_type | invalid |
    Then I should get error that out of stock type is invalid

  Scenario: Virtual product is available out of stock by default
    Given I add product "product1" with following information:
      | name[en-US] | eBook   |
      | type        | virtual |
    Then product "product1" should have following stock information:
      | out_of_stock_type | available |

  Scenario: I update product quantity
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have following stock information:
      | quantity | 0 |
    And product "product1" should have no stock movements
    When I update product "product1" stock with following information:
      | delta_quantity | 51 |
    Then I should get no error
    And product "product1" should have following stock information:
      | quantity | 51 |
    And product "product1" last employees stock movements should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | 51             |
    And product "product1" last stock movement increased by 51
    When I update product "product1" stock with following information:
      | delta_quantity | -9 |
    Then I should get no error
    And product "product1" should have following stock information:
      | quantity | 42 |
    And product "product1" last employees stock movements should be:
      | first_name | last_name | delta_quantity |
      | Puff       | Daddy     | -9             |
      | Puff       | Daddy     | 51             |
    And product "product1" last stock movement decreased by 9

  Scenario: I update product simple stock fields
    Given language "french" with locale "fr-FR" exists
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have following stock information:
      | minimal_quantity    | 1     |
      | location            |       |
      | low_stock_threshold | 0     |
      | low_stock_alert     | false |
      | available_date      |       |
    And product "product1" localized "available_now_labels" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "product1" localized "available_later_labels" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    When I update product "product1" stock with following information:
      | minimal_quantity              | 12           |
      | location                      | dtc          |
      | low_stock_threshold           | 42           |
      | low_stock_alert               | true         |
      | available_now_labels[en-US]   | get it now   |
      | available_later_labels[en-US] | too late bro |
      | available_date                | 1969-07-16   |
    And product "product1" should have following stock information:
      | minimal_quantity    | 12         |
      | location            | dtc        |
      | low_stock_threshold | 42         |
      | low_stock_alert     | true       |
      | available_date      | 1969-07-16 |
    And product "product1" localized "available_now_labels" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "product1" localized "available_later_labels" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    When I update product "product1" stock with following information:
      | available_now_labels[en-US]   | get it now   |
      | available_now_labels[fr-FR]   |              |
      | available_later_labels[en-US] | too late bro |
      | available_later_labels[fr-FR] |              |
    Then product "product1" localized "available_now_labels" should be:
      | locale | value      |
      | en-US  | get it now |
      | fr-FR  |            |
    And product "product1" localized "available_later_labels" should be:
      | locale | value        |
      | en-US  | too late bro |
      | fr-FR  |              |
    When I update product "product1" stock with following information:
      | available_now_labels[en-US]   | get it now          |
      | available_now_labels[fr-FR]   | commande maintenant |
      | available_later_labels[en-US] | too late bro        |
      | available_later_labels[fr-FR] | trop tard mec       |
    Then product "product1" localized "available_now_labels" should be:
      | locale | value               |
      | en-US  | get it now          |
      | fr-FR  | commande maintenant |
    And product "product1" localized "available_later_labels" should be:
      | locale | value         |
      | en-US  | too late bro  |
      | fr-FR  | trop tard mec |

  Scenario: When I use invalid values update is not authorized
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    And product "product1" should have following stock information:
      | quantity            | 0     |
      | minimal_quantity    | 1     |
      | location            |       |
      | low_stock_threshold | 0     |
      | low_stock_alert     | false |
      | available_date      |       |
    And product "product1" localized "available_now_labels" should be:
      | locale | value |
      | en-US  |       |
    And product "product1" localized "available_later_labels" should be:
      | locale | value |
      | en-US  |       |
    When I update product "product1" stock with following information:
      | minimal_quantity | -1 |
    Then I should get error that product minimal_quantity is invalid
    When I update product product1 location with value of 300 symbols length
    Then I should get error that product stock location is invalid
    When I update product "product1" stock with following information:
      | available_now_labels[en-US] | get it now <3 |
    Then I should get error that product available_now_labels is invalid
    When I update product "product1" stock with following information:
      | available_later_labels[en-US] | too late bro<3 |
    Then I should get error that product available_later_labels is invalid
    And product "product1" should have following stock information:
      | quantity            | 0     |
      | minimal_quantity    | 1     |
      | location            |       |
      | low_stock_threshold | 0     |
      | low_stock_alert     | false |
      | available_date      |       |
    And product "product1" localized "available_now_labels" should be:
      | locale | value |
      | en-US  |       |
    And product "product1" localized "available_later_labels" should be:
      | locale | value |
      | en-US  |       |
