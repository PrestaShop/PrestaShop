# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags search-products
@restore-products-before-feature
@restore-languages-after-feature
@reset-img-after-feature
@clear-cache-before-feature
@search-products
Feature: Search products to associate them in the BO
  As an employee
  I need to be able to search for products in the BO to associate them

  Background:
    Given language "english" with locale "en-US" exists
    And language "french" with locale "fr-FR" exists
    And language with iso code "en" is the default one

  Scenario: I can search products by name
    When I add product "product1" with following information:
      | name[en-US] | bottle of beer     |
      | name[fr-FR] | bouteille de biere |
      | type        | standard           |
    And I add product "product2" with following information:
      | name[en-US] | bottle of cider    |
      | name[fr-FR] | bouteille de cidre |
      | type        | standard           |
    When I search for products with locale "english" matching "beer" I should get following results:
      | product  | name           | reference | image url                                             |
      | product1 | bottle of beer |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "bOtT" I should get following results:
      | product  | name            | reference | image url                                             |
      | product1 | bottle of beer  |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product2 | bottle of cider |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "french" matching "biere" I should get following results:
      | product  | name               | reference | image url                                             |
      | product1 | bouteille de biere |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "french" matching "BoU" I should get following results:
      | product  | name               | reference | image url                                             |
      | product1 | bouteille de biere |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product2 | bouteille de cidre |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "french" matching "beer" I should get no results
    And I search for products with locale "english" matching "biere" I should get no results

  Scenario: I can search products by references
    When I add product "product3" with following information:
      | name[en-US] | bottle of champaign    |
      | name[fr-FR] | bouteille de champagne |
      | type        | standard               |
    When I search for products with locale "english" matching "978-3-16-148410-0" I should get no results
    And I search for products with locale "english" matching "72527273070" I should get no results
    And I search for products with locale "english" matching "978020137962" I should get no results
    And I search for products with locale "english" matching "mpn1" I should get no results
    And I search for products with locale "english" matching "ref1" I should get no results
    When I update product "product3" with following values:
      | isbn      | 978-3-16-148410-0 |
      | upc       | 72527273070       |
      | ean13     | 978020137962      |
      | mpn       | mpn1              |
      | reference | ref1              |
    Then product "product3" should have following details:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    # Search by all types of references matching product3
    When I search for products with locale "english" matching "978-3-16-148410-0" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "72527273070" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "978020137962" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "mpn1" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "ref1" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |

  Scenario: I can search products by combination references
    Given I add product "product4" with following information:
      | name[en-US] | bottle of wine   |
      | name[fr-FR] | bouteille de vin |
      | type        | combinations     |
    And attribute group "Color" named "Color" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Red" named "Red" in en language exists
    And attribute "Pink" named "Pink" in en language exists
    And I generate combinations for product product4 using following attributes:
      | Color | [Red,White,Pink] |
    And product "product4" should have following combinations:
      | id reference  | combination name | reference | attributes    | impact on price | quantity | is default |
      | product4Red   | Color - Red      |           | [Color:Red]   | 0               | 0        | true       |
      | product4White | Color - White    |           | [Color:White] | 0               | 0        | false      |
      | product4Pink  | Color - Pink     |           | [Color:Pink]  | 0               | 0        | false      |
    When I search for products with locale "english" matching "154867313573" I should get no results
    And I search for products with locale "english" matching "978-3-16-148410-3" I should get no results
    And I search for products with locale "english" matching "mpn3red" I should get no results
    And I search for products with locale "english" matching "ref3red" I should get no results
    And I search for products with locale "english" matching "137684192354" I should get no results
    And I search for products with locale "english" matching "1357321357213" I should get no results
    And I search for products with locale "english" matching "978-3-16-148410-4" I should get no results
    And I search for products with locale "english" matching "mpn3white" I should get no results
    And I search for products with locale "english" matching "ref3white" I should get no results
    And I search for products with locale "english" matching "3543213543213" I should get no results
    When I update combination "product4Red" with following values:
      | ean13     | 154867313573      |
      | isbn      | 978-3-16-148410-3 |
      | mpn       | mpn3red           |
      | reference | ref3red           |
      | upc       | 137684192354      |
    And I update combination "product4White" with following values:
      | ean13     | 1357321357213     |
      | isbn      | 978-3-16-148410-4 |
      | mpn       | mpn3white         |
      | reference | ref3white         |
      | upc       | 354321354321      |
    Then combination "product4Red" should have following details:
      | combination detail | value             |
      | ean13              | 154867313573      |
      | isbn               | 978-3-16-148410-3 |
      | mpn                | mpn3red           |
      | reference          | ref3red           |
      | upc                | 137684192354      |
    Then combination "product4White" should have following details:
      | combination detail | value             |
      | ean13              | 1357321357213     |
      | isbn               | 978-3-16-148410-4 |
      | mpn                | mpn3white         |
      | reference          | ref3white         |
      | upc                | 354321354321      |
    # Search by all types of references matching product4Red combination
    When I search for products with locale "english" matching "154867313573" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "978-3-16-148410-3" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "mpn3red" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "ref3red" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "137684192354" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    # Search by all types of references matching product4White combination
    When I search for products with locale "english" matching "1357321357213" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "978-3-16-148410-4" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "mpn3white" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "ref3white" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "354321354321" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    # Search by types that match both combinations, only one product is returned
    When I search for products with locale "english" matching "mpn3" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "ref3" I should get following results:
      | product  | name           | reference | image url                                             |
      | product4 | bottle of wine |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    # Search by types that match two products
    When I search for products with locale "english" matching "mpn" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | bottle of wine      |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for products with locale "english" matching "ref" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | bottle of wine      |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |

  Scenario: Search results include the appropriate images
    Given following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |
    When I add product "product5" with following information:
      | name[en-US] | can of lemonade     |
      | name[fr-FR] | canette de limonade |
      | type        | standard            |
    And I add product "product6" with following information:
      | name[en-US] | can of coke     |
      | name[fr-FR] | canette de coca |
      | type        | standard        |
    # Note: search results are ordered by name
    When I search for products with locale "english" matching "can" I should get following results:
      | product  | name            | reference | image url                                             |
      | product6 | can of coke     |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product5 | can of lemonade |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And product "product6" should have no images
    When I add new image "image1" named "app_icon.png" to product "product6"
    And I add new image "image2" named "logo.jpg" to product "product6"
    And I update image "image2" with following information:
      | cover | true |
    Then product "product6" should have following images:
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      |
      | image1          | false    |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | true     |               |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
    # Search returns the cover image url when present
    When I search for products with locale "english" matching "can" I should get following results:
      | product  | name            | reference | image url                                             |
      | product6 | can of coke     |           | http://myshop.com/img/p/{image2}-home_default.jpg     |
      | product5 | can of lemonade |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
