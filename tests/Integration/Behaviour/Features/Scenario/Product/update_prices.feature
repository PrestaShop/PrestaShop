# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-prices
@restore-products-before-feature
@restore-taxes-after-feature
@update-product-prices
@update-prices
Feature: Update product price fields from Back Office (BO) when default country has states.
  As a BO user I want to be able to update product fields associated with price.

  Background:
    Given I add product "product1" with following information:
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

  Scenario: I update product prices
    And tax rules group named "US-AL Rate (4%)" exists
    And product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    When I update product "product1" with following values:
      | price              | 100.99          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 900             |
      | unity              | bag of ten      |
    Then product product1 should have following prices information:
      | price                   | 100.99          |
      | price_tax_included      | 105.0296        |
      | ecotax                  | 0               |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | true            |
      | wholesale_price         | 70              |
      | unit_price              | 900             |
      | unit_price_tax_included | 936             |
      | unit_price_ratio        | 0.112211        |
      | unity                   | bag of ten      |

  Scenario: I partially update product prices, providing only those values which I want to update
    Given I update product "product1" with following values:
      | price              | 100.99          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 900             |
      | unity              | bag of ten      |
    Given product product1 should have following prices information:
      | price                   | 100.99          |
      | price_tax_included      | 105.0296        |
      | ecotax                  | 0               |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | true            |
      | wholesale_price         | 70              |
      | unit_price              | 900             |
      | unit_price_tax_included | 936             |
      | unit_price_ratio        | 0.112211        |
      | unity                   | bag of ten      |
    When I update product "product1" with following values:
      | price | 200 |
    Then product product1 should have following prices information:
      | price                   | 200             |
      | price_tax_included      | 208             |
      | ecotax                  | 0               |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | true            |
      | wholesale_price         | 70              |
      | unit_price              | 900             |
      | unit_price_tax_included | 936             |
      | unit_price_ratio        | 0.222222        |
      | unity                   | bag of ten      |
    When I update product "product1" with following values:
      | ecotax  | 5.5   |
      | on_sale | false |
    Then product product1 should have following prices information:
      | price                   | 200             |
      | price_tax_included      | 208             |
      | ecotax                  | 5.5             |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | false           |
      | wholesale_price         | 70              |
      | unit_price              | 900             |
      | unit_price_tax_included | 936             |
      | unit_price_ratio        | 0.222222        |
      | unity                   | bag of ten      |

  Scenario: I update product prices with negative values
    Given I update product "product1" with following values:
      | price           | 50              |
      | ecotax          | 3               |
      | tax rules group | US-AL Rate (4%) |
      | on_sale         | true            |
      | wholesale_price | 10              |
      | unit_price      | 500             |
      | unity           | bag of ten      |
    And product product1 should have following prices information:
      | price                   | 50              |
      | price_tax_included      | 52              |
      | ecotax                  | 3               |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | true            |
      | wholesale_price         | 10              |
      | unit_price              | 500             |
      | unit_price_tax_included | 520             |
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
      | price_tax_included      | 52              |
      | ecotax                  | 3               |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | true            |
      | wholesale_price         | 10              |
      | unit_price              | 500             |
      | unit_price_tax_included | 520             |
      | unit_price_ratio        | 0.1             |
      | unity                   | bag of ten      |

  Scenario: I update product unit price when product price is 0
    When I update product "product1" with following values:
      | price      | 200 |
      | unit_price | 300 |
    And product product1 should have following prices information:
      | price                   | 200      |
      | price_tax_included      | 200      |
      | ecotax                  | 0        |
      | tax rules group         |          |
      | on_sale                 | false    |
      | wholesale_price         | 0        |
      | unit_price              | 300      |
      | unit_price_tax_included | 300      |
      | unit_price_ratio        | 0.666666 |
      | unity                   |          |
    When I update product "product1" with following values:
      | price      | 0 |
    Then product product1 should have following prices information:
      | price                   | 0     |
      | price_tax_included      | 0     |
      | ecotax                  | 0     |
      | tax rules group         |       |
      | on_sale                 | false |
      | wholesale_price         | 0     |
      | unit_price              | 0     |
      | unit_price_tax_included | 0     |
      | unit_price_ratio        | 0     |
      | unity                   |       |
    # Even if you try to update unit price it remains zero because price is still zero
    When I update product "product1" with following values:
      | unit_price | 300 |
    Then product product1 should have following prices information:
      | price                   | 0     |
      | price_tax_included      | 0     |
      | ecotax                  | 0     |
      | tax rules group         |       |
      | on_sale                 | false |
      | wholesale_price         | 0     |
      | unit_price              | 0     |
      | unit_price_tax_included | 0     |
      | unit_price_ratio        | 0     |
      | unity                   |       |

  Scenario: I update product unit price when product price is 0 even if unit price was being modified
    When I update product "product1" with following values:
      | price      | 200 |
      | unit_price | 300 |
    And product product1 should have following prices information:
      | price                   | 200      |
      | price_tax_included      | 200      |
      | ecotax                  | 0        |
      | tax rules group         |          |
      | on_sale                 | false    |
      | wholesale_price         | 0        |
      | unit_price              | 300      |
      | unit_price_tax_included | 300      |
      | unit_price_ratio        | 0.666666 |
      | unity                   |          |
    When I update product "product1" with following values:
      | price      | 0   |
      | unit_price | 300 |
    And product product1 should have following prices information:
      | price                   | 0     |
      | price_tax_included      | 0     |
      | ecotax                  | 0     |
      | tax rules group         |       |
      | on_sale                 | false |
      | wholesale_price         | 0     |
      | unit_price              | 0     |
      | unit_price_tax_included | 0     |
      | unit_price_ratio        | 0     |
      | unity                   |       |

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
      | tax rules group | US-AL Rate (4%) |
    Then product product1 should have following prices information:
      | price                   | 20              |
      | price_tax_included      | 20.80           |
      | unit_price              | 500             |
      | unit_price_tax_included | 520             |
      | unit_price_ratio        | 0.04            |
      | tax rules group         | US-AL Rate (4%) |
    When I update product "product1" with following values:
      | tax rules group | US-FL Rate (6%) |
    Then product product1 should have following prices information:
      | price                   | 20              |
      | price_tax_included      | 21.20           |
      | unit_price              | 500             |
      | unit_price_tax_included | 530             |
      | unit_price_ratio        | 0.04            |
      | tax rules group         | US-FL Rate (6%) |

  Scenario: I update product prices providing non-existing tax rules group
    Given I update product "product1" with following values:
      | tax rules group | US-AL Rate (4%) |
    And product product1 should have following prices information:
      | tax rules group | US-AL Rate (4%) |
    When I update product "product1" prices and apply non-existing tax rules group
    Then I should get error that tax rules group does not exist
    And product product1 should have following prices information:
      | tax rules group | US-AL Rate (4%) |

  Scenario: When tax feature is disabled the price tax included has the same value as price tax excluded
    Given shop configuration for "PS_TAX" is set to 0
    And tax rules group named "US-AL Rate (4%)" exists
    And product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    When I update product "product1" with following values:
      | price              | 100.99          |
      | ecotax             | 0               |
      | tax rules group    | US-AL Rate (4%) |
      | on_sale            | true            |
      | wholesale_price    | 70              |
      | unit_price         | 900             |
      | unity              | bag of ten      |
    Then product product1 should have following prices information:
      | price                   | 100.99          |
      | price_tax_included      | 100.99          |
      | ecotax                  | 0               |
      | tax rules group         | US-AL Rate (4%) |
      | on_sale                 | true            |
      | wholesale_price         | 70              |
      | unit_price              | 900             |
      | unit_price_tax_included | 900             |
      | unit_price_ratio        | 0.112211        |
      | unity                   | bag of ten      |
    # Reset configuration to its initial value
    Then shop configuration for "PS_TAX" is set to 1

  Scenario: When first tax rule for specific state is deleted the tax rule group takes the next one
    And I add new tax "us-tax-state-1" with following properties:
      | name       | US Tax (6%) |
      | rate       | 6           |
      | is_enabled | true        |
    And I add new tax "us-tax-state-2" with following properties:
      | name       | US Tax (5%) |
      | rate       | 5           |
      | is_enabled | true        |
    And I add the tax rule group "us-tax-group-multiple-states" for the tax "us-tax-state-1" with the following conditions:
      | name    | US Tax group |
      | country | US           |
      | state   | AK           |
    And I add the tax rule "us-tax-state-2" for tax rule group "us-tax-group-multiple-states":
      | name    | US Tax (5%) |
      | country | US          |
      | state   | AR          |
    And product product1 should not have any suppliers assigned
    And product product1 should not have a default supplier
    When I update product "product1" with following values:
      | price           | 100.99       |
      | ecotax          | 0            |
      | tax rules group | US Tax group |
      | on_sale         | true         |
      | wholesale_price | 70           |
      | unit_price      | 900          |
      | unity           | bag of ten   |
    Then product product1 should have following prices information:
      | price                   | 100.99       |
      | price_tax_included      | 107.0494     |
      | ecotax                  | 0            |
      | tax rules group         | US Tax group |
      | on_sale                 | true         |
      | wholesale_price         | 70           |
      | unit_price              | 900          |
      | unit_price_tax_included | 954          |
      | unit_price_ratio        | 0.112211     |
      | unity                   | bag of ten   |
    When I delete tax rules that has tax "us-tax-state-1":
    Then product product1 should have following prices information:
      | price                   | 100.99       |
      | price_tax_included      | 106.0395     |
      | ecotax                  | 0            |
      | tax rules group         | US Tax group |
      | on_sale                 | true         |
      | wholesale_price         | 70           |
      | unit_price              | 900          |
      | unit_price_tax_included | 945          |
      | unit_price_ratio        | 0.112211     |
      | unity                   | bag of ten   |
