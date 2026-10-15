#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s catalog_price_rule --tags catalog-price-rule-edit-conditions
@restore-all-tables-before-feature
@restore-products-before-feature
@clear-cache-before-feature
@catalog-price-rule
@catalog-price-rule-edit-conditions

Feature: Keep the conditions of a catalog price rule when it is edited in Back Office (BO)
  As an employee
  I need a catalog price rule to stay scoped to what I scoped it to when I edit anything else about it

  Background:
    Given language with iso code "en" is the default one
    And shop "testShop" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And country "UnitedStates" with iso code "US" exists
    And group "visitor" named "Visitor" exists
    And category "home" in default language named "Home" exists
    And category "home" is the default one
    And category "women" in default language named "Women" exists

  Scenario: Renaming a rule does not widen it to the whole catalogue
    Given I add product "inWomen" with following information:
      | name[en-US] | dress    |
      | type        | standard |
    And I add product "outsideWomen" with following information:
      | name[en-US] | mug      |
      | type        | standard |
    And I add catalog price rule "scopedRule" with following details:
      | name            | scoped rule  |
      | currency        | usd          |
      | country         | UnitedStates |
      | group           | visitor      |
      | from quantity   | 1            |
      | reduction type  | amount       |
      | reduction value | 10           |
      | shop            | testShop     |
      | includes tax    | true         |
      | price           | 50           |
    And I add following conditions to catalog price rule "scopedRule":
      | type     | value |
      | category | women |
    And I assign product inWomen to following categories:
      | categories       | [home, women] |
      | default category | women         |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 1 and product "inWomen":
      | name        | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | scoped rule | usd      | UnitedStates | visitor | 1             | amount         | 10              | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And "outsideWomen" should have no catalog price rules with language "en"
    When I edit catalog price rule "scopedRule" with following details:
      | name | scoped rule renamed |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 1 and product "inWomen":
      | name                | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | scoped rule renamed | usd      | UnitedStates | visitor | 1             | amount         | 10              | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And "outsideWomen" should have no catalog price rules with language "en"
    And I delete catalog price rule "scopedRule"
