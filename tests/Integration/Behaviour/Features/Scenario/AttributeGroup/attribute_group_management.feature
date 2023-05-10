# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attribute group
@restore-all-tables-before-feature
@attribute-group-management
Feature: Attribute group management
  PrestaShop allows BO users to manage product attribute groups
  As a BO user
  I must be able to create, edit and delete attribute groups

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists

  Scenario: Adding new attribute group
    When I create attribute group "attributeGroup1" with specified properties:
      | name[en-US]        | Color          |
      | name[fr-FR]        | Couleur        |
      | public_name[en-US] | Public Color   |
      | public_name[fr-FR] | Public couleur |
      | type               | radio          |
    Then attribute group "attributeGroup1" should have the following properties:
      | name[en-US]        | Color          |
      | name[fr-FR]        | Couleur        |
      | public_name[en-US] | Public Color   |
      | public_name[fr-FR] | Public couleur |
      | type               | radio          |

  Scenario: Editing attribute group
    When I edit attribute group "attributeGroup1" with specified properties:
      | name[en-US]        | Coulor         |
      | name[fr-FR]        | Couleur        |
      | public_name[en-US] | Public Colour  |
      | public_name[fr-FR] | Public couleur |
      | type               | color          |
    Then attribute group "attributeGroup1" should have the following properties:
      | name[en-US]        | Coulor         |
      | name[fr-FR]        | Couleur        |
      | public_name[en-US] | Public Colour  |
      | public_name[fr-FR] | Public couleur |
      | type               | color          |

  Scenario: Deleting attribute group
    When I delete attribute group "attributeGroup1"
    Then attribute group "attributeGroup1" should be deleted
