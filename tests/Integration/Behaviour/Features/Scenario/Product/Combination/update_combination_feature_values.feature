# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-feature-values
@restore-products-before-feature
@restore-languages-after-feature
@clear-cache-before-feature
@product-combination
@update-combination-feature-values
Feature: Update product combination feature values from Back Office (BO)
  As a BO user
  I need to be able to update feature values of a product combination from BO

  Background:
    Given language "en" with locale "en-US" exists
    And language "fr" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And shop "shop1" with name "test_shop" exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists
    When I create product feature "element" with specified properties:
      | name[en-US]      | Nature Element |
      | associated shops | shop1          |
    When I create feature value "fire" for feature "element" with following properties:
      | value[en-US] | Fire |
      | value[fr-FR] | Feu  |
    And I create feature value "water" for feature "element" with following properties:
      | value[en-US] | Water |
      | value[fr-FR] | Eau   |
    When I create product feature "emotion" with specified properties:
      | name[en-US]      | Emotion |
      | associated shops | shop1   |
    When I create feature value "joy" for feature "emotion" with following properties:
      | value[en-US] | Joy  |
      | value[fr-FR] | Joie |
    When I create feature value "anger" for feature "emotion" with following properties:
      | value[en-US] | Anger  |
      | value[fr-FR] | Colère |

  Scenario: I can associate predefined feature values to a combination
    Given I add product "shirt" with following information:
      | name[en-US] | Magic shirt  |
      | type        | combinations |
    And I generate combinations for product shirt using following attributes:
      | Color | [Blue,Red] |
    And product "shirt" should have following combinations:
      | id reference | combination name | reference | attributes   | impact on price | quantity | is default |
      | shirtBlue    | Color - Blue     |           | [Color:Blue] | 0               | 0        | true       |
      | shirtRed     | Color - Red      |           | [Color:Red]  | 0               | 0        | false      |
    Then combination "shirtBlue" should have no feature values
    When I set to combination "shirtBlue" the following feature values:
      | feature | feature_value |
      | emotion | joy           |
      | emotion | anger         |
      | element | fire          |
    Then combination "shirtBlue" should have following feature values:
      | feature | feature_value |
      | emotion | joy           |
      | emotion | anger         |
      | element | fire          |
    # Other combinations are not impacted
    And combination "shirtRed" should have no feature values

  Scenario: I can create and edit custom feature values on a combination
    Given I add product "book" with following information:
      | name[en-US] | Magic book   |
      | type        | combinations |
    And I generate combinations for product book using following attributes:
      | Color | [Blue] |
    And product "book" should have following combinations:
      | id reference | combination name | reference | attributes   | impact on price | quantity | is default |
      | bookBlue     | Color - Blue     |           | [Color:Blue] | 0               | 0        | true       |
    Then combination "bookBlue" should have no feature values
    When I set to combination "bookBlue" the following feature values:
      | feature | feature_value | custom_values                 | custom_reference |
      | emotion | anger         |                               |                  |
      | element |               | en-US:Darkness;fr-FR:Ténèbres | darkness         |
    Then combination "bookBlue" should have following feature values:
      | feature | feature_value | custom_values                 |
      | emotion | anger         |                               |
      | element | darkness      | en-US:Darkness;fr-FR:Ténèbres |
    And feature value "darkness" should be associated to feature "element"
    When I set to combination "bookBlue" the following feature values:
      | feature | feature_value | custom_values              |
      | emotion | anger         |                            |
      | element | darkness      | en-US:Shadows;fr-FR:Ombres |
    Then combination "bookBlue" should have following feature values:
      | feature | feature_value | custom_values              |
      | emotion | anger         |                            |
      | element | darkness      | en-US:Shadows;fr-FR:Ombres |

  Scenario: I remove a custom feature value from a combination it is cleaned in the DB
    Given I add product "candyBook" with following information:
      | name[en-US] | Candy book   |
      | type        | combinations |
    And I generate combinations for product candyBook using following attributes:
      | Color | [Blue] |
    And product "candyBook" should have following combinations:
      | id reference   | combination name | reference | attributes   | impact on price | quantity | is default |
      | candyBookBlue  | Color - Blue     |           | [Color:Blue] | 0               | 0        | true       |
    When I set to combination "candyBookBlue" the following feature values:
      | feature | feature_value | custom_values            | custom_reference |
      | emotion | joy           |                          |                  |
      | element |               | en-US:Candy;fr-FR:Bonbon | candy            |
    Then combination "candyBookBlue" should have following feature values:
      | feature | feature_value | custom_values            |
      | emotion | joy           |                          |
      | element | candy         | en-US:Candy;fr-FR:Bonbon |
    And feature value "candy" should be associated to feature "element"
    When I set to combination "candyBookBlue" the following feature values:
      | feature | feature_value |
      | emotion | joy           |
    Then combination "candyBookBlue" should have following feature values:
      | feature | feature_value |
      | emotion | joy           |
    And feature value "candy" should not exist

  Scenario: I can remove all feature values from a combination
    Given I add product "lightBook" with following information:
      | name[en-US] | Light book   |
      | type        | combinations |
    And I generate combinations for product lightBook using following attributes:
      | Color | [Blue] |
    And product "lightBook" should have following combinations:
      | id reference   | combination name | reference | attributes   | impact on price | quantity | is default |
      | lightBookBlue  | Color - Blue     |           | [Color:Blue] | 0               | 0        | true       |
    When I set to combination "lightBookBlue" the following feature values:
      | feature | feature_value | custom_values             | custom_reference |
      | emotion | joy           |                           |                  |
      | element |               | en-US:Light;fr-FR:Lumière | light            |
    Then combination "lightBookBlue" should have following feature values:
      | feature | feature_value | custom_values             |
      | emotion | joy           |                           |
      | element | light         | en-US:Light;fr-FR:Lumière |
    When I remove all feature values from combination "lightBookBlue"
    Then combination "lightBookBlue" should have no feature values
    And feature value "light" should not exist

  Scenario: I can not set the same feature value twice on a combination
    Given I add product "twiceBook" with following information:
      | name[en-US] | Twice book   |
      | type        | combinations |
    And I generate combinations for product twiceBook using following attributes:
      | Color | [Blue] |
    And product "twiceBook" should have following combinations:
      | id reference   | combination name | reference | attributes   | impact on price | quantity | is default |
      | twiceBookBlue  | Color - Blue     |           | [Color:Blue] | 0               | 0        | true       |
    When I set to combination "twiceBookBlue" the following feature values:
      | feature | feature_value |
      | emotion | joy           |
      | emotion | joy           |
    Then I should get an error that a combination feature can only be associated once
    And combination "twiceBookBlue" should have no feature values

  Scenario: I can not set a value to another feature on a combination
    Given I add product "wrongBook" with following information:
      | name[en-US] | Wrong book   |
      | type        | combinations |
    And I generate combinations for product wrongBook using following attributes:
      | Color | [Blue] |
    And product "wrongBook" should have following combinations:
      | id reference   | combination name | reference | attributes   | impact on price | quantity | is default |
      | wrongBookBlue  | Color - Blue     |           | [Color:Blue] | 0               | 0        | true       |
    When I set to combination "wrongBookBlue" the following feature values:
      | feature | feature_value |
      | element | joy           |
    Then I should get an error that a combination feature value cannot be associated to another feature
    And combination "wrongBookBlue" should have no feature values

  Scenario: Saving product feature values does not delete combination custom feature values
    Given I add product "sharedBook" with following information:
      | name[en-US] | Shared book  |
      | type        | combinations |
    And I generate combinations for product sharedBook using following attributes:
      | Color | [Blue] |
    And product "sharedBook" should have following combinations:
      | id reference   | combination name | reference | attributes   | impact on price | quantity | is default |
      | sharedBookBlue | Color - Blue     |           | [Color:Blue] | 0               | 0        | true       |
    When I set to combination "sharedBookBlue" the following feature values:
      | feature | feature_value | custom_values                | custom_reference |
      | element |               | en-US:Shared;fr-FR:Partagé   | sharedCustom     |
    Then combination "sharedBookBlue" should have following feature values:
      | feature | feature_value | custom_values              |
      | element | sharedCustom  | en-US:Shared;fr-FR:Partagé |
    And feature value "sharedCustom" should be associated to feature "element"
    # Saving the product-level features triggers a global orphan custom-value cleanup;
    # the combination custom value must survive (regression: it used to be deleted)
    When I set to product "sharedBook" the following feature values:
      | feature | feature_value |
      | emotion | joy           |
    Then feature value "sharedCustom" should be associated to feature "element"
    And combination "sharedBookBlue" should have following feature values:
      | feature | feature_value | custom_values              |
      | element | sharedCustom  | en-US:Shared;fr-FR:Partagé |

  Scenario: A custom feature value shared by a product and its combination is only cleaned when removed from both
    Given I add product "linkBook" with following information:
      | name[en-US] | Link book    |
      | type        | combinations |
    And I generate combinations for product linkBook using following attributes:
      | Color | [Blue] |
    And product "linkBook" should have following combinations:
      | id reference | combination name | reference | attributes   | impact on price | quantity | is default |
      | linkBookBlue | Color - Blue     |           | [Color:Blue] | 0               | 0        | true       |
    # A custom value is first created at product level
    When I set to product "linkBook" the following feature values:
      | feature | feature_value | custom_values          | custom_reference |
      | element |               | en-US:Linked;fr-FR:Lié | linked           |
    Then feature value "linked" should be associated to feature "element"
    # The very same custom value is then also associated to the combination
    When I set to combination "linkBookBlue" the following feature values:
      | feature | feature_value |
      | element | linked        |
    Then combination "linkBookBlue" should have following feature values:
      | feature | feature_value | custom_values          |
      | element | linked        | en-US:Linked;fr-FR:Lié |
    # Removing it from the product must NOT delete it: the combination still references it
    When I set to product "linkBook" the following feature values:
      | feature | feature_value |
      | emotion | joy           |
    Then feature value "linked" should be associated to feature "element"
    And combination "linkBookBlue" should have following feature values:
      | feature | feature_value | custom_values          |
      | element | linked        | en-US:Linked;fr-FR:Lié |
    # Removing it from the combination too leaves it orphaned, so it is finally cleaned up
    When I remove all feature values from combination "linkBookBlue"
    Then combination "linkBookBlue" should have no feature values
    And feature value "linked" should not exist
