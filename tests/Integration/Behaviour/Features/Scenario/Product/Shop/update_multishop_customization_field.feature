# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multi-shop-customization-field
@restore-products-before-feature
@clear-cache-before-feature
@restore-shops-after-feature
@clear-cache-after-feature
@product-multi-shop
@product-customization-field
Feature: Update customization fields from Back Office (BO)
  As a BO user
  I need to be able to update customization fields options from BO

  Background:
    Given language "english" with locale "en-US" exists
    And language "french" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I add a shop group "test_second_shop_group" with name "Test second shop group" and color "green"
    And I add a shop "shop3" with name "test_third_shop" and color "blue" for the group "test_second_shop_group"
    And I add a shop "shop4" with name "test__fourth_shop" and color "blue" for the group "test_second_shop_group"
    And single shop context is loaded

  Scenario: I update some product customization fields label it occurs only in current language and current shop and don't affect any other fields
    Given I add product "product1" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And I update product "product1" with following customization fields:
      | reference       | type | name[en-US]                | name[fr-FR]                         | is required | modify_all_shops_name |
      | customFieldText | text | text on top of left lense  | texte en haut de la lentille gauche | true        | true                  |
      | customFieldFile | file | document joined with order | document joint a la commande        | true        | true                  |
    And I copy product product1 from shop shop1 to shop shop2
    And I copy product product1 from shop shop1 to shop shop3
    And I copy product product1 from shop shop1 to shop shop4
    And product product1 should have 1 customizable file field
    And product product1 should have 1 customizable text field
    And product product1 should have 1 customizable file field
    When I update product product1 with following customization fields:
      | reference       | type | name[en-US]                | name[fr-FR]                         | is required | modify_all_shops_name |
      | customFieldText | text | front-text                 | texte en haut de la lentille gauche | false       | false                 |
      | customFieldFile | file | document joined with order | document joint a la commande        | true        | true                  |
    Then product product1 should have 1 customizable text field
    And product product1 should have 1 customizable file field
    And product product1 should have following customization fields:
      | reference       | type | name[en-US]                |  name[fr-FR]                         | is required |
      | customFieldText | text | front-text                 | texte en haut de la lentille gauche  | false       |
      | customFieldFile | file | document joined with order | document joint a la commande         | true        |

  Scenario: I update some product customization fields label it occurs only in current shop and don't affect any other shops
    Given I add product "product2" with following information:
      | name[en-US] | magic staff |
      | type        | standard    |
    And I update product "product2" with following customization fields:
      | reference       | type | name[en-US]                | name[fr-FR]                         | is required | modify_all_shops_name |
      | customFieldText | text | text on top of left lense  | texte en haut de la lentille gauche | true        | true                  |
    And product product2 should have 1 customizable text field
    When I update product product2 with following customization fields:
      | reference       | type | name[en-US]                | name[fr-FR]                         | is required | modify_all_shops_name |
      | customFieldText | text | text on top of right lense | texte en haut de la lentille droite | true        | false                  |
    Then product product2 should have 1 customizable text field
    And product product2 should have following customization fields for shops shop1:
      | reference       | type | name[en-US]                 | name[fr-FR]                         | is required |
      | customFieldText | text |  text on top of right lense | texte en haut de la lentille droite | true       |
    And product product2 should have following customization fields for shops shop2,shop3:
      | reference       | type | name[en-US]                |  name[fr-FR]                         | is required |
      | customFieldText | text | text on top of left lense  | texte en haut de la lentille gauche  | true       |
