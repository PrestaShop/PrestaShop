# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-feature-values
@restore-products-before-feature
@restore-languages-after-feature
@clear-cache-before-feature
@update-feature-values
Feature: Update product details from Back Office (BO)
  As a BO user
  I need to be able to update product feature values from BO

  Background:
    Given language "en" with locale "en-US" exists
    And language "fr" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And shop "shop1" with name "test_shop" exists
    When I create product feature "element" with specified properties:
      | name[en-US]      | Nature Element |
      | associated shops | shop1          |
    Then product feature "element" should have following details:
      | name[en-US] | Nature Element |
      | name[fr-FR] | Nature Element |
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
      | name[en-US]      | Emotion |
      | associated shops | shop1   |
    Then product feature "emotion" should have following details:
      | name[en-US] | Emotion |
      | name[fr-FR] | Emotion |
    When I create feature value "joy" for feature "emotion" with following properties:
      | value[en-US] | Joy  |
      | value[fr-FR] | Joie |
    When I create feature value "love" for feature "emotion" with following properties:
      | value[en-US] | Love  |
      | value[fr-FR] | Amour |
    When I create feature value "anger" for feature "emotion" with following properties:
      | value[en-US] | Anger  |
      | value[fr-FR] | Colère |
    When I create feature value "sadness" for feature "emotion" with following properties:
      | value[en-US] | Sadness   |
      | value[fr-FR] | Tristesse |
    When I create feature value "disgust" for feature "emotion" with following properties:
      | value[en-US] | Disgust |
      | value[fr-FR] | Dégout  |

  Scenario: I can associate predefined feature values to a product
    Given I add product "fireMagicBook" with following information:
      | name[en-US] | Fire Magic Book |
      | type        | standard        |
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
      | type        | standard        |
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
    And feature value "darkness" should be associated to feature "element"
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

  Scenario: I remove a custom feature from a value it is cleaned in the DB
    Given I add product "beginnerMagicBook" with following information:
      | name[en-US] | Beginner Magic Book |
      | type        | standard            |
    Then product "beginnerMagicBook" should have no feature values
    # Expectations?
    When I set to product "beginnerMagicBook" the following feature values:
      | feature | feature_value | custom_values            | custom_reference |
      | emotion | joy           |                          |                  |
      | emotion | love          |                          |                  |
      | element |               | en-US:Candy;fr-FR:Bonbon | candy            |
    Then product "beginnerMagicBook" should have following feature values:
      | feature | feature_value | custom_values            |
      | emotion | joy           |                          |
      | emotion | love          |                          |
      | element | candy         | en-US:Candy;fr-FR:Bonbon |
    And feature value "candy" should be associated to feature "element"
    When I set to product "beginnerMagicBook" the following feature values:
      | feature | feature_value | custom_values        | custom_reference |
      | emotion | anger         |                      |                  |
      | emotion | disgust       |                      |                  |
      | element |               | en-US:Poo;fr-FR:Caca | poo              |
    # Reality!!
    Then product "beginnerMagicBook" should have following feature values:
      | feature | feature_value | custom_values        |
      | emotion | anger         |                      |
      | emotion | disgust       |                      |
      | element | poo           | en-US:Poo;fr-FR:Caca |
    And feature value "candy" should not exist
    And feature value "poo" should be associated to feature "element"

  Scenario: I can remove all feature values from a Product
    Given I add product "lightMagicBook" with following information:
      | name[en-US] | Light Magic Book |
      | type        | standard         |
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
    And feature value "light" should not exist

  Scenario: I can not set the same feature twice
    Given I add product "lightMagicBook" with following information:
      | name[en-US] | Light Magic Book |
      | type        | standard         |
    Then product "lightMagicBook" should have no feature values
    When I set to product "lightMagicBook" the following feature values:
      | feature | feature_value |
      | emotion | joy           |
      | emotion | joy           |
    Then I should get an error that a feature can only be associated once
    And product "lightMagicBook" should have no feature values

  Scenario: I can not set a value to another feature
    Given I add product "lightMagicBook" with following information:
      | name[en-US] | Light Magic Book |
      | type        | standard         |
    Then product "lightMagicBook" should have no feature values
    When I set to product "lightMagicBook" the following feature values:
      | feature | feature_value |
      | element | joy           |
    Then I should get an error that a feature value cannot be associated to another feature
    And product "lightMagicBook" should have no feature values
