# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add-image-multishop
@restore-products-before-feature
@clear-cache-before-feature
@reset-img-after-feature
@restore-shops-after-feature
@product-image
@add-image-multishop
Feature: Add product image from Back Office (BO)
  As an employee I need to be able to add new product image in multishop context

  Background:
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And single shop context is loaded
    And I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | standard       |
    And product "product1" type should be standard
    When I set following shops for product "product1":
      | source shop | shop1       |
      | shops       | shop1,shop2 |
    And following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |
    And product "product1" should have no images

  Scenario: Add new product image for specific shop
    When I add new image "image1" named "app_icon.png" to product "product1" for shop "shop1"
    Then image "image1" should have same file as "app_icon.png"
    And product "product1" should have following images for shop "shop1":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      | shops |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop1 |
    When I add new image "image2" named "logo.jpg" to product "product1" for shop "shop1"
    Then image "image2" should have same file as "logo.jpg"
    And product "product1" should have following images for shop "shop1":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      | shops |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop1 |
      | image2          | false    |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1 |
    And images "[image1, image2]" should have following types generated:
      | name           | width | height |
      | cart_default   | 125   | 125    |
      | home_default   | 250   | 250    |
      | large_default  | 800   | 800    |
      | medium_default | 452   | 452    |
      | small_default  | 98    | 98     |
    And product "product1" should have following images for shop "shop2":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      | shops |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop1 |
      | image2          | false    |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1 |
    When I add new image "image3" named "logo.jpg" to product "product1" for shop "shop2"
    And product "product1" should have following images for shop "shop2":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      | shops |
      | image1          | false    |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop1 |
      | image2          | false    |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1 |
      | image3          | true     |               | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg | shop2 |

  Scenario: Add new product image for all shops
    When I add new image "image1" named "app_icon.png" to product "product1" for all shops
    Then image "image1" should have same file as "app_icon.png"
    And product "product1" should have following images for shops "shop1,shop2":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      | shops       |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop1,shop2 |
    When I add new image "image2" named "logo.jpg" to product "product1" for all shops
    Then image "image2" should have same file as "logo.jpg"
    And product "product1" should have following images for shops "shop1,shop2":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      | shops       |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg | shop1,shop2 |
      | image2          | false    |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg | shop1,shop2 |
    And images "[image1, image2]" should have following types generated:
      | name           | width | height |
      | cart_default   | 125   | 125    |
      | home_default   | 250   | 250    |
      | large_default  | 800   | 800    |
      | medium_default | 452   | 452    |
      | small_default  | 98    | 98     |

  Scenario: All shops and shop group constraints are not supported when retrieving product images
    When I try to get product "product1" images for all shops
    Then I should get error that shop constraint is invalid
    When I try to get product "product1" images for shop group "default_shop_group"
    Then I should get error that shop constraint is invalid
