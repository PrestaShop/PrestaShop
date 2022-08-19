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

  Scenario: I can see a list of catalog price rules
    When I add catalog price rule "catalogPriceRuleReference1" with following details:
      | name            | huge discount |
      | currency        | usd           |
      | country         | UnitedStates  |
      | group           | visitor       |
      | from quantity   | 1             |
      | reduction type  | amount        |
      | reduction value | 111.50        |
      | shop            | testShop      |
      | includes tax    | true          |
      | price           | 50            |
    Then catalog price rule "catalogPriceRuleReference1" should have following details:
      | catalog price rule detail | value         |
      | name                      | huge discount |
      | currency                  | usd           |
      | country                   | UnitedStates  |
      | group                     | visitor       |
      | from quantity             | 1             |
      | reduction type            | amount        |
      | reduction value           | 111.50        |
      | shop                      | testShop      |
      | includes tax              | true          |
      | price                     | 50            |
    When I add catalog price rule "catalogPriceRuleReference2" with following details:
      | name            | smaller discount  |
      | currency        | usd               |
      | country         | UnitedStates      |
      | group           | visitor           |
      | from quantity   | 1                 |
      | reduction type  | amount            |
      | reduction value | 50                |
      | shop            | testShop          |
      | includes tax    | true              |
      | price           | 10                |
    Then catalog price rule "catalogPriceRuleReference2" should have following details:
      | catalog price rule detail | value            |
      | name                      | smaller discount |
      | currency                  | usd              |
      | country                   | UnitedStates     |
      | group                     | visitor          |
      | from quantity             | 1                |
      | reduction type            | amount           |
      | reduction value           | 50               |
      | shop                      | testShop         |
      | includes tax              | true             |
      | price                     | 10               |
    When I add catalog price rule "catalogPriceRuleReference3" with following details:
      | name            | some discount     |
      | currency        | usd               |
      | country         | UnitedStates      |
      | group           | visitor           |
      | from quantity   | 5                 |
      | reduction type  | amount            |
      | reduction value | 10                |
      | shop            | testShop          |
      | includes tax    | true              |
      | price           | 20                |
    Then catalog price rule "catalogPriceRuleReference3" should have following details:
      | catalog price rule detail | value            |
      | name                      | some discount    |
      | currency                  | usd              |
      | country                   | UnitedStates     |
      | group                     | visitor          |
      | from quantity             | 5                |
      | reduction type            | amount           |
      | reduction value           | 10               |
      | shop                      | testShop         |
      | includes tax              | true             |
      | price                     | 20               |
    Then I should be able to see following list of catalog price rules with language "en" with limit 2 offset 0 and total 3:
      | name             | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | huge discount    | usd        | UnitedStates  | visitor  | 1              | amount        | 111.50          | testShop | true        | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | smaller discount | usd        | UnitedStates  | visitor  | 1              | amount        | 50              | testShop | true        | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 3:
      | name             | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | huge discount    | usd        | UnitedStates  | visitor  | 1              | amount        | 111.50          | testShop | true        | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | smaller discount | usd        | UnitedStates  | visitor  | 1              | amount        | 50              | testShop | true        | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | some discount    | usd        | UnitedStates  | visitor  | 5              | amount        | 10              | testShop | true        | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 2 and total 3:
      | name             | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | some discount    | usd        | UnitedStates  | visitor  | 5              | amount        | 10              | testShop | true        | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |

