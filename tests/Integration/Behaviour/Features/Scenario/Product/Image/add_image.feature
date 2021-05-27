# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add-image
@reset-database-before-feature
@clear-cache-before-feature
@reset-img-after-feature
@product-image
@add-image
Feature: Add product image from Back Office (BO)
  As an employee I need to be able to add new product image

  Scenario: Add new product image
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
    When I add new image "image1" named "app_icon.png" to product "product1"
    Then image "image1" should have same file as "app_icon.png"
    And product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
    When I add new image "image2" named "logo.jpg" to product "product1"
    Then image "image2" should have same file as "logo.jpg"
    And product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | false    |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
    And images "[image1, image2]" should have following types generated:
      | name           | width | height |
      | cart_default   | 125   | 125    |
      | home_default   | 250   | 250    |
      | large_default  | 800   | 800    |
      | medium_default | 452   | 452    |
      | small_default  | 98    | 98     |
