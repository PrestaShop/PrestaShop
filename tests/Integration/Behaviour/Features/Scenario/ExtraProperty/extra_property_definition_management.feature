# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s extra-property-definition
@restore-extra-property-definition-before-feature
@remove-extra-tables-after-feature
@clear-cache-before-feature
@clear-cache-after-feature
Feature: Extra property definition management
  PrestaShop allows BO users to manage the registry of "extra property" definitions
  As a BO user
  I must be able to create, edit and delete (single and bulk) extra property
  definitions, and module-owned definitions must remain read-only from the BO

  Scenario: Add a core extra property definition
    When I add an extra property definition "ep1" with following properties:
      | entity_name   | product       |
      | property_name | internal_code |
      | type          | string        |
      | scope         | common        |
      | display_front | true          |
    Then extra property definition "ep1" should still exist
    And extra property definition "ep1" should have the following parameters:
      | entity_name   | product       |
      | property_name | internal_code |
      | type          | string        |
      | scope         | common        |
      | display_front | true          |

  Scenario: Adding an extra property definition with the same entity/property under a different scope is rejected
    When I add an extra property definition "ep2" with following properties:
      | entity_name   | product        |
      | property_name | conflict_field |
      | type          | string         |
      | scope         | common         |
    Then extra property definition "ep2" should still exist
    When I add an extra property definition "ep2-conflict" with following properties:
      | entity_name   | product        |
      | property_name | conflict_field |
      | type          | string         |
      | scope         | lang           |
    Then I should get an error registering the extra property definition

  Scenario: Edit a core extra property definition
    When I add an extra property definition "ep3" with following properties:
      | entity_name   | product |
      | property_name | edit_me |
      | type          | string  |
      | scope         | common  |
      | size          | 64      |
      | nullable      | false   |
      | display_front | false   |
    Then extra property definition "ep3" should have the following parameters:
      | entity_name   | product |
      | property_name | edit_me |
      | type          | string  |
      | scope         | common  |
      | size          | 64      |
      | nullable      | false   |
      | display_front | false   |
    When I edit extra property definition "ep3" with following properties:
      | display_front | true |
      | nullable      | true |
      | size          | 128  |
    Then extra property definition "ep3" should have the following parameters:
      | entity_name   | product |
      | property_name | edit_me |
      | type          | string  |
      | scope         | common  |
      | size          | 128     |
      | nullable      | true    |
      | display_front | true    |

  Scenario: Decreasing the size of an existing extra property definition is rejected (destructive change)
    When I add an extra property definition "ep4" with following properties:
      | entity_name   | product   |
      | property_name | shrink_me |
      | type          | string    |
      | scope         | common    |
      | size          | 128       |
    When I edit extra property definition "ep4" with following properties:
      | size | 32 |
    Then I should get an error registering the extra property definition
    And extra property definition "ep4" should have the following parameters:
      | entity_name   | product   |
      | property_name | shrink_me |
      | type          | string    |
      | scope         | common    |
      | size          | 128       |

  Scenario: Editing a module-owned extra property definition is rejected
    Given a module-owned extra property definition "ep5" exists for entity "product" named "module_field" owned by module "demotestmodule"
    When I edit extra property definition "ep5" with following properties:
      | display_front | true |
    Then I should get an error that the extra property definition is protected by a module

  Scenario: Editing a non-existent extra property definition fails
    Given I define an uncreated extra property definition "unknownEp"
    When I edit extra property definition "unknownEp" with following properties:
      | display_front | true |
    Then I should get an error that the extra property definition was not found

  Scenario: Delete a core extra property definition, keeping its SQL column
    When I add an extra property definition "ep6" with following properties:
      | entity_name   | product   |
      | property_name | delete_me |
      | type          | string    |
      | scope         | common    |
    Then extra property definition "ep6" should still exist
    When I delete extra property definition "ep6"
    Then extra property definition "ep6" should no longer exist

  Scenario: Delete a core extra property definition and drop its SQL column
    When I add an extra property definition "ep7" with following properties:
      | entity_name   | product        |
      | property_name | delete_drop_me |
      | type          | string         |
      | scope         | common         |
    When I delete extra property definition "ep7" and drop its column
    Then extra property definition "ep7" should no longer exist

  Scenario: Deleting a module-owned extra property definition is rejected
    Given a module-owned extra property definition "ep8" exists for entity "product" named "module_field_delete" owned by module "demotestmodule"
    When I delete extra property definition "ep8"
    Then I should get an error that the extra property definition is protected by a module
    And extra property definition "ep8" should still exist

  Scenario: Bulk delete a mix of core and module-owned extra property definitions
    When I add an extra property definition "ep9" with following properties:
      | entity_name   | product  |
      | property_name | bulk_one |
      | type          | string   |
      | scope         | common   |
    And I add an extra property definition "ep10" with following properties:
      | entity_name   | product  |
      | property_name | bulk_two |
      | type          | string   |
      | scope         | common   |
    And a module-owned extra property definition "ep11" exists for entity "product" named "bulk_module" owned by module "demotestmodule"
    When I bulk delete extra property definitions "ep9,ep10,ep11"
    Then the bulk deletion should report 1 skipped definitions
    And extra property definition "ep9" should no longer exist
    And extra property definition "ep10" should no longer exist
    And extra property definition "ep11" should still exist

  Scenario: Edit the associated_apis of a core extra property definition
    When I add an extra property definition "ep14" with following properties:
      | entity_name     | product   |
      | property_name   | api_field |
      | type            | string    |
      | scope           | common    |
      | associated_apis | /products |
    Then extra property definition "ep14" should have the following parameters:
      | associated_apis | /products |
    When I edit extra property definition "ep14" with following properties:
      | associated_apis | /products,/products/{productId}:GET |
    Then extra property definition "ep14" should have the following parameters:
      | associated_apis | /products,/products/{productId}:GET |

  Scenario: Change the sql_index of a core extra property definition
    When I add an extra property definition "ep15" with following properties:
      | entity_name   | product     |
      | property_name | index_field |
      | type          | string      |
      | scope         | common      |
      | sql_index     | none        |
    Then extra property definition "ep15" should have the following parameters:
      | sql_index | none |
    When I edit extra property definition "ep15" with following properties:
      | sql_index | key |
    Then extra property definition "ep15" should have the following parameters:
      | sql_index | key |

  Scenario: Create a choice field with enum values and add a new value on edit
    When I add an extra property definition "ep16" with following properties:
      | entity_name   | product      |
      | property_name | choice_field |
      | type          | choice       |
      | scope         | common       |
      | enum_values   | red,green    |
    Then extra property definition "ep16" should have the following parameters:
      | enum_values | red,green |
    When I edit extra property definition "ep16" with following properties:
      | enum_values | red,green,blue |
    Then extra property definition "ep16" should have the following parameters:
      | enum_values | red,green,blue |

  Scenario: Removing an existing choice value is rejected (destructive change)
    When I add an extra property definition "ep17" with following properties:
      | entity_name   | product        |
      | property_name | choice_field2  |
      | type          | choice         |
      | scope         | common         |
      | enum_values   | red,green,blue |
    When I edit extra property definition "ep17" with following properties:
      | enum_values | red,green |
    Then I should get an error registering the extra property definition
    And extra property definition "ep17" should have the following parameters:
      | enum_values | red,green,blue |

  Scenario: Create an extra property definition with associated forms and grids, then edit them
    When I add an extra property definition "ep18" with following properties:
      | entity_name      | product      |
      | property_name    | placed_field |
      | type             | string       |
      | scope            | common       |
      | label_wording    | Placed field |
      | associated_forms | product      |
      | associated_grids | product      |
    Then extra property definition "ep18" should have the following parameters:
      | associated_forms | product |
      | associated_grids | product |
    When I edit extra property definition "ep18" with following properties:
      | associated_forms | product,category |
      | associated_grids | category         |
    Then extra property definition "ep18" should have the following parameters:
      | associated_forms | product,category |
      | associated_grids | category         |

  Scenario: Set and edit the validation constraints of a core extra property definition
    When I add an extra property definition "ep19" with following properties:
      | entity_name   | product                             |
      | property_name | constrained_field                   |
      | type          | string                              |
      | scope         | common                              |
      | constraints   | NotBlank,TypedRegex('generic_name') |
    Then extra property definition "ep19" should have the following parameters:
      | constraints | NotBlank,TypedRegex('generic_name') |
    When I edit extra property definition "ep19" with following properties:
      | constraints | Email,Url |
    Then extra property definition "ep19" should have the following parameters:
      | constraints | Email,Url |

  Scenario: Set a list-valued validation constraint on a core extra property definition
    When I add an extra property definition "ep20" with following properties:
      | entity_name   | product                 |
      | property_name | choice_constrained      |
      | type          | string                  |
      | scope         | common                  |
      | constraints   | Choice(['a', 'b', 'c']) |
    Then extra property definition "ep20" should have the following parameters:
      | constraints | Choice(['a', 'b', 'c']) |
    When I edit extra property definition "ep20" with following properties:
      | constraints | NotBlank,Choice(['x', 'y']) |
    Then extra property definition "ep20" should have the following parameters:
      | constraints | NotBlank,Choice(['x', 'y']) |
