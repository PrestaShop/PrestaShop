# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags related-products
@restore-products-before-feature
@reset-img-after-feature
@clear-cache-before-feature
@related-products
Feature: Update product related products from Back Office (BO)
  As an employee
  I need to be able to update related products of a product from Back Office

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Color" named "Color" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists

  Scenario: I set related products
    Given I add product "product1" with following information:
      | name[en-US] | book of law |
      | type        | standard    |
    And I add product "product2" with following information:
      | name[en-US] | book of love |
      | type        | standard    |
    And I add product "product3" with following information:
      | name[en-US] | lovely books package |
      | type        | pack                 |
    And I update pack "product3" with following product quantities:
      | product  | quantity |
      | product2 | 5        |
    And I add product "product4" with following information:
      | name[en-US] | Reading glasses |
      | type        | combinations    |
    When I generate combinations for product product4 using following attributes:
      | Color | [White,Black] |
    Then product "product4" should have following combinations:
      | id reference | combination name | reference | attributes    | impact on price | quantity | is default |
      | whiteFramed  | Color - White    |           | [Color:White] | 0               | 0        | true       |
      | blackFramed  | Color - Black    |           | [Color:Black] | 0               | 0        | false      |
    And product product1 should have no related products
    And product product2 type should be standard
    And product "product3" type should be pack
    And product product4 type should be combinations
    When I set following related products to product product1:
      | product2 |
      | product3 |
      | product4 |
    Then product product1 should have following related products:
      | product  | name                 | reference | image url                                              |
      | product2 | book of love         |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product3 | lovely books package |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | Reading glasses      |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    When I set following related products to product product1:
      | product2 |
      | product4 |
    Then product product1 should have following related products:
      | product  | name            | reference | image url                                              |
      | product2 | book of love    |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | Reading glasses |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |

  Scenario: Check product reference
    Given product product1 should have following related products:
      | product  | name            | reference | image url                                              |
      | product2 | book of love    |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | Reading glasses |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    When I update product "product2" with following values:
      | reference | ref2              |
    Then product product1 should have following related products:
      | product  | name            | reference | image url                                              |
      | product2 | book of love    | ref2      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | Reading glasses |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |

  Scenario: Check product image
    Given following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |
    And product product1 should have following related products:
      | product  | name            | reference | image url                                              |
      | product2 | book of love    | ref2      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | Reading glasses |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And product "product4" should have no images
    When I add new image "image1" named "app_icon.png" to product "product4"
    And I add new image "image2" named "logo.jpg" to product "product4"
    And I update image "image2" with following information:
      | cover | true |
    Then product "product4" should have following images:
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | false    |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | true     |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
    And product product1 should have following related products:
      | product  | name            | reference | image url                                              |
      | product2 | book of love    | ref2      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | Reading glasses |           | http://myshop.com/img/p/{image2}-home_default.jpg     |

  Scenario: Remove all related products
    Given product product1 should have following related products:
      | product  | name            | reference | image url                                              |
      | product2 | book of love    | ref2      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | Reading glasses |           | http://myshop.com/img/p/{image2}-home_default.jpg      |
    When I remove all related products from product product1
    Then product product1 should have no related products
