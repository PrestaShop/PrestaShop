# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-images
@reset-database-before-feature
@clear-cache-before-feature
@reset-img-after-feature
@product-combination
@update-combination-images
Feature: Associate combination image from Back Office (BO)
  As an employee I need to be able to associate combination images

  Background:
    Given language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists
    And following image types should be applicable to products:
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
    And I add new image "image1" named "app_icon.png" to product "product1"
    And I add new image "image2" named "logo.jpg" to product "product1"
    And I add new image "image3" named "app_icon.png" to product "product1"
    And I add new image "image4" named "logo.jpg" to product "product1"
    And product "product1" should have following images:
      | image reference | is cover | legend[en-US] | position |
      | image1          | true     |               | 1        |
      | image2          | false    |               | 2        |
      | image3          | false    |               | 3        |
      | image4          | false    |               | 4        |
    And images "[image1, image2, image3, image4]" should have following types generated:
      | name           | width | height |
      | cart_default   | 125   | 125    |
      | home_default   | 250   | 250    |
      | large_default  | 800   | 800    |
      | medium_default | 452   | 452    |
      | small_default  | 98    | 98     |

  Scenario: I associate images to a combination
    Given I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product product1 type should be combinations
    And I generate combinations for product product1 using following attributes:
      | Size  | [S,M]              |
      | Color | [White,Black] |
    And product "product1" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default |
      | product1SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       |
      | product1SBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      |
      | product1MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      |
      | product1MBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      |
    And combination "product1SWhite" should have no images
    When I associate "[image1, image2]" to combination "product1SWhite"
    Then combination "product1SWhite" should have following images "[image1, image2]"
    When I remove all images associated to combination "product1SWhite"
    And combination "product1SWhite" should have no images
