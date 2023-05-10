# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attribute
@restore-all-tables-before-feature
@attribute-management
Feature: Attribute group management
  PrestaShop allows BO users to manage product attribute groups
  As a BO user
  I must be able to create, edit and delete product features

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "en" with locale "en-US" exists
    And language "fr" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And I create attribute group "attributeGroup1" with specified properties:
      | name        | Color        |
      | public_name | Public color |
      | type        | color        |
    And I create attribute group "attributeGroup2" with specified properties:
      | name        | Color2       |
      | public_name | Public color |
      | type        | color        |

  Scenario: Adding new attribute
    And I create attribute "attribute1" with specified properties:
      | attribute_group | attributeGroup1 |
      | value[en-US]    | Color           |
      | value[fr-FR]    | Couleur         |
      | color           | #44DB6A         |
    Then attribute "attribute1" should have the following properties:
      | attribute_group | attributeGroup1 |
      | value[en-US]    | Color           |
      | value[fr-FR]    | Couleur         |
      | color           | #44DB6A         |
  Scenario: Editing attribute
    When I edit attribute "attribute1" with specified properties:
      | attribute_group | attributeGroup2 |
      | value[en-US]    | Colores         |
      | value[fr-FR]    | Couleures       |
      | color           | #44DB6B         |
    Then attribute "attribute1" should have the following properties:
      | attribute_group | attributeGroup2 |
      | value[en-US]    | Colores         |
      | value[fr-FR]    | Couleures       |
      | color           | #44DB6B         |

  Scenario: Deleting attribute
    When I delete attribute "attribute1"
    Then attribute "attribute1" should be deleted
