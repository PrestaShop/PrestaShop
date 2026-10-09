@restore-all-tables-before-feature
@restore-all-tables-after-feature
#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s csp
Feature: Curate the CSP allow-list
  As a shop owner
  I want to promote and revoke CSP sources per shop
  So that my Content Security Policy allows exactly the sources I trust

  # Each scenario uses its own shop id so scenarios stay isolated within the feature. Rules are stored
  # by reference so they can be revoked without hardcoding integer ids.

  Scenario: Adding a rule stores it for the shop
    When I add a CSP rule "rule1" for shop 51 with directive "script-src" and source "https://cdn.example.com"
    Then a CSP rule for shop 51 with directive "script-src" and source "https://cdn.example.com" should exist
    And shop 51 should have 1 CSP rule

  Scenario: Adding a source that was never reported stores it on the allow-list without a log row
    When I add a CSP rule "rule1" for shop 62 with directive "script-src" and source "https://pre.example.com"
    Then shop 62 should have 1 CSP rule
    And a CSP rule for shop 62 with directive "script-src" and source "https://pre.example.com" should exist
    And the CSP log for shop 62 should be empty

  Scenario: Clearing the violation log leaves curated rules untouched
    When I add a CSP rule "rule1" for shop 63 with directive "script-src" and source "https://pre.example.com"
    And I clear the CSP log for shop 63
    Then shop 63 should have 1 CSP rule
    And the CSP log for shop 63 should be empty

  Scenario: The grid flags a broad scheme as weakening on script-src but not on img-src
    When I record a CSP violation for shop 64 with directive "script-src" and blocked source "data"
    And I record a CSP violation for shop 64 with directive "img-src" and blocked source "data"
    Then the CSP grid should flag directive "script-src" source "data:" for shop 64 as weakening
    And the CSP grid should flag directive "img-src" source "data:" for shop 64 as not weakening

  Scenario: Adding the same directive and source twice for a shop is rejected as a duplicate
    When I add a CSP rule "rule1" for shop 52 with directive "script-src" and source "https://cdn.example.com"
    And I add a CSP rule "rule2" for shop 52 with directive "script-src" and source "https://cdn.example.com"
    Then I should get a CSP error that the rule already exists
    And shop 52 should have 1 CSP rule

  Scenario: Allowing a recorded violation creates a rule, and allowing it again is idempotent
    When I record a CSP violation for shop 53 with directive "script-src" and blocked source "https://cdn.example.com"
    And I allow the recorded violation "script-src" from "https://cdn.example.com" for shop 53 as CSP rule "rule1"
    Then a CSP rule for shop 53 with directive "script-src" and source "https://cdn.example.com" should exist
    And shop 53 should have 1 CSP rule
    When I allow the recorded violation "script-src" from "https://cdn.example.com" for shop 53 as CSP rule "rule2"
    Then CSP rules "rule1" and "rule2" should be the same rule
    And shop 53 should have 1 CSP rule

  Scenario: Revoking a rule removes it
    When I add a CSP rule "rule1" for shop 54 with directive "script-src" and source "https://cdn.example.com"
    And I revoke CSP rule "rule1"
    Then no CSP rule for shop 54 with directive "script-src" and source "https://cdn.example.com" should exist
    And shop 54 should have no CSP rules

  Scenario: Bulk revoking several rules removes all of them
    When I add a CSP rule "rule1" for shop 55 with directive "script-src" and source "https://a.example.com"
    And I add a CSP rule "rule2" for shop 55 with directive "script-src" and source "https://b.example.com"
    And I add a CSP rule "rule3" for shop 55 with directive "style-src" and source "https://c.example.com"
    And I bulk revoke CSP rules "rule1,rule2,rule3"
    Then shop 55 should have no CSP rules

  Scenario: A rule added for one shop is not returned for another shop
    When I add a CSP rule "rule1" for shop 56 with directive "script-src" and source "https://cdn.example.com"
    Then a CSP rule for shop 56 with directive "script-src" and source "https://cdn.example.com" should exist
    And shop 57 should have no CSP rules
    And no CSP rule for shop 57 with directive "script-src" and source "https://cdn.example.com" should exist
    And the CSP rules for shop 57 should not include directive "script-src" and source "https://cdn.example.com"

  Scenario: Allowing a violation that belongs to another shop is rejected outside the current scope
    When I record a CSP violation for shop 58 with directive "script-src" and blocked source "https://cdn.example.com"
    And I try to allow the recorded violation "script-src" from "https://cdn.example.com" for shop 58 while scoped to shop 59
    Then I should get a CSP error that the log entry was not found
    And shop 58 should have no CSP rules
    And shop 59 should have no CSP rules

  Scenario: Revoking a rule that belongs to another shop is rejected outside the current scope
    When I add a CSP rule "rule1" for shop 60 with directive "script-src" and source "https://cdn.example.com"
    And I try to revoke CSP rule "rule1" while scoped to shop 61
    Then I should get a CSP error that the rule was not found
    And a CSP rule for shop 60 with directive "script-src" and source "https://cdn.example.com" should exist
