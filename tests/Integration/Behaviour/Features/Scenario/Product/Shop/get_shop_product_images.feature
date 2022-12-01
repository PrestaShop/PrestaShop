# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags get-shop-product-images
@restore-products-before-feature
@clear-cache-before-feature
@reset-img-after-feature
@product-image
@get-shop-product-images
Feature: Get every image details for a product in every shop

  Background:
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I add a shop "shop3" with name "default_shop_group" and color "green" for the group "default_shop_group"
    And single shop context is loaded
    And I add product "product1" with following information:
      | name[en-US] | bottle of water |
      | type        | standard       |
    And product "product1" type should be standard
    And I add new image "image1" named "app_icon.png" to product "product1" for shop "shop1"
    And I add new image "image2" named "some_image.jpg" to product "product1" for shop "shop1"
    And I copy product product1 from shop shop1 to shop shop2
    And I add new image "image3" named "logo.jpg" to product "product1" for shop "shop2"
    And I copy product product1 from shop shop2 to shop shop3
    Then product "product1" should have following images for shop "shop1":
      | image reference |  position | shops               |
      | image1          |  1        | shop1, shop2, shop3 |
      | image2          |  2        | shop1, shop2, shop3 |
    And product "product1" should have following images for shop "shop2":
      | image reference |  position | shops               |
      | image1          |  1        | shop1, shop2, shop3 |
      | image2          |  2        | shop1, shop2, shop3 |
      | image3          |  3        | shop2, shop3        |
    And product "product1" should have following images for shop "shop3":
      | image reference |  position | shops               |
      | image1          |  1        | shop1, shop2, shop3 |
      | image2          |  2        | shop1, shop2, shop3 |
      | image3          |  3        | shop2, shop3        |
    And following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |

  Scenario: Get every image details for a product in every shop successfully
    When I try to get every image details for product "product1" in every shop:
    Then I should have the followings image details for shop "shop1":
      | image reference |  cover |
      | image1          |  1     |
      | image2          |  0     |
    And I should have the followings image details for shop "shop2":
      | image reference |  cover |
      | image1          |  0     |
      | image2          |  0     |
      | image3          |  0     |
    And I should have the followings image details for shop "shop3":
      | image reference |  cover |
      | image1          |  0     |
      | image2          |  0     |
      | image3          |  0     |
