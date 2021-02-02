# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-feature-values
@reset-database-before-feature
@clear-cache-before-feature
@update-feature-values
Feature: Update product details from Back Office (BO)
  As a BO user
  I need to be able to update product feature values from BO

  Background:
    Given language "language1" with locale "en-US" exists
    And language "language2" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    When I create product feature "element" with specified properties:
      | name | Nature Element |
    Then product feature "element" name should be "Nature Element"
    When I create feature value "fire" for feature "element" with following properties:
      | value[en-US] | Fire |
      | value[fr-FR] | Feu  |
    And I create feature value "water" for feature "element" with following properties:
      | value[en-US] | Water |
      | value[fr-FR] | Eau   |
    And I create feature value "air" for feature "element" with following properties:
      | value[en-US] | Air |
      | value[fr-FR] | Air |
    And I create feature value "earth" for feature "element" with following properties:
      | value[en-US] | Earth |
      | value[fr-FR] | Terre |
    When I create product feature "emotion" with specified properties:
      | name | Emotion |
    Then product feature "emotion" name should be "Emotion"
    When I create feature value "joy" for feature "emotion" with following properties:
      | value[en-US] | Joy  |
      | value[fr-FR] | Joie |
    When I create feature value "anger" for feature "emotion" with following properties:
      | value[en-US] | Anger  |
      | value[fr-FR] | Colère |
    When I create feature value "sadness" for feature "emotion" with following properties:
      | value[en-US] | Sadness   |
      | value[fr-FR] | Tristesse |

    Scenario: I can associate predefined feature values to a product
      Given I add product "fireMagicBook" with following information:
        | name[en-US] | Fire Magic Book |
        | is_virtual  | false           |
      Then product "fireMagicBook" should have no feature values
      When I set to product "fireMagicBook" the following feature values:
        | feature | feature_value |
        | emotion | joy           |
        | emotion | anger         |
        | element | fire          |
      Then product "fireMagicBook" should have following feature values:
        | feature | feature_value |
        | emotion | joy           |
        | emotion | anger         |
        | element | fire          |

  Scenario: I can create and edit custom feature values to a product
    Given I add product "darkMagicBook" with following information:
      | name[en-US] | Dark Magic Book |
      | is_virtual  | false           |
    Then product "darkMagicBook" should have no feature values
    When I set to product "darkMagicBook" the following feature values:
      | feature | feature_value | custom_values                 | custom_reference |
      | emotion | anger         |                               |                  |
      | emotion | sadness       |                               |                  |
      | element |               | en-US:Darkness;fr-FR:Ténèbres | darkness         |
    Then product "darkMagicBook" should have following feature values:
      | feature | feature_value | custom_values                 |
      | emotion | anger         |                               |
      | emotion | sadness       |                               |
      | element | darkness      | en-US:Darkness;fr-FR:Ténèbres |
    When I set to product "darkMagicBook" the following feature values:
      | feature | feature_value | custom_values              |
      | emotion | anger         |                            |
      | emotion | sadness       |                            |
      | element | darkness      | en-US:Shadows;fr-FR:Ombres |
    Then product "darkMagicBook" should have following feature values:
      | feature | feature_value | custom_values              |
      | emotion | anger         |                            |
      | emotion | sadness       |                            |
      | element | darkness      | en-US:Shadows;fr-FR:Ombres |

  Scenario: I can remove all feature values from a Product
    Given I add product "lightMagicBook" with following information:
      | name[en-US] | Light Magic Book |
      | is_virtual  | false           |
    Then product "lightMagicBook" should have no feature values
    When I set to product "lightMagicBook" the following feature values:
      | feature | feature_value | custom_values             | custom_reference |
      | emotion | joy           |                           |                  |
      | element |               | en-US:Light;fr-FR:Lumière | light            |
    Then product "lightMagicBook" should have following feature values:
      | feature | feature_value | custom_values             |
      | emotion | joy           |                           |
      | element | light         | en-US:Light;fr-FR:Lumière |
    When I remove all feature values from product "lightMagicBook"
    Then product "lightMagicBook" should have no feature values
