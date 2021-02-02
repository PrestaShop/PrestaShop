# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s feature --tags feature-value-management
@reset-database-before-feature
@feature-value-management
Feature: Product feature value management
  PrestaShop allows BO users to manage product feature value
  As a BO user
  I must be able to create, edit and delete product feature values

  Background:
    Given shop "shop1" with name "test_shop" exists
    When I create product feature "element" with specified properties:
      | name | Nature Element |
    Then product feature "element" name should be "Nature Element"
    Given language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists

  Scenario: I create feature value
    When I create feature value "fire" for feature "element" value with following properties:
      | name[en-US] | Fire |
      | name[fr-FR] | Feu  |
    Then feature value "fire" localized value should be:
      | locale | value |
      | en-US  | Fire  |
      | fr-FR  | Feu   |
