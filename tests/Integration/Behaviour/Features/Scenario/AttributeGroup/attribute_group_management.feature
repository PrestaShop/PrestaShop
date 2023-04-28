# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attribute_group
@restore-all-tables-before-feature
@feature-management
Feature: Attribute group management
  PrestaShop allows BO users to manage product attribute groups
  As a BO user
  I must be able to create, edit and delete product features

  Background:
    Given shop "shop1" with name "test_shop" exists

  Scenario: Adding new attribute group
    When I create attribute group "attributeGroup1" with specified properties:
      | name        | Color        |
      | public_name | Public color |
      | type        | radio        |
    Then attribute group "attributeGroup1" "name" in default language should be "Color"
    And attribute group "attributeGroup1" "public_name" in default language should be "Public color"
    And attribute group "attributeGroup1" "group_type" should be "radio"

  Scenario: Editing attribute group
    When I edit attribute group "attributeGroup1" with specified properties:
      | name        | Colour        |
      | public_name | Public colour |
      | type        | color         |
    Then attribute group "attributeGroup1" "name" in default language should be "Colour"
    And attribute group "attributeGroup1" "public_name" in default language should be "Public colour"
    And attribute group "attributeGroup1" "group_type" should be "color"

  Scenario: Deleting attribute group
    When I delete attribute group "attributeGroup1"
    Then attribute group "attributeGroup1" should be deleted
