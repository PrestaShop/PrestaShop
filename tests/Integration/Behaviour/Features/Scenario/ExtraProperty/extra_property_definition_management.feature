# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s extra-property-definition
@reset-extra-properties-before-feature
@reset-extra-properties-after-feature
@restore-shops-before-feature
@restore-shops-after-feature
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
    Then I should get an error that the scope conflicts with an existing definition

  Scenario: Adding an extra property definition on an unknown entity is rejected and persists nothing
    When I add an extra property definition "epGhost" with following properties:
      | entity_name   | demo_unknown |
      | property_name | ghost_field  |
      | type          | string       |
      | scope         | common       |
    Then I should get an error that the entity table is missing
    And no extra property definition should exist for entity "demo_unknown" and property "ghost_field"

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
    Then I should get an error that the change is destructive
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

  Scenario: Create an extra property definition restricted to some shops, edit and revert its association
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And I add a shop group "epShopGroup" with name "Extra property shop group" and color "red"
    And I add a shop "epShop2" with name "Extra property shop 2" and color "green" for the group "epShopGroup"
    When I add an extra property definition "ep21" with following properties:
      | entity_name         | product         |
      | property_name       | shop_restricted |
      | type                | string          |
      | scope               | common          |
      | associated_shop_ids | shop1           |
    Then extra property definition "ep21" should have the following parameters:
      | associated_shop_ids | shop1 |
    When I edit extra property definition "ep21" with following properties:
      | associated_shop_ids | shop1,epShop2 |
    Then extra property definition "ep21" should have the following parameters:
      | associated_shop_ids | shop1,epShop2 |
    # Editing other fields without providing the association leaves the stored association untouched
    When I edit extra property definition "ep21" with following properties:
      | display_front | true |
    Then extra property definition "ep21" should have the following parameters:
      | display_front       | true          |
      | associated_shop_ids | shop1,epShop2 |
    # An empty cell reverts to the fallback behavior (no explicit restriction)
    When I edit extra property definition "ep21" with following properties:
      | associated_shop_ids |  |
    Then extra property definition "ep21" should have the following parameters:
      | associated_shop_ids |  |

  Scenario: The shop association is the only editable field of a module-owned extra property definition
    Given shop "shop1" with name "test_shop" exists
    And a module-owned extra property definition "ep22" exists for entity "product" named "module_shop_field" owned by module "demotestmodule"
    When I edit extra property definition "ep22" with following properties:
      | associated_shop_ids | shop1 |
    Then extra property definition "ep22" should have the following parameters:
      | associated_shop_ids | shop1 |
    When I edit extra property definition "ep22" with following properties:
      | associated_shop_ids | shop1 |
      | display_front       | true  |
    Then I should get an error that the extra property definition is protected by a module
    And extra property definition "ep22" should have the following parameters:
      | associated_shop_ids | shop1 |
      | display_front       | false |

  Scenario: Default values keep their type through the whole round-trip
    # BOOL false used to be lost on reload (naive string cast turned it into "no default"),
    # and a falsy '0' default was dropped by the BO form handler
    When I add an extra property definition "ep30" with following properties:
      | entity_name   | product     |
      | property_name | default_off |
      | type          | bool        |
      | nullable      | false       |
      | default_value | 0           |
    Then extra property definition "ep30" should have the following parameters:
      | default_value | 0 |
    When I add an extra property definition "ep31" with following properties:
      | entity_name   | product      |
      | property_name | default_zero |
      | type          | int          |
      | default_value | 0            |
    Then extra property definition "ep31" should have the following parameters:
      | default_value | 0 |
    # A default that does not fit the declared type is refused before anything is written
    When I add an extra property definition "ep32" with following properties:
      | entity_name   | product      |
      | property_name | default_bad  |
      | type          | int          |
      | default_value | not-a-number |
    Then I should get an error that the default value is invalid
    And no extra property definition should exist for entity "product" and property "default_bad"

  Scenario: Associating an extra property definition with a shop that does not exist is rejected
    Given shop "shop1" with name "test_shop" exists
    And I define an uncreated shop "ghostShop"
    # The association rows carry no foreign key, so the registry refuses unknown ids
    # before any write — otherwise the definition would silently vanish from every shop
    When I add an extra property definition "ep23" with following properties:
      | entity_name         | product         |
      | property_name       | ghost_shop_prop |
      | type                | string          |
      | scope               | common          |
      | associated_shop_ids | shop1,ghostShop |
    Then I should get an error that the shop association contains an unknown shop
    And no extra property definition should exist for entity "product" and property "ghost_shop_prop"
    # The guard sits on the single write endpoint, so edits are covered too
    When I add an extra property definition "ep23" with following properties:
      | entity_name         | product         |
      | property_name       | ghost_shop_prop |
      | type                | string          |
      | scope               | common          |
      | associated_shop_ids | shop1           |
    And I edit extra property definition "ep23" with following properties:
      | associated_shop_ids | ghostShop |
    Then I should get an error that the shop association contains an unknown shop
    And extra property definition "ep23" should have the following parameters:
      | associated_shop_ids | shop1 |

  Scenario: Validation constraints with named options, composites and custom messages round-trip
    When I add an extra property definition "ep40" with following properties:
      | entity_name   | product                                                                     |
      | property_name | constraint_shapes                                                           |
      | type          | string                                                                      |
      | scope         | common                                                                      |
      | constraints   | NotBlank(message: 'Required!'),Length(min: 2, max: 64),All[ Url, NotBlank ] |
    Then extra property definition "ep40" should have the following parameters:
      | constraints | NotBlank(message: 'Required!'),Length(min: 2, max: 64),All[ Url, NotBlank ] |
    # The stored form is the canonical render: one constraint per line, composites indented,
    # named options sorted alphabetically
    And extra property definition "ep40" should have the following constraints:
      """
      NotBlank(message: 'Required!')
      Length(max: 64, min: 2)
      All[
        Url,
        NotBlank
      ]
      """

  Scenario: A Collection constraint reads back with the Required wrappers Symfony gives its fields
    When I add an extra property definition "ep41" with following properties:
      | entity_name   | product                                                                   |
      | property_name | constraint_collection                                                     |
      | type          | json                                                                      |
      | scope         | common                                                                    |
      | constraints   | Collection(allowExtraFields: true)[ name: NotBlank, code: Length(min: 2, max: 5) ] |
    # Required/Optional are only ever accepted as Collection fields (see the refused examples below)
    Then extra property definition "ep41" should have the following parameters:
      | constraints | Collection(allowExtraFields: true)[ name: Required[ NotBlank ], code: Required[ Length(min: 2, max: 5) ] ] |
    And extra property definition "ep41" should have the following constraints:
      """
      Collection(allowExtraFields: true)[
        name: Required[
          NotBlank
        ],
        code: Required[
          Length(max: 5, min: 2)
        ]
      ]
      """

  Scenario: Validation constraint values keep their type
    # An unquoted number is a number, a quoted one is a string, and a list may mix both
    When I add an extra property definition "ep42" with following properties:
      | entity_name   | product                                                     |
      | property_name | constraint_typed_values                                     |
      | type          | int                                                         |
      | scope         | common                                                      |
      | constraints   | GreaterThan(5),NotEqualTo('5'),Choice(['1', 2]),LessThan(9.5) |
    Then extra property definition "ep42" should have the following parameters:
      | constraints | GreaterThan(5),NotEqualTo('5'),Choice(['1', 2]),LessThan(9.5) |
    And extra property definition "ep42" should have the following constraints:
      """
      GreaterThan(5)
      NotEqualTo('5')
      Choice(['1', 2])
      LessThan(9.5)
      """

  Scenario: Per-language validation rules of a multilingual extra property
    When I add an extra property definition "ep43" with following properties:
      | entity_name   | product                              |
      | property_name | constraint_per_language              |
      | type          | string                               |
      | scope         | lang                                 |
      | constraints   | All[ NotBlank, Length(min: 1, max: 10) ] |
    Then extra property definition "ep43" should have the following parameters:
      | constraints | All[ NotBlank, Length(min: 1, max: 10) ] |

  Scenario: Editing keeps, replaces or clears the validation constraints
    When I add an extra property definition "ep44" with following properties:
      | entity_name   | product           |
      | property_name | constraint_edits  |
      | type          | string            |
      | scope         | common            |
      | constraints   | NotBlank,Email    |
    # A row-less edit leaves the constraints untouched
    When I edit extra property definition "ep44" with following properties:
      | label_wording | Edited label |
    Then extra property definition "ep44" should have the following parameters:
      | label_wording | Edited label   |
      | constraints   | NotBlank,Email |
    # Providing constraints replaces them as a whole
    When I edit extra property definition "ep44" with following properties:
      | constraints | Url |
    Then extra property definition "ep44" should have the following parameters:
      | constraints | Url |
    # An empty cell removes every constraint
    When I edit extra property definition "ep44" with following properties:
      | constraints |  |
    Then extra property definition "ep44" should have the following parameters:
      | constraints |  |

  Scenario: Validation constraints the parser cannot build are refused by the command
    # The DSL is parsed when the command is built: an unknown name, a malformed token or an option
    # Symfony would execute never reaches the registry, so nothing is written. Each attempt below
    # fails on its own; the property name is reused because no attempt ever creates the row.
    # Unknown constraint name
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                      |
      | property_name | constraint_refused_by_parser |
      | constraints   | Nope                         |
    Then I should get an error that the constraints are invalid
    # Names are case-sensitive
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                      |
      | property_name | constraint_refused_by_parser |
      | constraints   | notblank                     |
    Then I should get an error that the constraints are invalid
    # Unbalanced delimiter
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                      |
      | property_name | constraint_refused_by_parser |
      | constraints   | Length(min: 2                |
    Then I should get an error that the constraints are invalid
    # An option Symfony invokes as a callable at validation time
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                      |
      | property_name | constraint_refused_by_parser |
      | constraints   | Length(normalizer: 'trim')   |
    Then I should get an error that the constraints are invalid
    # An option that traverses the validated object
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                            |
      | property_name | constraint_refused_by_parser       |
      | constraints   | GreaterThan(propertyPath: 'other') |
    Then I should get an error that the constraints are invalid
    # A value given to a constraint that takes none
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                      |
      | property_name | constraint_refused_by_parser |
      | constraints   | NotBlank(5)                  |
    Then I should get an error that the constraints are invalid
    # A required option left out (Regex needs its pattern)
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                      |
      | property_name | constraint_refused_by_parser |
      | constraints   | Regex                        |
    Then I should get an error that the constraints are invalid
    # The bracket shape on a constraint that is not a composite
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                      |
      | property_name | constraint_refused_by_parser |
      | constraints   | NotBlank[ Url ]              |
    Then I should get an error that the constraints are invalid
    # Keyed children outside a Collection
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                      |
      | property_name | constraint_refused_by_parser |
      | constraints   | All[ name: NotBlank ]        |
    Then I should get an error that the constraints are invalid
    # The internal Collection wrappers are not public constraints
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                      |
      | property_name | constraint_refused_by_parser |
      | constraints   | Required[ NotBlank ]         |
    Then I should get an error that the constraints are invalid
    # Nesting deeper than the grammar bound (16 levels)
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                                                                                         |
      | property_name | constraint_refused_by_parser                                                                    |
      | constraints   | All[All[All[All[All[All[All[All[All[All[All[All[All[All[All[All[All[All[ NotBlank ]]]]]]]]]]]]]]]]]] |
    Then I should get an error that the constraints are invalid
    # Options the format never carries: only the default validation group applies, and there is no payload
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                      |
      | property_name | constraint_refused_by_parser |
      | constraints   | NotBlank(groups: ['custom']) |
    Then I should get an error that the constraints are invalid
    When I add an extra property definition "ep45" with following properties:
      | entity_name   | product                      |
      | property_name | constraint_refused_by_parser |
      | constraints   | NotBlank(payload: 'x')       |
    Then I should get an error that the constraints are invalid
    And no extra property definition should exist for entity "product" and property "constraint_refused_by_parser"

  Scenario: Validation constraints that cannot be stored losslessly are refused by the registry
    # This parses into a valid Symfony constraint, but the persisted DSL could not carry it back
    # identically: a whole-number float renders as an integer. The registry refuses it before any
    # storage column is created.
    When I add an extra property definition "ep46" with following properties:
      | entity_name   | product                        |
      | property_name | constraint_refused_by_registry |
      | type          | int                            |
      | constraints   | GreaterThan(1.0)               |
    Then I should get an error that the constraints cannot be stored
    And no extra property definition should exist for entity "product" and property "constraint_refused_by_registry"

  Scenario: Refused validation constraints leave an existing definition unchanged
    When I add an extra property definition "ep47" with following properties:
      | entity_name   | product                     |
      | property_name | constraint_refused_on_edit  |
      | type          | string                      |
      | scope         | common                      |
      | constraints   | NotBlank                    |
    When I edit extra property definition "ep47" with following properties:
      | constraints | Nope |
    Then I should get an error that the constraints are invalid
    And extra property definition "ep47" should have the following parameters:
      | constraints | NotBlank |
    When I edit extra property definition "ep47" with following properties:
      | constraints | NotBlank(groups: ['custom']) |
    Then I should get an error that the constraints are invalid
    And extra property definition "ep47" should have the following parameters:
      | constraints | NotBlank |
