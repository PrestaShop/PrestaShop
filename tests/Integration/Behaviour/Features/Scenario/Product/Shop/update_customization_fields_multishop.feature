# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-customization-fields-multishop
@restore-products-before-feature
@restore-languages-after-feature
@restore-shops-after-feature
@restore-languages-after-feature
@clear-cache-before-feature
@product-multishop
@update-customization-fields-multishop
Feature: Set product images for all shops from Back Office (BO)
  As an employee I need to be able to edit customization fields for each shop

  Background:
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    # We don't init too many things in the background because the product and shops are used along all the scenarios
    # so if we create them here there are new instances on each scenario

  Scenario: I create a multishop product then I add customization fields (they are added to all shops)
    Given I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And single shop context is loaded
    And I add product "product1" to shop "shop1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And product "product1" type should be standard
    When I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    Then product product1 is associated to shops "shop1,shop2"
    When I update product product1 with following customization fields for shop shop1:
      | reference    | type | name[en-US] | is required |
      | customField1 | text | front-text  | true        |
      | customField2 | text | back-text   | false       |
    # The customizability is always the same for all shops since they share the same fields
    Then product "product1" should require customization for shops "shop1,shop2"
    And product product1 should have 2 customizable text fields for shops "shop1,shop2"
    And product product1 should have 0 customizable file fields for shops "shop1,shop2"
    And product product1 should have following customization fields for shops shop1,shop2:
      | reference    | type | name[en-US] | is required |
      | customField1 | text | front-text  | true        |
      | customField2 | text | back-text   | false       |

  Scenario: I update some product customization fields and add additional one (name update only applies on one shop, new name is common to all)
    Given product product1 should have following customization fields for shops shop1,shop2:
      | reference    | type | name[en-US] | is required |
      | customField1 | text | front-text  | true        |
      | customField2 | text | back-text   | false       |
    When I update product product1 with following customization fields for shop shop2:
      | reference    | type | name[en-US] | is required |
      | customField1 | text | front-text  | false       |
      | customField2 | text | bottom-text | false       |
      | customField3 | file | back image  | false       |
    Then product "product1" should allow customization for shops "shop1,shop2"
    And product product1 should have 2 customizable text fields for shops "shop1,shop2"
    And product product1 should have 1 customizable file field for shops "shop1,shop2"
    And product product1 should have following customization fields for shop shop2:
      | reference    | type | name[en-US] | is required |
      | customField1 | text | front-text  | false       |
      | customField2 | text | bottom-text | false       |
      | customField3 | file | back image  | false       |
    # List of fields is updated on all shops, but only shop2 had its name updated for customField2
    And product product1 should have following customization fields for shop shop1:
      | reference    | type | name[en-US] | is required |
      | customField1 | text | front-text  | false       |
      | customField2 | text | back-text   | false       |
      | customField3 | file | back image  | false       |

  Scenario: I delete some product customization fields
    Given product product1 should have following customization fields for shop shop2:
      | reference    | type | name[en-US] | is required |
      | customField1 | text | front-text  | false       |
      | customField2 | text | bottom-text | false       |
      | customField3 | file | back image  | false       |
    And product product1 should have following customization fields for shop shop1:
      | reference    | type | name[en-US] | is required |
      | customField1 | text | front-text  | false       |
      | customField2 | text | back-text   | false       |
      | customField3 | file | back image  | false       |
    # Remove two fields and update the name for shop1 they now will be in sync
    When I update product product1 with following customization fields for shop shop1:
      | reference    | type | name[en-US] | is required |
      | customField2 | text | bottom-text | true        |
    Then product "product1" should require customization for shops "shop1,shop2"
    And product product1 should have 1 customizable text field for shops "shop1,shop2"
    And product product1 should have 0 customizable file fields for shops "shop1,shop2"
    And product product1 should have following customization fields for shops shop1,shop2:
      | reference    | type | name[en-US] | is required |
      | customField2 | text | bottom-text | true        |

  Scenario: Update customization field name in different languages
    Given language "french" with locale "fr-FR" exists
    And product "product1" should require customization
    And product product1 should have 1 customizable text field
    And product product1 should have 0 customizable file fields
    # The previous name in english was kept for french
    And product product1 should have following customization fields for shops shop1,shop2:
      | reference    | type | name[en-US] | name[fr-FR] | is required |
      | customField2 | text | bottom-text | bottom-text | true        |
    When I update product product1 with following customization fields for shop shop1:
      | reference    | type | name[en-US] | name[fr-FR]  | is required |
      | customField2 | text | bottom-text | texte du bas | true        |
    # Only shop1 will be updated
    And product product1 should have following customization fields for shop shop1:
      | reference    | type | name[en-US] | name[fr-FR]  | is required |
      | customField2 | text | bottom-text | texte du bas | true        |
    And product product1 should have following customization fields for shop shop2:
      | reference    | type | name[en-US] | name[fr-FR] | is required |
      | customField2 | text | bottom-text | bottom-text | true        |
    # Only shop2 will be updated
    When I update product product1 with following customization fields for shop shop2:
      | reference    | type | name[en-US] | name[fr-FR]  | is required |
      | customField2 | text | bottom      | texte du bas | true        |
    Then product product1 should have following customization fields for shop shop1:
      | reference    | type | name[en-US] | name[fr-FR]  | is required |
      | customField2 | text | bottom-text | texte du bas | true        |
    And product product1 should have following customization fields for shop shop2:
      | reference    | type | name[en-US] | name[fr-FR]  | is required |
      | customField2 | text | bottom      | texte du bas | true        |

  Scenario: I delete all customization fields for product
    Given product "product1" should require customization for shops "shop1,shop2"
    And product product1 should have 1 customizable text field for shops shop1,shop2
    And product product1 should have 0 customizable file fields for shops shop1,shop2
    And product product1 should have following customization fields for shop shop1:
      | reference    | type | name[en-US] | name[fr-FR]  | is required |
      | customField2 | text | bottom-text | texte du bas | true        |
    And product product1 should have following customization fields for shop shop2:
      | reference    | type | name[en-US] | name[fr-FR]  | is required |
      | customField2 | text | bottom      | texte du bas | true        |
    When I remove all customization fields from product product1
    Then product "product1" should not be customizable for shops "shop1,shop2"
    Then product product1 should have 0 customizable text fields for shops shop1,shop2
    And product product1 should have 0 customizable file fields for shops shop1,shop2
