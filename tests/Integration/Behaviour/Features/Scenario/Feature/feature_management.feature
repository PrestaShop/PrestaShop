# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s feature --tags feature-management
@reset-database-before-feature
@feature-management
Feature: Product feature management
  PrestaShop allows BO users to manage product features
  As a BO user
  I must be able to create, edit and delete product features

  Background:
    Given shop "shop1" with name "test_shop" exists

  Scenario: Create product feature
    When I create product feature "feature1" with specified properties:
      | name | My feature |
    Then product feature "feature1" name should be "My feature"

  Scenario: Update product feature name
    Given product feature with reference "feature1" exists
    When I update product feature with reference "feature1" field "name" in default language to "My great feature"
    Then product feature with reference "feature1" field "name" in default language should be "My great feature"

  Scenario: Updating product feature with empty name should not be allowed
    Given product feature with reference "feature1" exists
    When I update product feature with reference "feature1" field "name" in default language to ""
    Then I should get an error that feature name is invalid.

  Scenario: Creating product feature with empty name should not be allowed
    When I create product feature with empty name
    Then I should get an error that feature name is invalid.
