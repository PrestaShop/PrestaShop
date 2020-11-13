# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags add-image
@reset-database-before-feature
@clear-cache-before-feature
@reset-img-after-feature
@add-image
Feature: Add product image from Back Office (BO)
  As an employee I need to be able to add new product image

  Scenario: Add new product image
    Given I add product "product1" with following information:
      | name       | en-US:bottle of beer |
      | is_virtual | false                |
    And product "product1" type should be standard
    And product "product1" should have no images
    When I add new product "product1" image "image1" named "app_icon.png"
    Then product "product1" should have following images:
      | image reference | is cover | legend | position |
      | image1          | true     | en-US: | 1        |
    When I add new product "product1" image "image2" named "logo.jpg"
    Then product "product1" should have following images:
      | image reference | is cover | legend | position |
      | image1          | true     | en-US: | 1        |
      | image2          | false    | en-US: | 2        |

#todo: assert image types (which is not present in dummy database), assert combination & pack images
