# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-customization-fields
@reset-database-before-feature
@clear-cache-before-feature
@update-customization-fields
Feature: Update product customization fields in Back Office (BO)
  As a BO user
  I need to be able to update product customization fields in the BO

  Background:
    Given customization feature is enabled
    And language with iso code "en" is the default one

  Scenario: I add customization fields to product
    When I add product "product1" with following information:
      | name       | en-US: nice customizable t-shirt  |
      | is_virtual | false                             |
    And product "product1" type should be standard
    When I update product product1 with following customization fields:
      | reference             | type    | name                    | is required |
      | customField1          | text    | en-US:front-text        | true        |
      | customField2          | text    | en-US:back-text         | false       |
    Then product "product1" should require customization
    And product product1 should have 2 customizable text fields
    And product product1 should have 0 customizable file fields
    And product product1 should have following customization fields:
      | reference             | type    | name                    | is required |
      | customField1          | text    | en-US:front-text        | true        |
      | customField2          | text    | en-US:back-text         | false       |

  Scenario: I update some product customization fields and add additional one
    Given product product1 should have following customization fields:
      | reference             | type    | name                    | is required |
      | customField1          | text    | en-US:front-text        | true        |
      | customField2          | text    | en-US:back-text         | false       |
    When I update product product1 with following customization fields:
      | reference             | type    | name                    | is required |
      | customField1          | text    | en-US:front-text        | false       |
      | customField2          | text    | en-US:bottom-text       | false       |
      | customField3          | file    | en-US:back image        | false       |
    Then product "product1" should allow customization
    And product product1 should have 2 customizable text fields
    And product product1 should have 1 customizable file field
    And product product1 should have following customization fields:
      | reference             | type    | name                    | is required |
      | customField1          | text    | en-US:front-text        | false       |
      | customField2          | text    | en-US:bottom-text       | false       |
      | customField3          | file    | en-US:back image        | false       |

  Scenario: I delete some product customization fields
    Given product product1 should have following customization fields:
      | reference             | type    | name                    | is required |
      | customField1          | text    | en-US:front-text        | false       |
      | customField2          | text    | en-US:bottom-text       | false       |
      | customField3          | file    | en-US:back image        | false       |
    When I update product product1 with following customization fields:
      | reference             | type    | name                    | is required |
      | customField2          | text    | en-US:bottom-text       | true        |
    Then product "product1" should require customization
    And product product1 should have 1 customizable text field
    And product product1 should have 0 customizable file fields
    And product product1 should have following customization fields:
      | reference             | type    | name                    | is required |
      | customField2          | text    | en-US:bottom-text       | true        |

  Scenario: Update customization field name in different languages
    Given language "french" with locale "fr-FR" exists
    And product "product1" should require customization
    And product product1 should have 1 customizable text field
    And product product1 should have 0 customizable file fields
    And product product1 should have following customization fields:
      | reference             | type    | name                                 | is required |
      | customField2          | text    | en-US:bottom-text;fr-FR:bottom-text  | true        |
    When I update product product1 with following customization fields:
      | reference             | type    | name                                 | is required |
      | customField2          | text    | en-US:bottom-text;fr-FR:texte du bas | true        |
    And product product1 should have following customization fields:
      | reference             | type    | name                                 | is required |
      | customField2          | text    | en-US:bottom-text;fr-FR:texte du bas | true        |
    When I update product product1 with following customization fields:
      | reference             | type    | name                                 | is required |
      | customField2          | text    | en-US:bottom;fr-FR:texte du bas      | true        |
    Then product product1 should have following customization fields:
      | reference             | type    | name                                 | is required |
      | customField2          | text    | en-US:bottom;fr-FR:texte du bas      | true        |

  Scenario: I delete all customization fields for product
    Given product "product1" should require customization
    And product product1 should have 1 customizable text field
    And product product1 should have 0 customizable file fields
    And product product1 should have following customization fields:
      | reference             | type    | name                                  | is required |
      | customField2          | text    | en-US:bottom;fr-FR:texte du bas       | true        |
    When I delete all customization fields from product product1
    Then product "product1" should not be customizable
    Then product product1 should have 0 customizable text fields
    And product product1 should have 0 customizable file fields
