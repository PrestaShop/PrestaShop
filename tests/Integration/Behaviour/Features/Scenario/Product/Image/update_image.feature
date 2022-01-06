# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-image
@restore-products-before-feature
@clear-cache-before-feature
@reset-img-after-feature
@product-image
@update-image
Feature: Update product image from Back Office (BO)
  As an employee I need to be able to update new product image

  Background: Add new product image
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
    And product "product1" should have following cover "http://myshop.com/img/p/{no_picture}-cart_default.jpg"
    When I add new image "image1" named "app_icon.png" to product "product1"
    When I add new image "image2" named "logo.jpg" to product "product1"
    When I add new image "image3" named "app_icon.png" to product "product1"
    When I add new image "image4" named "logo.jpg" to product "product1"
    When I add new image "image5" named "app_icon.png" to product "product1"
    When I add new image "image6" named "logo.jpg" to product "product1"
    And product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | false    |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
      | image3          | false    |               | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg |
      | image4          | false    |               | 4        | http://myshop.com/img/p/{image4}.jpg | http://myshop.com/img/p/{image4}-small_default.jpg |
      | image5          | false    |               | 5        | http://myshop.com/img/p/{image5}.jpg | http://myshop.com/img/p/{image5}-small_default.jpg |
      | image6          | false    |               | 6        | http://myshop.com/img/p/{image6}.jpg | http://myshop.com/img/p/{image6}-small_default.jpg |
    And product "product1" should have following cover "http://myshop.com/img/p/{image1}-cart_default.jpg"
    And images "[image1, image2, image3, image4, image5, image6]" should have following types generated:
      | name           | width | height |
      | cart_default   | 125   | 125    |
      | home_default   | 250   | 250    |
      | large_default  | 800   | 800    |
      | medium_default | 452   | 452    |
      | small_default  | 98    | 98     |

  Scenario: I update image file
    When I update image "image1" with following information:
      | file | logo.jpg |
    Then image "image1" should have same file as "logo.jpg"

  Scenario: I update image legend
    When I update image "image1" with following information:
      | legend[en-US] | preston is alive |
    Then product "product1" should have following images:
      | image reference | is cover | legend[en-US]    | position | image url                            | thumbnail url                                      |
      | image1          | true     | preston is alive | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | false    |                  | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
      | image3          | false    |                  | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg |
      | image4          | false    |                  | 4        | http://myshop.com/img/p/{image4}.jpg | http://myshop.com/img/p/{image4}-small_default.jpg |
      | image5          | false    |                  | 5        | http://myshop.com/img/p/{image5}.jpg | http://myshop.com/img/p/{image5}-small_default.jpg |
      | image6          | false    |                  | 6        | http://myshop.com/img/p/{image6}.jpg | http://myshop.com/img/p/{image6}-small_default.jpg |
    And product "product1" should have following cover "http://myshop.com/img/p/{image1}-cart_default.jpg"

  Scenario: I update image cover
    When I update image "image2" with following information:
      | cover | true |
    Then product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | false    |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | true     |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
      | image3          | false    |               | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg |
      | image4          | false    |               | 4        | http://myshop.com/img/p/{image4}.jpg | http://myshop.com/img/p/{image4}-small_default.jpg |
      | image5          | false    |               | 5        | http://myshop.com/img/p/{image5}.jpg | http://myshop.com/img/p/{image5}-small_default.jpg |
      | image6          | false    |               | 6        | http://myshop.com/img/p/{image6}.jpg | http://myshop.com/img/p/{image6}-small_default.jpg |
    And product "product1" should have following cover "http://myshop.com/img/p/{image2}-cart_default.jpg"
    # Set cover false just to check it does not force the cover (it happened ^^)
    When I update image "image1" with following information:
      | legend[en-US] | preston is alive |
      | cover         | false            |
    Then product "product1" should have following images:
      | image reference | is cover | legend[en-US]    | position | image url                            | thumbnail url                                      |
      | image1          | false    | preston is alive | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | true     |                  | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
      | image3          | false    |                  | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg |
      | image4          | false    |                  | 4        | http://myshop.com/img/p/{image4}.jpg | http://myshop.com/img/p/{image4}-small_default.jpg |
      | image5          | false    |                  | 5        | http://myshop.com/img/p/{image5}.jpg | http://myshop.com/img/p/{image5}-small_default.jpg |
      | image6          | false    |                  | 6        | http://myshop.com/img/p/{image6}.jpg | http://myshop.com/img/p/{image6}-small_default.jpg |

  Scenario: I update image positions
    When I update image "image2" with following information:
      | position | 5 |
    Then product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image3          | false    |               | 2        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg |
      | image4          | false    |               | 3        | http://myshop.com/img/p/{image4}.jpg | http://myshop.com/img/p/{image4}-small_default.jpg |
      | image5          | false    |               | 4        | http://myshop.com/img/p/{image5}.jpg | http://myshop.com/img/p/{image5}-small_default.jpg |
      | image2          | false    |               | 5        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
      | image6          | false    |               | 6        | http://myshop.com/img/p/{image6}.jpg | http://myshop.com/img/p/{image6}-small_default.jpg |
    And product "product1" should have following cover "http://myshop.com/img/p/{image1}-cart_default.jpg"
    When I update image "image6" with following information:
      | position | 2 |
    Then product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image1          | true     |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image6          | false    |               | 2        | http://myshop.com/img/p/{image6}.jpg | http://myshop.com/img/p/{image6}-small_default.jpg |
      | image3          | false    |               | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg |
      | image4          | false    |               | 4        | http://myshop.com/img/p/{image4}.jpg | http://myshop.com/img/p/{image4}-small_default.jpg |
      | image5          | false    |               | 5        | http://myshop.com/img/p/{image5}.jpg | http://myshop.com/img/p/{image5}-small_default.jpg |
      | image2          | false    |               | 6        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
    And product "product1" should have following cover "http://myshop.com/img/p/{image1}-cart_default.jpg"
    When I update image "image1" with following information:
      | position | 2 |
    Then product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image6          | false    |               | 1        | http://myshop.com/img/p/{image6}.jpg | http://myshop.com/img/p/{image6}-small_default.jpg |
      | image1          | true     |               | 2        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image3          | false    |               | 3        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg |
      | image4          | false    |               | 4        | http://myshop.com/img/p/{image4}.jpg | http://myshop.com/img/p/{image4}-small_default.jpg |
      | image5          | false    |               | 5        | http://myshop.com/img/p/{image5}.jpg | http://myshop.com/img/p/{image5}-small_default.jpg |
      | image2          | false    |               | 6        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
    And product "product1" should have following cover "http://myshop.com/img/p/{image1}-cart_default.jpg"
    When I update image "image1" with following information:
      | position | 42 |
    Then product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image6          | false    |               | 1        | http://myshop.com/img/p/{image6}.jpg | http://myshop.com/img/p/{image6}-small_default.jpg |
      | image3          | false    |               | 2        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg |
      | image4          | false    |               | 3        | http://myshop.com/img/p/{image4}.jpg | http://myshop.com/img/p/{image4}-small_default.jpg |
      | image5          | false    |               | 4        | http://myshop.com/img/p/{image5}.jpg | http://myshop.com/img/p/{image5}-small_default.jpg |
      | image2          | false    |               | 5        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
      | image1          | true     |               | 6        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
    And product "product1" should have following cover "http://myshop.com/img/p/{image1}-cart_default.jpg"
    When I update image "image3" with following information:
      | position | -8000 |
    Then product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position | image url                            | thumbnail url                                      |
      | image3          | false    |               | 1        | http://myshop.com/img/p/{image3}.jpg | http://myshop.com/img/p/{image3}-small_default.jpg |
      | image6          | false    |               | 2        | http://myshop.com/img/p/{image6}.jpg | http://myshop.com/img/p/{image6}-small_default.jpg |
      | image4          | false    |               | 3        | http://myshop.com/img/p/{image4}.jpg | http://myshop.com/img/p/{image4}-small_default.jpg |
      | image5          | false    |               | 4        | http://myshop.com/img/p/{image5}.jpg | http://myshop.com/img/p/{image5}-small_default.jpg |
      | image2          | false    |               | 5        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
      | image1          | true     |               | 6        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
    And product "product1" should have following cover "http://myshop.com/img/p/{image1}-cart_default.jpg"
