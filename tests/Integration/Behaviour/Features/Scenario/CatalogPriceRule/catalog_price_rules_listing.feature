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
    Then I should be able to see following list of catalog price rules in product page for language "en":
      | catalog price rule reference | name             | currency   | country       | group    | from quantity  | reduction type| reduction value | shop     | includes tax| price | from                | to                  |
      | catalogPriceRuleReference1   | huge discount    | usd        | UnitedStates  | visitor  | 1              | amount        | 111.50          | testShop | true        | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalogPriceRuleReference1   | smaller discount | usd        | UnitedStates  | visitor  | 1              | amount        | 50              | testShop | true        | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |


