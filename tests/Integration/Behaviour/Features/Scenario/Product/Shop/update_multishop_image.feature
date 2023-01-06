# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-multishop-image
@restore-products-before-feature
@clear-cache-before-feature
@reset-img-after-feature
@product-image
@update-multishop-image
Feature: Update product image from Back Office (BO)
  As an employee I need to be able to update new product image

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
    And I copy product product1 from shop shop1 to shop shop2
    And I copy product product1 from shop shop1 to shop shop3
    And product "product1" should have following cover "http://myshop.com/img/p/{no_picture}-cart_default.jpg" for shops "shop1"
    And product "product1" should have following cover "http://myshop.com/img/p/{no_picture}-cart_default.jpg" for shops "shop2"
    And product "product1" should have following cover "http://myshop.com/img/p/{no_picture}-cart_default.jpg" for shops "shop3"
    When I add new image "image1" named "app_icon.png" to product "product1" for shop "shop1"
    And I copy product product1 from shop shop1 to shop shop3
    When I add new image "image2" named "logo.jpg" to product "product1" for shop "shop1"
    And I copy product product1 from shop shop1 to shop shop2
    And product "product1" should have following images for shop "shop1, shop2":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | false    |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
    And product "product1" should have following images for shop "shop3":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
    And product "product1" should have following cover "http://myshop.com/img/p/{image1}-cart_default.jpg" for shops "shop1,shop2,shop3"
    And images "[image1, image2]" should have following types generated:
      | name           | width | height |
      | cart_default   | 125   | 125    |
      | home_default   | 250   | 250    |
      | large_default  | 800   | 800    |
      | medium_default | 452   | 452    |
      | small_default  | 98    | 98     |

  Scenario: I update image file
    When I update image "image1" with following information for shop "shop1":
      | file | logo.jpg |
    Then image "image1" should have same file as "logo.jpg"
    When I update image "image1" with following information for shop "shop2":
      | file | app_icon.png |
    Then image "image1" should have same file as "app_icon.png"

  Scenario: I update image legend
    When I update image "image1" with following information for shop "shop1":
      | legend[en-US] | preston is alive |
    Then product "product1" should have following images for shop "shop1, shop2":
      | image reference | is cover | legend[en-US]    | position | image url                            | thumbnail url                                      |
      | image1          | true     | preston is alive | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | false    |                  | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
    Then product "product1" should have following images for shop "shop3":
      | image reference | is cover | legend[en-US]    | position | image url                            | thumbnail url                                      |
      | image1          | true     | preston is alive | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
    When I update image "image1" with following information for shop "shop2":
      | legend[en-US] | preston is alive |
    Then product "product1" should have following images for shop "shop1, shop2":
      | image reference | is cover | legend[en-US]    | position | image url                            | thumbnail url                                      |
      | image1          | true     | preston is alive | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | false    |                  | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |

  Scenario: I update image cover
    When I update image "image2" with following information for shop "shop2":
      | cover | true |
    Then product "product1" should have following images for shop "shop1":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | false    |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
    Then product "product1" should have following images for shop "shop2":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | false    |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | true     |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |

    And product "product1" should have following cover "http://myshop.com/img/p/{image1}-cart_default.jpg" for shops "shop1"
    And product "product1" should have following cover "http://myshop.com/img/p/{image2}-cart_default.jpg" for shops "shop2"

  Scenario: I update image file for all shops
    When I update image "image2" with following information for all shops:
      | cover | true |
    Then product "product1" should have following images for shop "shop1, shop2":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | false    |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | true     |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
    Then product "product1" should have following images for shop "shop3":
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | true    |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
