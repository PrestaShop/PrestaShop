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
    And I create product feature "legs_count" with specified properties:
      | name[en-US]      | Legs count          |
      | name[fr-FR]      | les jambes comptent |
      | associated shops | shop1               |
    And product feature "legs_count" should have following details:
      | name[en-US] | Legs count          |
      | name[fr-FR] | les jambes comptent |

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

  Scenario: Delete feature value
    When I create feature value "3_legs" for feature "legs_count" with following properties:
      | value[en-US] | 3 |
      | value[fr-FR] | 3 |
    And I create feature value "4_legs" for feature "legs_count" with following properties:
      | value[en-US] | 4 |
      | value[fr-FR] | 4 |
    Then feature value "3_legs" localized value should be:
      | locale | value |
      | en-US  | 3     |
      | fr-FR  | 3     |
    And feature value "4_legs" localized value should be:
      | locale | value |
      | en-US  | 4     |
      | fr-FR  | 4     |
    When I delete feature value "3_legs"
    Then feature value "3_legs" should not exist
    When I delete feature value "4_legs"
    Then feature value "4_legs" should not exist

  Scenario: Bulk delete feature values
    Given I create feature value "3_legs" for feature "legs_count" with following properties:
      | value[en-US] | 3 |
      | value[fr-FR] | 3 |
    And I create feature value "4_legs" for feature "legs_count" with following properties:
      | value[en-US] | 4 |
      | value[fr-FR] | 4 |
    And feature value "3_legs" localized value should be:
      | locale | value |
      | en-US  | 3     |
      | fr-FR  | 3     |
    And feature value "4_legs" localized value should be:
      | locale | value |
      | en-US  | 4     |
      | fr-FR  | 4     |
    And I create feature value "5_legs" for feature "legs_count" with following properties:
      | value[en-US] | 5 |
      | value[fr-FR] | 5 |
    And feature value "5_legs" localized value should be:
      | locale | value |
      | en-US  | 5     |
      | fr-FR  | 5     |
    And I create feature value "no_element" for feature "element" with following properties:
      | value[en-US] | No element |
      | value[fr-FR] | Non        |
    And feature value "no_element" localized value should be:
      | locale | value      |
      | en-US  | No element |
      | fr-FR  | Non        |
    When I bulk delete feature values "3_legs,4_legs,no_element"
    Then feature value "3_legs" should not exist
    And feature value "4_legs" should not exist
    And feature value "no_element" should not exist
    And feature value "5_legs" localized value should be:
      | locale | value |
      | en-US  | 5     |
      | fr-FR  | 5     |
