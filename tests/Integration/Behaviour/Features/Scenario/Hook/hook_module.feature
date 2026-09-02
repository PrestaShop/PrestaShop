# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s hook-module --tags hook-module
@restore-all-tables-before-feature
@clear-cache-before-feature
@clear-cache-before-scenario
@hook-module
Feature: Hook a module
  PrestaShop allows BO users to register a module on a hook with optional exception pages.
  As a BO user
  I must be able to hook a module, edit its exceptions, move it to another hook, and handle errors.

  Background:
    Given the module "ps_emailsubscription" is installed
    And the hook "displayFooterBefore" exists
    And the hook "actionCustomerAccountAdd" exists
    And the hook "displayHome" exists
    And the module "ps_emailsubscription" is not registered on any hook

  Scenario: Hook a module successfully
    When I hook module "ps_emailsubscription" to hook "displayFooterBefore" with no exceptions
    Then the module "ps_emailsubscription" should be registered on hook "displayFooterBefore"

  Scenario: Hook a module with exception pages
    When I hook module "ps_emailsubscription" to hook "actionCustomerAccountAdd" with exceptions "product, category"
    Then the module "ps_emailsubscription" should be registered on hook "actionCustomerAccountAdd"
    And the module "ps_emailsubscription" exceptions on hook "actionCustomerAccountAdd" should be "product, category"

  Scenario: Cannot hook a module that is already registered on the same hook
    Given the module "ps_emailsubscription" is hooked to "displayFooterBefore"
    When I hook module "ps_emailsubscription" to hook "displayFooterBefore" with no exceptions
    Then I should get an error that the module is already hooked

  Scenario: Cannot hook a module to a hook it does not implement
    When I hook module "ps_emailsubscription" to hook "displayHome" with no exceptions
    Then I should get an error that the module cannot be hooked

  Scenario: Cannot hook a module to a non-existing hook
    When I hook module "ps_emailsubscription" to a non-existing hook
    Then I should get an error that the hook was not found

  Scenario: Cannot hook a non-existing module
    When I hook a non-existing module to hook "displayFooterBefore"
    Then I should get an error that the hook update failed

  Scenario: Cannot hook a module with an invalid exception filename
    When I hook module "ps_emailsubscription" to hook "displayFooterBefore" with exceptions "in valid name.php"
    Then I should get an error that the hook update failed

  Scenario: Edit exception pages for a hooked module
    Given the module "ps_emailsubscription" is hooked to "displayFooterBefore"
    When I edit module "ps_emailsubscription" on hook "displayFooterBefore" setting exceptions to "product, category"
    Then the module "ps_emailsubscription" exceptions on hook "displayFooterBefore" should be "product, category"

  Scenario: Clear exception pages for a hooked module
    Given the module "ps_emailsubscription" is hooked to "displayFooterBefore" with exceptions "product"
    When I edit module "ps_emailsubscription" on hook "displayFooterBefore" setting exceptions to ""
    Then the module "ps_emailsubscription" exceptions on hook "displayFooterBefore" should be ""

  Scenario: Move a hooked module to a different hook
    Given the module "ps_emailsubscription" is hooked to "displayFooterBefore"
    When I edit module "ps_emailsubscription" moving it from hook "displayFooterBefore" to hook "actionCustomerAccountAdd" with no exceptions
    Then the module "ps_emailsubscription" should be registered on hook "actionCustomerAccountAdd"
    And the module "ps_emailsubscription" should not be registered on hook "displayFooterBefore"

  Scenario: List possible hooks for a module reflects registration state
    Given the module "ps_emailsubscription" is hooked to "displayFooterBefore"
    Then the list of possible hooks for module "ps_emailsubscription" should contain "displayFooterBefore" as registered
    And the list of possible hooks for module "ps_emailsubscription" should contain "actionCustomerAccountAdd" as not registered
