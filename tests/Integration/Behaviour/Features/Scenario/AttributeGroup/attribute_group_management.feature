# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attribute_group --tags attribute-group-management
@restore-all-tables-before-feature
@attribute-group-management
Feature: Attribute group management
  PrestaShop allows BO users to manage product attribute groups
  As a BO user
  I must be able to create, edit and delete attribute groups

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "en" with locale "en-US" exists
    And language "fr" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_shop2" and color "blue" for the group "default_shop_group"
    And I add a shop "shop3" with name "test_shop3" and color "red" for the group "default_shop_group"
    And I add a shop "shop4" with name "test_shop4" and color "red" for the group "default_shop_group"

  Scenario: Adding new attribute group
    When I create attribute group "attributeGroup1" with specified properties:
      | name[en-US]        | Color             |
      | name[fr-FR]        | Couleur           |
      | public_name[en-US] | Public Color      |
      | public_name[fr-FR] | Public couleur    |
      | type               | radio             |
      | shopIds            | shop1,shop2,shop4 |
    Then attribute group "attributeGroup1" should have the following properties:
      | name[en-US]        | Color             |
      | name[fr-FR]        | Couleur           |
      | public_name[en-US] | Public Color      |
      | public_name[fr-FR] | Public couleur    |
      | type               | radio             |
      | shopIds            | shop1,shop2,shop4 |

  Scenario: Editing attribute group
    When I edit attribute group "attributeGroup1" with specified properties:
      | name[en-US]        | Coulors            |
      | name[fr-FR]        | Couleurs           |
      | public_name[en-US] | Public Colours     |
      | public_name[fr-FR] | Couleurs publiques |
      | type               | color              |
      | shopIds            | shop3,shop4        |
    Then attribute group "attributeGroup1" should have the following properties:
      | name[en-US]        | Coulors            |
      | name[fr-FR]        | Couleurs           |
      | public_name[en-US] | Public Colours     |
      | public_name[fr-FR] | Couleurs publiques |
      | type               | color              |
      | shopIds            | shop3,shop4        |
    # Test partial update
    When I edit attribute group "attributeGroup1" with specified properties:
      | name[en-US] | Colors |
    Then attribute group "attributeGroup1" should have the following properties:
      | name[en-US] | Colors   |
      | name[fr-FR] | Couleurs |
    When I edit attribute group "attributeGroup1" with specified properties:
      | name[fr-FR] | Couleurs |
    Then attribute group "attributeGroup1" should have the following properties:
      | name[en-US] | Colors   |
      | name[fr-FR] | Couleurs |
    When I edit attribute group "attributeGroup1" with specified properties:
      | public_name[en-US] | Public Color |
    Then attribute group "attributeGroup1" should have the following properties:
      | public_name[en-US] | Public Color       |
      | public_name[fr-FR] | Couleurs publiques |
    When I edit attribute group "attributeGroup1" with specified properties:
      | public_name[fr-FR] | Public couleur |
    Then attribute group "attributeGroup1" should have the following properties:
      | public_name[en-US] | Public Color   |
      | public_name[fr-FR] | Public couleur |
    When I edit attribute group "attributeGroup1" with specified properties:
      | type | radio |
    Then attribute group "attributeGroup1" should have the following properties:
      | type | radio |
    When I edit attribute group "attributeGroup1" with specified properties:
      | shopIds | shop1,shop2,shop4 |
    Then attribute group "attributeGroup1" should have the following properties:
      | shopIds | shop1,shop2,shop4 |
    And attribute group "attributeGroup1" should have the following properties:
      | name[en-US]        | Colors            |
      | name[fr-FR]        | Couleurs          |
      | public_name[en-US] | Public Color      |
      | public_name[fr-FR] | Public couleur    |
      | type               | radio             |
      | shopIds            | shop1,shop2,shop4 |
    # Now test invalid values
    When I edit attribute group "attributeGroup1" with specified properties:
      | name[fr-FR] | Couleurs< |
    Then I should get an error that attribute group field name value is invalid
    When I edit attribute group "attributeGroup1" with specified properties:
      | public_name[fr-FR] | Couleurs< |
    Then I should get an error that attribute group field public_name value is invalid
    When I edit attribute group "attributeGroup1" with specified properties:
      | type | random |
    Then I should get an error that attribute group field type value is invalid
    # Values remain unchanged
    And attribute group "attributeGroup1" should have the following properties:
      | name[en-US]        | Colors            |
      | name[fr-FR]        | Couleurs          |
      | public_name[en-US] | Public Color      |
      | public_name[fr-FR] | Public couleur    |
      | type               | radio             |
      | shopIds            | shop1,shop2,shop4 |

  Scenario: Deleting attribute group
    When I delete attribute group "attributeGroup1"
    Then attribute group "attributeGroup1" should be deleted
