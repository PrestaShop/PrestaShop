# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags delete-image
@reset-database-before-feature
@clear-cache-before-feature
@reset-img-after-feature
@delete-image
Feature: Add product image from Back Office (BO)
  As an employee I need to be able to add new product image

  Scenario: Delete product image
    Given following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |
    And I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | is_virtual  | false          |
    And product "product1" type should be standard
    And product "product1" should have no images
    When I add new image "image1" named "app_icon.png" to product "product1"
    Then product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position |
      | image1          | true     |               | 1        |
    Then product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position |
      | image1          | true     |               | 1        |
    And images "[image1]" should have following types generated:
      | name           | width | height |
      | cart_default   | 125   | 125    |
      | home_default   | 250   | 250    |
      | large_default  | 800   | 800    |
      | medium_default | 452   | 452    |
      | small_default  | 98    | 98     |
    When I delete product image "image1"
    Then product "product1" should have no images
    And following types for image "image1" should be removed:
      | name           |
      | cart_default   |
      | home_default   |
      | large_default  |
      | medium_default |
      | small_default  |
