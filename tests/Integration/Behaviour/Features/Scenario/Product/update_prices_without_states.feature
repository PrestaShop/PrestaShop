# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-prices-without-states
@restore-products-before-feature
@restore-taxes-after-feature
@reboot-kernel-before-feature
@clear-cache-before-feature
@update-prices-without-states
@update-product-prices
@reboot-kernel-after-feature
@restore-all-tables-after-feature
Feature: Update product price fields from Back Office (BO) when default country has no states.
  As a BO user I want to be able to update product fields associated with price.

  Background:
    Given shop configuration for default shop is set to fr
    And I add product "product1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And product product1 should have following prices information:
      | price              | 0     |
      | price_tax_included | 0     |
      | ecotax             | 0     |
      | tax rules group    |       |
      | on_sale            | false |
      | wholesale_price    | 0     |
      | unit_price         | 0     |
      | unit_price_ratio   | 0     |
      | unity              |       |
    And I add new tax "fr-tax-6" with following properties:
      | name         | FR Tax (6%)   |
      | rate         | 6             |
      | is_enabled   | true          |
    And I add the tax rule group "fr-tax-6-group" for the tax "fr-tax-6" with the following conditions:
      | name         | FR Tax (6%)   |
      | country      | FR            |

  Scenario: I update product prices
    And tax rules group named "FR Tax (6%)" exists
    And product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    When I update product "product1" with following values:
      | price              | 100.99          |
      | ecotax             | 0               |
      | tax rules group    | FR Tax (6%)     |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 900             |
      | unity              | bag of ten      |
    Then product product1 should have following prices information:
      | price                   | 100.99          |
      | price_tax_included      | 107.0494        |
      | ecotax                  | 0               |
      | tax rules group         | FR Tax (6%)     |
      | on_sale                 | true            |
      | wholesale_price         | 70              |
      | unit_price              | 900             |
      | unit_price_tax_included | 954             |
      | unit_price_ratio        | 0.112211        |
      | unity                   | bag of ten      |

  Scenario: I update product prices with negative values
    Given I update product "product1" with following values:
      | price           | 50              |
      | ecotax          | 3               |
      | tax rules group | FR Tax (6%)     |
      | on_sale         | true            |
      | wholesale_price | 10              |
      | unit_price      | 500             |
      | unity           | bag of ten      |
    And product product1 should have following prices information:
      | price                   | 50              |
      | price_tax_included      | 53              |
      | ecotax                  | 3               |
      | tax rules group         | FR Tax (6%)     |
      | on_sale                 | true            |
      | wholesale_price         | 10              |
      | unit_price              | 500             |
      | unit_price_tax_included | 530             |
      | unit_price_ratio        | 0.1             |
      | unity                   | bag of ten      |
    When I update product "product1" with following values:
      | price | -20 |
    Then I should get error that product "price" is invalid
    When I update product "product1" with following values:
      | ecotax | -2 |
    Then I should get error that product "ecotax" is invalid
    When I update product "product1" with following values:
      | wholesale_price | -35 |
    Then I should get error that product "wholesale_price" is invalid
    When I update product "product1" with following values:
      | unit_price | -300 |
    Then I should get error that product "unit_price" is invalid
    And product product1 should have following prices information:
      | price                   | 50              |
      | price_tax_included      | 53              |
      | ecotax                  | 3               |
      | tax rules group         | FR Tax (6%)     |
      | on_sale                 | true            |
      | wholesale_price         | 10              |
      | unit_price              | 500             |
      | unit_price_tax_included | 530             |
      | unit_price_ratio        | 0.1             |
      | unity                   | bag of ten      |

  Scenario: I update product tax the price tax included and unit price tax included is impacted
    When I update product "product1" with following values:
      | price      | 20  |
      | unit_price | 500 |
    Then product product1 should have following prices information:
      | price                   | 20   |
      | price_tax_included      | 20   |
      | unit_price              | 500  |
      | unit_price_tax_included | 500  |
      | unit_price_ratio        | 0.04 |
      | tax rules group         |      |
    When I update product "product1" with following values:
      | tax rules group | FR Tax (6%)     |
    Then product product1 should have following prices information:
      | price                   | 20              |
      | price_tax_included      | 21.2            |
      | unit_price              | 500             |
      | unit_price_tax_included | 530             |
      | unit_price_ratio        | 0.04            |
      | tax rules group         | FR Tax (6%)     |
