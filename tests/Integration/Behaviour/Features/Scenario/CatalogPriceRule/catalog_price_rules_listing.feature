#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags catalog-price-rule-listing
@restore-products-before-feature
@clear-cache-before-feature
@catalog-prices
@catalog-price-rule-listing

Feature: List catalog price rules for product in Back Office (BO)
  As an employee
  I need to be able to see all catalog price rules in product page

  Background:
    Given language with iso code "en" is the default one
    And shop "testShop" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And country "UnitedStates" with iso code "US" exists
    And group "visitor" named "Visitor" exists
    And category "home" in default language named "Home" exists
    And category "home" is the default one
    And category "women" in default language named "Women" exists
    And category "accessories" in default language named "Accessories" exists
    And manufacturer studioDesign named "Studio Design" exists

  Scenario: I can see a list of catalog price rules
    Given I add product "product1" with following information:
      | name[en-US] | dress       |
      | type        | standard    |
    Given I add product "product2" with following information:
      | name[en-US] | skirt       |
      | type        | standard    |
    And I add catalog price rule "catalogPriceRuleReference1" with following details:
      | name            | catalog price rule 1 |
      | currency        | usd                  |
      | country         | UnitedStates         |
      | group           | visitor              |
      | from quantity   | 1                    |
      | reduction type  | amount               |
      | reduction value | 111.50               |
      | shop            | testShop             |
      | includes tax    | true                 |
      | price           | 50                   |
    Then catalog price rule "catalogPriceRuleReference1" should have following details:
      | catalog price rule detail | value                |
      | name                      | catalog price rule 1 |
      | currency                  | usd                  |
      | country                   | UnitedStates         |
      | group                     | visitor              |
      | from quantity             | 1                    |
      | reduction type            | amount               |
      | reduction value           | 111.50               |
      | shop                      | testShop             |
      | includes tax              | true                 |
      | price                     | 50                   |
    When I add catalog price rule "catalogPriceRuleReference2" with following details:
      | name            | catalog price rule 2  |
      | currency        | usd                   |
      | country         | UnitedStates          |
      | group           | visitor               |
      | from quantity   | 1                     |
      | reduction type  | amount                |
      | reduction value | 50                    |
      | shop            | testShop              |
      | includes tax    | true                  |
      | price           | 10                    |
    Then catalog price rule "catalogPriceRuleReference2" should have following details:
      | catalog price rule detail | value                |
      | name                      | catalog price rule 2 |
      | currency                  | usd                  |
      | country                   | UnitedStates         |
      | group                     | visitor              |
      | from quantity             | 1                    |
      | reduction type            | amount               |
      | reduction value           | 50                   |
      | shop                      | testShop             |
      | includes tax              | true                 |
      | price                     | 10                   |
    When I add catalog price rule "catalogPriceRuleReference3" with following details:
      | name            | catalog price rule 3  |
      | currency        | usd                   |
      | country         | UnitedStates          |
      | group           | visitor               |
      | from quantity   | 5                     |
      | reduction type  | amount                |
      | reduction value | 10                    |
      | shop            | testShop              |
      | includes tax    | true                  |
      | price           | 20                    |
    Then catalog price rule "catalogPriceRuleReference3" should have following details:
      | catalog price rule detail | value                 |
      | name                      | catalog price rule 3  |
      | currency                  | usd                   |
      | country                   | UnitedStates          |
      | group                     | visitor               |
      | from quantity             | 5                     |
      | reduction type            | amount                |
      | reduction value           | 10                    |
      | shop                      | testShop              |
      | includes tax              | true                  |
      | price                     | 20                    |
    Then I should be able to see following list of catalog price rules with language "en" with limit 2 offset 0 and total 3 and product "product1":
      | name                  | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | catalog price rule 1  | usd        | UnitedStates  | visitor  | 1              | amount        | 111.50          | testShop | true        | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2  | usd        | UnitedStates  | visitor  | 1              | amount        | 50              | testShop | true        | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 3 and product "product1":
      | name                 | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | catalog price rule 1 | usd        | UnitedStates  | visitor  | 1              | amount        | 111.50          | testShop | true        | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd        | UnitedStates  | visitor  | 1              | amount        | 50              | testShop | true        | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd        | UnitedStates  | visitor  | 5              | amount        | 10              | testShop | true        | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 2 and total 3 and product "product1":
      | name                  | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | catalog price rule 3  | usd        | UnitedStates  | visitor  | 5              | amount        | 10              | testShop | true        | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |

  Scenario: I can see correctly filtered list by categories
    Then I add following conditions to catalog price rule "catalogPriceRuleReference1":
      | type     | value       |
      | category | women |
    When I assign product product1 to following categories:
      | categories       | [home, women] |
      | default category | women         |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 3 and product "product1":
      | name                 | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | catalog price rule 1 | usd        | UnitedStates  | visitor  | 1              | amount        | 111.50          | testShop | true        | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd        | UnitedStates  | visitor  | 1              | amount        | 50              | testShop | true        | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd        | UnitedStates  | visitor  | 5              | amount        | 10              | testShop | true        | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 2 and product "product2":
      | name                 | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | catalog price rule 2 | usd        | UnitedStates  | visitor  | 1              | amount        | 50              | testShop | true        | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd        | UnitedStates  | visitor  | 5              | amount        | 10              | testShop | true        | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |

  Scenario: I can see correctly filtered list by supplier
    Given I add new supplier supplier1 with following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | meta keywords[en-US]    | sup,1              |
      | shops                   | [testShop]         |
    And I associate suppliers to product "product1"
      | supplier  | product_supplier           |
      | supplier1 | product1Supplier1          |
    Then I add following conditions to catalog price rule "catalogPriceRuleReference2":
      | type     | value       |
      | supplier | supplier1   |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 3 and product "product1":
      | name                 | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | catalog price rule 1 | usd        | UnitedStates  | visitor  | 1              | amount        | 111.50          | testShop | true        | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd        | UnitedStates  | visitor  | 1              | amount        | 50              | testShop | true        | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd        | UnitedStates  | visitor  | 5              | amount        | 10              | testShop | true        | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 1 and product "product2":
      | name                 | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | catalog price rule 3 | usd        | UnitedStates  | visitor  | 5              | amount        | 10              | testShop | true        | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |


  Scenario: I can see correctly filtered list by manufacturer
    When I update product "product1" options with following values:
      | manufacturer        | studioDesign |
    Then I add following conditions to catalog price rule "catalogPriceRuleReference3":
      | type         | value        |
      | manufacturer | studioDesign |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 3 and product "product1":
      | name                 | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | catalog price rule 1 | usd        | UnitedStates  | visitor  | 1              | amount        | 111.50          | testShop | true        | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd        | UnitedStates  | visitor  | 1              | amount        | 50              | testShop | true        | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd        | UnitedStates  | visitor  | 5              | amount        | 10              | testShop | true        | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 0 and product "product2":
      | name                 | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
