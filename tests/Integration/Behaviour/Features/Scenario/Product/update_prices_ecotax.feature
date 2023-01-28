# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-prices-ecotax
@restore-products-before-feature
@update-prices-ecotax
Feature: Update product price fields from Back Office (BO).
  As a BO user I want to be able to update product fields associated with price.

  Background:
    Given I add product "ecoProduct" with following information:
      | name[en-US] | washing machine |
      | type        | standard        |
    And product ecoProduct should have following prices information:
      | price              | 0     |
      | price_tax_included | 0     |
      | ecotax             | 0     |
      | tax rules group    |       |
      | on_sale            | false |
      | wholesale_price    | 0     |
      | unit_price         | 0     |
      | unit_price_ratio   | 0     |
      | unity              |       |
    And shop configuration for "PS_USE_ECOTAX" is set to 1
    And I identify tax rules group named "US-AL Rate (4%)" as "us-al-tax-rate"
    And I identify tax rules group named "US-KS Rate (5.3%)" as "us-ks-tax-rate"

  Scenario: I set ecotax value on a product it should impact its price tax included (no tax for ecotax)
    When I update product "ecoProduct" with following values:
      | price              | 51.42           |
      | ecotax             | 8.56            |
      | tax rules group    | US-AL Rate (4%) |
    Then product ecoProduct should have following prices information:
      | price                   | 51.42   |
      # (51.42 + 4% = 53.4768) + (8.56 + 0%)
      | price_tax_included      | 62.0368 |
      | ecotax                  | 8.56    |
      | ecotax_tax_included     | 8.56    |
    And shop configuration for "PS_ECOTAX_TAX_RULES_GROUP_ID" is set to us-ks-tax-rate
    Then product ecoProduct should have following prices information:
      | price                   | 51.42   |
      # (51.42 + 4% = 53.4768) + (8.56 + 5.3%)
      | price_tax_included      | 62.49048 |
      | ecotax                  | 8.56     |
      | ecotax_tax_included     | 9.01368  |

  Scenario: I set ecotax value on a product with price tax excluded still zero and I update unit price
    Given shop configuration for "PS_ECOTAX_TAX_RULES_GROUP_ID" is set to none
    Given I add product "ecoUnitProduct" with following information:
      | name[en-US] | beer machine |
      | type        | standard     |
    When I update product "ecoUnitProduct" with following values:
      | price              | 0.00           |
      | ecotax             | 1.00           |
    Then product ecoUnitProduct should have following prices information:
      | price               | 0.00 |
      # (0.00 + 4% = 0.00) + (1.00 + 0%)
      | price_tax_included  | 1.00 |
      | ecotax              | 1.00 |
      | ecotax_tax_included | 1.00 |
      | unit_price          | 0.0  |
      | unit_price_ratio    | 0.0  |
    And shop configuration for "PS_ECOTAX_TAX_RULES_GROUP_ID" is set to us-ks-tax-rate
    Then product ecoUnitProduct should have following prices information:
      | price               | 0.00  |
      # (0.00 + 4% = 0.00) + (1.00 + 5.3%)
      | price_tax_included  | 1.053 |
      | ecotax              | 1.00  |
      | ecotax_tax_included | 1.053 |
      | unit_price          | 0.0   |
      | unit_price_ratio    | 0.0   |
    When I update product "ecoUnitProduct" with following values:
      | unit_price | 10 |
    Then product ecoUnitProduct should have following prices information:
      | price               | 0.00  |
      # (0.00 + 4% = 0.00) + (1.00 + 0%)
      | price_tax_included  | 1.053 |
      | ecotax              | 1.00  |
      | ecotax_tax_included | 1.053 |
      | unit_price          | 10.0  |
      | unit_price_ratio    | 0.1   |

  Scenario: I set ecotax value on a product but the tax is disabled, the ecotax should be applied but without taxes
    When I update product "ecoProduct" with following values:
      | price              | 51.42           |
      | ecotax             | 8.56            |
      | tax rules group    | US-AL Rate (4%) |
    Then product ecoProduct should have following prices information:
      | price                   | 51.42   |
      # (51.42 + 4% = 53.4768) + (8.56 + 0%)
      | price_tax_included      | 62.0368 |
      | ecotax                  | 8.56    |
      | ecotax_tax_included     | 8.56    |
    And shop configuration for "PS_ECOTAX_TAX_RULES_GROUP_ID" is set to us-ks-tax-rate
    Then product ecoProduct should have following prices information:
      | price                   | 51.42   |
      # (51.42 + 4% = 53.4768) + (8.56 + 5.3%)
      | price_tax_included      | 62.49048 |
      | ecotax                  | 8.56     |
      | ecotax_tax_included     | 9.01368  |
    # Disable tax feature
    And shop configuration for "PS_TAX" is set to 0
    Then product ecoProduct should have following prices information:
      | price                   | 51.42 |
      # (51.42 + 0% = 51.42) + (8.56 + 0%)
      | price_tax_included      | 59.98 |
      | ecotax                  | 8.56  |
      | ecotax_tax_included     | 8.56  |
