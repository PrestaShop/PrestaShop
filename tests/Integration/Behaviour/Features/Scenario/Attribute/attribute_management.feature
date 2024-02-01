# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attribute --tags attribute-management
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
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_shop2" and color "blue" for the group "default_shop_group"
    And I add a shop "shop3" with name "test_shop3" and color "red" for the group "default_shop_group"
    And I add a shop "shop4" with name "test_shop4" and color "red" for the group "default_shop_group"
    And I create attribute group "attributeGroup1" with specified properties:
      | name[en-US]        | Color            |
      | name[fr-FR]        | Couleur          |
      | public_name[en-US] | Public color     |
      | public_name[fr-FR] | Couleur publique |
      | type               | color            |
    And I create attribute group "attributeGroup2" with specified properties:
      | name[en-US]        | Color            |
      | name[fr-FR]        | Couleur          |
      | public_name[en-US] | Public color     |
      | public_name[fr-FR] | Couleur publique |
      | type               | color            |

  Scenario: Adding new attribute
    And I create attribute "attribute1" with specified properties:
      | attribute_group | attributeGroup1   |
      | name[en-US]     | Color             |
      | name[fr-FR]     | Couleur           |
      | color           | #44DB6A           |
      | shopIds         | shop1,shop2,shop4 |
    Then attribute "attribute1" should have the following properties:
      | attribute_group | attributeGroup1   |
      | name[en-US]     | Color             |
      | name[fr-FR]     | Couleur           |
      | color           | #44DB6A           |
      | shopIds         | shop1,shop2,shop4 |

  Scenario: Editing attribute
    When I edit attribute "attribute1" with specified properties:
      | attribute_group | attributeGroup2 |
      | name[en-US]     | Colores         |
      | name[fr-FR]     | Couleures       |
      | color           | #44DB6B         |
      | shopIds         | shop4           |
    Then attribute "attribute1" should have the following properties:
      | attribute_group | attributeGroup2 |
      | name[en-US]     | Colores         |
      | name[fr-FR]     | Couleures       |
      | color           | #44DB6B         |
      | shopIds         | shop4           |
    # Check partial updates
    When I edit attribute "attribute1" with specified properties:
      | attribute_group | attributeGroup1 |
    Then attribute "attribute1" should have the following properties:
      | attribute_group | attributeGroup1 |
    When I edit attribute "attribute1" with specified properties:
      | name[en-US] | Color |
    Then attribute "attribute1" should have the following properties:
      | name[en-US] | Color     |
      | name[fr-FR] | Couleures |
    When I edit attribute "attribute1" with specified properties:
      | name[fr-FR] | Couleur |
    Then attribute "attribute1" should have the following properties:
      | name[en-US] | Color   |
      | name[fr-FR] | Couleur |
    When I edit attribute "attribute1" with specified properties:
      | color | #44DBFF |
    Then attribute "attribute1" should have the following properties:
      | color | #44DBFF |
    When I edit attribute "attribute1" with specified properties:
      | shopIds | shop1,shop3 |
    Then attribute "attribute1" should have the following properties:
      | shopIds | shop1,shop3 |
    # Edit attribute with invalid color results with an exception and data is not modified
    When I edit attribute "attribute1" with specified properties:
      | color | wrong_color |
    Then I should get an error that color value is invalid
    And attribute "attribute1" should have the following properties:
      | attribute_group | attributeGroup1 |
      | name[en-US]     | Color           |
      | name[fr-FR]     | Couleur         |
      | color           | #44DBFF         |
      | shopIds         | shop1,shop3     |

  Scenario: Adding new attribute with invalid color
    When I create attribute "attribute2" with specified properties:
      | attribute_group | attributeGroup1   |
      | name[en-US]     | Color             |
      | name[fr-FR]     | Couleur           |
      | color           | wrong_color       |
      | shopIds         | shop1,shop2,shop4 |
    Then I should get an error that color value is invalid

  Scenario: Deleting attribute
    When I delete attribute "attribute1"
    Then attribute "attribute1" should be deleted
