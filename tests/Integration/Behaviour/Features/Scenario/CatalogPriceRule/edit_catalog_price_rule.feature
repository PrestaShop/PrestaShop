#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s catalog_price_rule --tags edit-catalog-price-rule
@restore-all-tables-before-feature
@clear-cache-before-feature
@catalog-price-rule
@edit-catalog-price-rule

Feature: Edit catalog price rule date range in Back Office (BO)
  As an employee
  I must not be able to save a catalog price rule with an end date before its start date

  Background:
    Given language with iso code "en" is the default one
    And shop "testShop" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And country "UnitedStates" with iso code "US" exists
    And group "visitor" named "Visitor" exists
    And I add catalog price rule "rule1" with following details:
      | name            | December rule       |
      | currency        | usd                 |
      | country         | UnitedStates        |
      | group           | visitor             |
      | from quantity   | 1                   |
      | reduction type  | amount              |
      | reduction value | 10                  |
      | shop            | testShop            |
      | includes tax    | true                |
      | price           | 50                  |
      | from            | 2026-12-01 00:00:00 |
      | to              | 2026-12-31 00:00:00 |
    Then catalog price rule "rule1" should have following details:
      | catalog price rule detail | value               |
      | name                      | December rule       |
      | currency                  | usd                 |
      | country                   | UnitedStates        |
      | group                     | visitor             |
      | from quantity             | 1                   |
      | reduction type            | amount              |
      | reduction value           | 10                  |
      | shop                      | testShop            |
      | includes tax              | true                |
      | price                     | 50                  |
      | from                      | 2026-12-01 00:00:00 |
      | to                        | 2026-12-31 00:00:00 |

  Scenario: I cannot add a catalog price rule with an end date before its start date
    When I add catalog price rule "rule2" with following details:
      | name            | Inverse rule        |
      | currency        | usd                 |
      | country         | UnitedStates        |
      | group           | visitor             |
      | from quantity   | 1                   |
      | reduction type  | amount              |
      | reduction value | 10                  |
      | shop            | testShop            |
      | includes tax    | true                |
      | price           | 50                  |
      | from            | 2026-12-20 00:00:00 |
      | to              | 2026-12-10 00:00:00 |
    Then I should get error that catalog price rule date range is invalid

  Scenario: I can move both dates of a catalog price rule after its current end date
    When I edit catalog price rule "rule1" with following details:
      | from | 2027-02-01 00:00:00 |
      | to   | 2027-02-28 00:00:00 |
    Then catalog price rule "rule1" should have following details:
      | catalog price rule detail | value               |
      | name                      | December rule       |
      | currency                  | usd                 |
      | country                   | UnitedStates        |
      | group                     | visitor             |
      | from quantity             | 1                   |
      | reduction type            | amount              |
      | reduction value           | 10                  |
      | shop                      | testShop            |
      | includes tax              | true                |
      | price                     | 50                  |
      | from                      | 2027-02-01 00:00:00 |
      | to                        | 2027-02-28 00:00:00 |

  Scenario: I can edit only one date of a catalog price rule when the range stays valid
    When I edit catalog price rule "rule1" with following details:
      | from | 2026-12-10 00:00:00 |
    And I edit catalog price rule "rule1" with following details:
      | to | 2026-12-15 00:00:00 |
    Then catalog price rule "rule1" should have following details:
      | catalog price rule detail | value               |
      | name                      | December rule       |
      | currency                  | usd                 |
      | country                   | UnitedStates        |
      | group                     | visitor             |
      | from quantity             | 1                   |
      | reduction type            | amount              |
      | reduction value           | 10                  |
      | shop                      | testShop            |
      | includes tax              | true                |
      | price                     | 50                  |
      | from                      | 2026-12-10 00:00:00 |
      | to                        | 2026-12-15 00:00:00 |

  Scenario: I cannot edit a catalog price rule with an end date before its start date
    When I edit catalog price rule "rule1" with following details:
      | from | 2026-12-20 00:00:00 |
      | to   | 2026-12-10 00:00:00 |
    Then I should get error that catalog price rule date range is invalid
    When I edit catalog price rule "rule1" with following details:
      | to | 2026-11-15 00:00:00 |
    Then I should get error that catalog price rule date range is invalid
    When I edit catalog price rule "rule1" with following details:
      | from | 2027-01-15 00:00:00 |
    Then I should get error that catalog price rule date range is invalid
    And catalog price rule "rule1" should have following details:
      | catalog price rule detail | value               |
      | name                      | December rule       |
      | currency                  | usd                 |
      | country                   | UnitedStates        |
      | group                     | visitor             |
      | from quantity             | 1                   |
      | reduction type            | amount              |
      | reduction value           | 10                  |
      | shop                      | testShop            |
      | includes tax              | true                |
      | price                     | 50                  |
      | from                      | 2026-12-01 00:00:00 |
      | to                        | 2026-12-31 00:00:00 |
