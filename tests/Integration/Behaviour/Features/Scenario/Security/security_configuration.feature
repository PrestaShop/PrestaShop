# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s security
@restore-all-tables-before-feature
Feature: Security configuration form
  PrestaShop allows BO users to manage Security configuration
  As a BO user
  I must be able to edit the security configuration
  Clear the outdated sessions for customers and employees

  Background:
    Given there is a customer named "John Doe" whose email is "john.doe@prestashop.com"

  Scenario: Play with general configuration
    Given I specify following properties for security form
      | token            | 0 |
    And shop configuration for "PS_SECURITY_TOKEN" is set to 1
    Then the token configuration should be enabled
    When I submit the security form
    Then the security form is valid
    Then the token configuration should be disabled

  Scenario: Clear outdated customer sessions
    Given there is customer "John Doe" with email "john.doe@prestashop.com"
    And a session for customer named "John Doe" is created 1 hour ago
    And a session for customer named "John Doe" is created 10 hours ago
    # Means two hours
    And shop configuration for "PS_COOKIE_LIFETIME_FO" is set to 2
    Then there is 2 customer sessions left
    When I clear outdated customer sessions
    Then there is 1 customer session left

  Scenario: Clear outdated employee sessions
    Given a session for the employee is created 1 hour ago
    And a session for the employee is created 2 hour ago
    # these ones will be cleared
    And a session for the employee is created 5 hours ago
    And a session for the employee is created 10 hours ago
    # Means one hour
    And shop configuration for "PS_COOKIE_LIFETIME_BO" is set to 3
    Then there is 4 employee sessions left
    When I clear outdated employee sessions
    Then there is 2 employee session left
