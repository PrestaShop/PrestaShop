# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s feature --tags feature-management
@restore-all-tables-before-feature
@feature-management
Feature: Product feature management
  PrestaShop allows BO users to manage product features
  As a BO user
  I must be able to create, edit and delete product features

  Background:
    Given shop "shop1" with name "test_shop" exists
    And single shop "shop1" context is loaded
    And language "en" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists

  Scenario: Create product feature in single shop
    When I create product feature "feature1" with specified properties:
      | name[en-US] | My feature en |
      | name[fr-FR] | My feature fr |
    Then product feature feature1 should have following details:
      | name[en-US]      | My feature en |
      | name[fr-FR]      | My feature fr |
      | associated shops | shop1         |

  Scenario: Update product feature
    Given I create product feature "feature2" with specified properties:
      | name[en-US] | My feature en |
      | name[fr-FR] | My feature fr |
    And product feature feature2 should have following details:
      | name[en-US]      | My feature en |
      | name[fr-FR]      | My feature fr |
      | associated shops | shop1         |
    When I update product feature feature2 reference with following details:
      | name[en-US] | My feature en updated1 |
      | name[fr-FR] | My feature fr updated1 |
    Then product feature feature2 should have following details:
      | name[en-US] | My feature en updated1 |
      | name[fr-FR] | My feature fr updated1 |

  Scenario: Creating and updating feature with empty shop association should assign the feature to current shop by default
    When I create product feature "feature3" with specified properties:
      | name[en-US]      | My feature en |
      | name[fr-FR]      | My feature fr |
      | associated shops |               |
    Then product feature feature3 should have following details:
      | name[en-US]      | My feature en |
      | name[fr-FR]      | My feature fr |
      | associated shops | shop1         |
    When I update product feature feature3 reference with following details:
      | associated shops |  |
    Then product feature feature3 should have following details:
      | name[en-US]      | My feature en |
      | name[fr-FR]      | My feature fr |
      | associated shops | shop1         |

  Scenario: Updating product feature with empty name in default language should not be allowed
    Given I create product feature "feature4" with specified properties:
      | name[en-US] | My feature en |
      | name[fr-FR] | My feature fr |
    And product feature feature4 should have following details:
      | name[en-US] | My feature en |
      | name[fr-FR] | My feature fr |
    When I update product feature feature4 reference with following details:
      | name[en-US] |               |
      | name[fr-FR] | My feature fr |
    Then I should get an error that feature name cannot be empty in default language
    And product feature feature4 should have following details:
      | name[en-US] | My feature en |
      | name[fr-FR] | My feature fr |

  Scenario: Creating product feature with empty name in default language should not be allowed
    When I create product feature "feature5" with specified properties:
      | name[en-US] |               |
      | name[fr-FR] | My feature fr |
    Then I should get an error that feature name cannot be empty in default language
    And product feature feature5 should not exist

  Scenario: Delete feature which has no values
    Given I create product feature feature6 with specified properties:
      | name[en-US] | My feature en |
      | name[fr-FR] |               |
    And product feature feature6 should have following details:
      | name[en-US] | My feature en |
      | name[fr-FR] |               |
    When I delete product feature feature6
    Then product feature feature6 should not exist

  Scenario: Deleting feature should also remove its values
    Given I create product feature "feature7" with specified properties:
      | name[en-US] | My feature 7    |
      | name[fr-FR] | My feature 7 fr |
    And product feature feature6 should have following details:
      | name[en-US] | My feature en |
      | name[fr-FR] |               |
    Then product feature feature7 should have following details:
      | name[en-US] | My feature 7    |
      | name[fr-FR] | My feature 7 fr |
    When I create feature value "featureValue7" for feature "feature7" with following properties:
      | value[en-US] | Earth |
      | value[fr-FR] | Terre |
    And feature value "featureValue3" localized value should be:
      | locale | value |
      | en-US  | Earth |
      | fr-FR  | Terre |
    When I delete product feature feature7
    Then product feature feature7 should not exist
    And feature value featureValue7 should not exist
