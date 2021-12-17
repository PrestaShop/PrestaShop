# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s feature_flag --tags feature-flag
@restore-all-tables-before-feature
Feature: Feature Flag
  As a BO user
  I want to be able to try experimental features by toggling feature flags in the Back Office

  @feature-flag
  Scenario: Enable a feature flag
    Given I register a disabled feature flag "Security_BO_Page_1"
    Then the feature flag "Security_BO_Page_1" state is disabled
    When I enable feature flag "Security_BO_Page_1"
    Then the feature flag "Security_BO_Page_1" state is enabled

  @feature-flag
  Scenario: Disable a feature flag
    Given I register a enabled feature flag "Security_BO_Page_1"
    Then the feature flag "Security_BO_Page_1" state is enabled
    When I disable feature flag "Security_BO_Page_1"
    Then the feature flag "Security_BO_Page_1" state is disabled

  @feature-flag
  Scenario: Cannot register twice the same feature flag
    Given I register a enabled feature flag "Security_BO_Page"
    When I register a enabled feature flag "Security_BO_Page"
    Then I should be returned an error

  @feature-flag
  Scenario: Toggle multiple times a feature flag
    Given I register a disabled feature flag "Security_BO_Page_2"
    When I enable feature flag "Security_BO_Page_2"
    Then the feature flag "Security_BO_Page_2" state is enabled
    When I disable feature flag "Security_BO_Page_2"
    Then the feature flag "Security_BO_Page_2" state is disabled
    When I enable feature flag "Security_BO_Page_2"
    Then the feature flag "Security_BO_Page_2" state is enabled
