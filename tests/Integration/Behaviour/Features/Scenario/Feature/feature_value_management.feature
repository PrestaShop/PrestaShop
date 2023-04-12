# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s feature --tags feature-value-management
@restore-all-tables-before-feature
@feature-value-management
Feature: Product feature value management
  PrestaShop allows BO users to manage product feature value
  As a BO user
  I must be able to create, edit and delete product feature values

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "en" with locale "en-US" exists
    And language "fr" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And I create product feature "element" with specified properties:
      | name[en-US]      | Nature Element |
      | associated shops | shop1          |
    And product feature "element" should have following details:
      | name[en-US] | Nature Element |
      | name[fr-FR] | Nature Element |

  Scenario: I create and edit feature value
    When I create feature value "fire" for feature "element" with following properties:
      | value[en-US] | Fire |
      | value[fr-FR] | Feu  |
    Then feature value "fire" localized value should be:
      | locale | value |
      | en-US  | Fire  |
      | fr-FR  | Feu   |
    When I edit feature value "fire" with following properties:
      | value[en-US] | Blue Fire |
      | value[fr-FR] | Feu Bleu  |
    Then feature value "fire" localized value should be:
      | locale | value     |
      | en-US  | Blue Fire |
      | fr-FR  | Feu Bleu  |

  Scenario: Creating product feature value with empty name should not be allowed
    When I create feature value "water" for feature "element" with following properties:
      | value[en-US] |  |
    Then I should get an error that feature value is invalid
    When I create feature value "water" for feature "element" with following properties:
      | value[en-US] | <invalid |
    Then I should get an error that feature value is invalid

  Scenario: Editing product feature value with empty name should not be allowed
    When I create feature value "earth" for feature "element" with following properties:
      | value[en-US] | Earth |
      | value[fr-FR] | Terre |
    Then feature value "earth" localized value should be:
      | locale | value |
      | en-US  | Earth |
      | fr-FR  | Terre |
    When I edit feature value "earth" with following properties:
      | value[en-US] |  |
    Then I should get an error that feature value is invalid
    When I edit feature value "earth" with following properties:
      | value[en-US] | <invalid |
    Then I should get an error that feature value is invalid

  Scenario: I can edit the feature associated to a feature value
    When I create feature value "earth" for feature "element" with following properties:
      | value[en-US] | Earth |
      | value[fr-FR] | Terre |
    Then feature value "earth" localized value should be:
      | locale | value |
      | en-US  | Earth |
      | fr-FR  | Terre |
    And feature value "earth" should be associated to feature "element"
    When I create product feature "planet" with specified properties:
      | name[en-US]      | Planet |
      | associated shops | shop1  |
    Then product feature "planet" should have following details:
      | name[en-US] | Planet |
      | name[fr-FR] | Planet |
    When I associate feature value "earth" to feature "planet"
    Then feature value "earth" should be associated to feature "planet"
