@restore-all-tables-before-feature
@restore-all-tables-after-feature
#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s csp
Feature: Collect CSP violation reports
  As a shop owner
  I want the CSP violations reported by browsers to be collected per shop
  So that I can curate my Content Security Policy from real data

  # Each scenario uses its own shop id so scenarios stay isolated within the feature. The recorder
  # treats id_shop as an opaque integer (no foreign key on ps_csp_log), so no shop fixture is needed
  # to exercise per-shop storage and isolation.

  Scenario: Recording a violation stores one row for the shop
    When I record a CSP violation for shop 11 with directive "script-src" and blocked source "https://cdn.example.com"
    Then the CSP log for shop 11 should contain 1 row
    And violation "script-src" from "https://cdn.example.com" for shop 11 should have 1 hit

  Scenario: Recording the same violation twice increments the hit counter instead of adding a row
    When I record a CSP violation for shop 12 with directive "script-src" and blocked source "https://cdn.example.com"
    And I record a CSP violation for shop 12 with directive "script-src" and blocked source "https://cdn.example.com"
    Then the CSP log for shop 12 should contain 1 row
    And violation "script-src" from "https://cdn.example.com" for shop 12 should have 2 hits

  Scenario: The same source reported on two different pages is kept as two rows
    When I record a CSP violation for shop 15 with directive "script-src" and blocked source "https://cdn.example.com" on page "https://shop.example.com/"
    And I record a CSP violation for shop 15 with directive "script-src" and blocked source "https://cdn.example.com" on page "https://shop.example.com/category/3-clothes"
    Then the CSP log for shop 15 should contain 2 rows

  Scenario: The same source on the same page increments the hit counter instead of adding a row
    When I record a CSP violation for shop 16 with directive "script-src" and blocked source "https://cdn.example.com" on page "https://shop.example.com/"
    And I record a CSP violation for shop 16 with directive "script-src" and blocked source "https://cdn.example.com" on page "https://shop.example.com/"
    Then the CSP log for shop 16 should contain 1 row

  Scenario: A junk browser-extension source is dropped and nothing is recorded
    When I record a CSP violation for shop 13 with directive "script-src" and blocked source "chrome-extension://abcdefghijklmnop/inject.js"
    Then the CSP log for shop 13 should be empty

  Scenario: An unknown directive is dropped and nothing is recorded
    When I record a CSP violation for shop 14 with directive "totally-not-a-directive" and blocked source "https://cdn.example.com"
    Then the CSP log for shop 14 should be empty

  Scenario: A violation recorded for one shop is not counted for another shop
    When I record a CSP violation for shop 21 with directive "script-src" and blocked source "https://a.example.com"
    Then the CSP log for shop 21 should contain 1 row
    And the CSP log for shop 22 should be empty
    And the CSP log for shop 22 should not contain violation "script-src" from "https://a.example.com"

  Scenario: Clearing the log for a shop empties only that shop's rows
    When I record a CSP violation for shop 31 with directive "script-src" and blocked source "https://a.example.com"
    And I record a CSP violation for shop 32 with directive "script-src" and blocked source "https://b.example.com"
    And I clear the CSP log for shop 31
    Then the CSP log for shop 31 should be empty
    And the CSP log for shop 32 should contain 1 row
    And violation "script-src" from "https://b.example.com" for shop 32 should have 1 hit

  Scenario: The per-shop row cap prunes the oldest rows beyond the cap
    When I record the following CSP violations for shop 41 through a recorder capped at 3:
      | directive  | source                 |
      | script-src | https://a1.example.com |
      | script-src | https://a2.example.com |
      | script-src | https://a3.example.com |
      | script-src | https://a4.example.com |
      | script-src | https://a5.example.com |
    Then the CSP log for shop 41 should contain 3 rows
    And the CSP log for shop 41 should not contain violation "script-src" from "https://a1.example.com"
    And the CSP log for shop 41 should not contain violation "script-src" from "https://a2.example.com"
    And violation "script-src" from "https://a5.example.com" for shop 41 should have 1 hit

  Scenario: The unreviewed count excludes sources that are already allow-listed
    When I record a CSP violation for shop 71 with directive "script-src" and blocked source "https://a.example.com"
    And I record a CSP violation for shop 71 with directive "script-src" and blocked source "https://b.example.com"
    And I add a CSP rule "r1" for shop 71 with directive "script-src" and source "https://a.example.com"
    Then the unreviewed CSP count for shop 71 should be 1

  Scenario: Back-office and storefront violations are kept in separate surfaces
    When I record a CSP violation for shop 61 with directive "script-src" and blocked source "https://front.example.com"
    And I record a back-office CSP violation with directive "script-src" and blocked source "https://admin.example.com"
    Then the CSP log for shop 61 should contain 1 row
    And the CSP log for shop 61 should not contain violation "script-src" from "https://admin.example.com"
    And the back-office CSP log should contain 1 row
    And the back-office CSP log should contain violation "script-src" from "https://admin.example.com"

  Scenario: Pruning old reports deletes stale rows, keeps recent ones, and never touches the allow-list
    When I record a CSP violation for shop 51 with directive "script-src" and blocked source "https://old.example.com"
    And I backdate the CSP log for shop 51 source "https://old.example.com" by 60 days
    And I record a CSP violation for shop 51 with directive "script-src" and blocked source "https://recent.example.com"
    And I add a CSP rule "rule1" for shop 51 with directive "script-src" and source "https://ruled.example.com"
    And I prune CSP reports for shop 51 older than 30 days
    Then shop 51 should have 1 CSP rule
    And the CSP log for shop 51 should contain 1 row
    And the CSP log for shop 51 should not contain violation "script-src" from "https://old.example.com"
    And violation "script-src" from "https://recent.example.com" for shop 51 should have 1 hit
