# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags delete-image-multishop
@restore-products-before-feature
@clear-cache-before-feature
@reset-img-after-feature
@product-image
@delete-image-multishop
Feature: Delete product image from Back Office (BO)
  As an employee I need to be able to delete product image

  Background: Add new product image
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    Given I add a shop "shop3" with name "default_shop_group" and color "green" for the group "default_shop_group"
    And single shop context is loaded
    Given following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |
    And I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And product "product1" type should be standard
    And product "product1" should have no images
    And I add new image "image1" named "app_icon.png" to product "product1" for shop "shop1"
    And I set following shops for product "product1":
      | source shop | shop1             |
      | shops       | shop1,shop2,shop3 |
    And I add new image "image2" named "logo.jpg" to product "product1" for shop "shop1"
    And I add new image "image3" named "logo.jpg" to product "product1" for shop "shop2"
      | cover | true |
    And product "product1" should have following images for shop "shop1, shop2, shop3":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      | shops               |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop1, shop2, shop3 |
      | image2          | false    |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1               |
      | image3          | false    |               | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg | shop2               |
    And images "[image1, image2, image3]" should have following types generated:
      | name           | width | height |
      | cart_default   | 125   | 125    |
      | home_default   | 250   | 250    |
      | large_default  | 800   | 800    |
      | medium_default | 452   | 452    |
      | small_default  | 98    | 98     |

  Scenario: I delete an image which
  is cover in some shops, then a new cover should be set
  for every shops which doesn't have a cover anymore. the new cover should be the first image from
  the remaining images ordered by position
    When I delete product image "image1"
    And product "product1" should have following images for shop "shop1":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      | shops |
      | image2          | true     |               | 1        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1 |
      | image3          | false    |               | 2        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg | shop2 |
    And product "product1" should have following images for shop "shop2":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      | shops |
      | image2          | false    |               | 1        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1 |
      | image3          | true     |               | 2        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg | shop2 |
    And following types for image "image1" should be removed:
      | name           |
      | cart_default   |
      | home_default   |
      | large_default  |
      | medium_default |
      | small_default  |
