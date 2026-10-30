# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-content-multishop
@restore-products-before-feature
@clear-cache-before-feature
@product-combination
@update-combination-content-multishop
Feature: Update product combination descriptions and meta tags in Back Office (BO) in multi shop context
  As an employee
  I need to be able to override the product descriptions and meta tags of a combination per shop

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Color" named "Color" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And shop "shop1" with name "test_shop" exists
    And I enable multishop feature
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I associate attribute group "Color" with shops "shop1,shop2"
    And I associate attribute "White" with shops "shop1,shop2"
    And I associate attribute "Black" with shops "shop1,shop2"
    And single shop context is loaded
    And I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And I generate combinations in shop "shop1" for product product1 using following attributes:
      | Color | [White,Black] |
    And I generate combinations in shop "shop2" for product product1 using following attributes:
      | Color | [White,Black] |
    And product "product1" should have the following combinations for shops "shop1,shop2":
      | id reference  | combination name | reference | attributes    | impact on price | quantity | is default |
      | product1White | Color - White    |           | [Color:White] | 0               | 0        | true       |
      | product1Black | Color - Black    |           | [Color:Black] | 0               | 0        | false      |

  Scenario: I override the content for a single shop
    When I update combination "product1Black" content with following values for shop "shop2":
      | meta_title[en-US]        | Black T-shirt from shop 2 |
      | description_short[en-US] | <p>Shop 2 summary</p>     |
    Then combination "product1Black" should have following content for shop "shop2":
      | meta_title[en-US]        | Black T-shirt from shop 2 |
      | description_short[en-US] | <p>Shop 2 summary</p>     |
    And combination "product1Black" should have following content for shop "shop1":
      | meta_title[en-US]        |  |
      | description_short[en-US] |  |

  Scenario: I override a value for all shops, the other values of each shop are kept
    Given I update combination "product1Black" content with following values for shop "shop2":
      | description_short[en-US] | <p>Shop 2 summary</p> |
    When I update combination "product1Black" content with following values for all shops:
      | meta_title[en-US] | Black T-shirt |
    Then combination "product1Black" should have following content for shop "shop1":
      | meta_title[en-US] | Black T-shirt |
    And combination "product1Black" should have following content for shop "shop2":
      | meta_title[en-US]        | Black T-shirt         |
      | description_short[en-US] | <p>Shop 2 summary</p> |
