# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags set-multishop-images-for-all-shops
@restore-products-before-feature
@clear-cache-before-feature
@reset-img-after-feature
@restore-shops-after-feature
@product-image
@set-multishop-images-for-all-shops
Feature: Set product images for all shops from Back Office (BO)
  As an employee I need to be able to apply a matrix of images in terms of shops

  Background:
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And I add a shop "shop3" with name "default_shop_group" and color "green" for the group "default_shop_group"
    And single shop context is loaded
    And I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And product "product1" type should be standard
    And I add new image "image1" named "app_icon.png" to product "product1" for shop "shop1"
    And I add new image "image2" named "some_image.jpg" to product "product1" for shop "shop1"
    And I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And I add new image "image3" named "logo.jpg" to product "product1" for shop "shop2"
    And I set following shops for product "product1":
      | source shop | shop2             |
      | shops       | shop1,shop2,shop3 |
    Then product "product1" should have following images for shop "shop1,shop2,shop3":
      | image reference | position | shops               |
      | image1          | 1        | shop1, shop2, shop3 |
      | image2          | 2        | shop1, shop2, shop3 |
      | image3          | 3        | shop2, shop3        |
    And following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |

  Scenario: I remove an image from a shop and I associate it to another at the same time
    When I apply the following matrix of images for product "product1":
      | imageReference | shopReferences      |
      | image1         | shop1, shop2, shop3 |
      | image2         | shop1, shop2, shop3 |
      | image3         | shop1, shop2        |
    Then product "product1" should have following images for shops "shop1, shop2, shop3":
      | image reference | position | shops               |
      | image1          | 1        | shop1, shop2, shop3 |
      | image2          | 2        | shop1, shop2, shop3 |
      | image3          | 3        | shop1, shop2        |

  Scenario: Remove an image for all shops  should remove the image physically and recalculate position
    When I apply the following matrix of images for product "product1":
      | imageReference | shopReferences      |
      | image1         | shop1, shop2, shop3 |
      | image3         | shop2, shop3        |
    Then product "product1" should have following images for shop "shop1, shop2, shop3":
      | image reference | position | shops               |
      | image1          | 1        | shop1, shop2, shop3 |
      | image3          | 2        | shop2,shop3         |
    And following types for image "image2" should be removed:
      | name           |
      | cart_default   |
      | home_default   |
      | large_default  |
      | medium_default |
      | small_default  |

  Scenario: Remove an image which is a cover should raise an error
    When I apply the following matrix of images for product "product1":
      | imageReference | shopReferences      |
      | image2         | shop1, shop2, shop3 |
      | image3         | shop2, shop3        |
    Then I should get an error that you cannot remove an image which is a cover
