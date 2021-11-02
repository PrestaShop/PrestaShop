# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags search-combinations
@reset-database-before-feature
@clear-cache-before-feature
@search-combinations
Feature: Search combinations to associate them in the BO
  As an employee
  I need to be able to search for combinations in the BO to associate them

  Background:
    Given language "english" with locale "en-US" exists
    And language "french" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And attribute group "Color" named "Color" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Red" named "Red" in en language exists
    And attribute "Pink" named "Pink" in en language exists
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And attribute "Blue" named "Blue" in en language exists
    And attribute "Red" named "Red" in en language exists

  Scenario: I can search combinations by name
    When I add product "beer_bottle" with following information:
      | name[en-US] | bottle of beer     |
      | name[fr-FR] | bouteille de biere |
      | type        | standard           |
    And I add product "product2" with following information:
      | name[en-US] | bottle of cider    |
      | name[fr-FR] | bouteille de cidre |
      | type        | standard           |
    When I search for combinations with locale "english" matching "beer" I should get following results:
      | product  | name           | reference | image url                                             |
      | beer_bottle | bottle of beer |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "bOtT" I should get following results:
      | product  | name            | reference | image url                                             |
      | beer_bottle | bottle of beer  |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product2 | bottle of cider |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "french" matching "biere" I should get following results:
      | product  | name               | reference | image url                                             |
      | beer_bottle | bouteille de biere |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "french" matching "BoU" I should get following results:
      | product  | name               | reference | image url                                             |
      | beer_bottle | bouteille de biere |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product2 | bouteille de cidre |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "french" matching "beer" I should get no results
    And I search for combinations with locale "english" matching "biere" I should get no results

  Scenario: I can search combinations by references
    When I add product "product3" with following information:
      | name[en-US] | bottle of champaign    |
      | name[fr-FR] | bouteille de champagne |
      | type        | standard               |
    When I search for combinations with locale "english" matching "978-3-16-148410-0" I should get no results
    And I search for combinations with locale "english" matching "72527273070" I should get no results
    And I search for combinations with locale "english" matching "978020137962" I should get no results
    And I search for combinations with locale "english" matching "mpn1" I should get no results
    And I search for combinations with locale "english" matching "ref1" I should get no results
    When I update product "product3" details with following values:
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
    When I search for combinations with locale "english" matching "978-3-16-148410-0" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "72527273070" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "978020137962" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "mpn1" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "ref1" I should get following results:
      | product  | name                | reference | image url                                             |
      | product3 | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |

  Scenario: I can search combinations by combination references
    Given I add product "product4" with following information:
      | name[en-US] | bottle of wine   |
      | name[fr-FR] | bouteille de vin |
      | type        | combinations     |
    And I generate combinations for product product4 using following attributes:
      | Color | [Red,White,Pink] |
    And product "product4" should have following combinations:
      | id reference  | combination name | reference | attributes    | impact on price | quantity | is default |
      | product4Red   | Color - Red      |           | [Color:Red]   | 0               | 0        | true       |
      | product4White | Color - White    |           | [Color:White] | 0               | 0        | false      |
      | product4Pink  | Color - Pink     |           | [Color:Pink]  | 0               | 0        | false      |
    When I search for combinations with locale "english" matching "154867313573" I should get no results
    And I search for combinations with locale "english" matching "978-3-16-148410-3" I should get no results
    And I search for combinations with locale "english" matching "mpn3red" I should get no results
    And I search for combinations with locale "english" matching "ref3red" I should get no results
    And I search for combinations with locale "english" matching "137684192354" I should get no results
    And I search for combinations with locale "english" matching "1357321357213" I should get no results
    And I search for combinations with locale "english" matching "978-3-16-148410-4" I should get no results
    And I search for combinations with locale "english" matching "mpn3white" I should get no results
    And I search for combinations with locale "english" matching "ref3white" I should get no results
    And I search for combinations with locale "english" matching "3543213543213" I should get no results
    When I update combination "product4Red" details with following values:
      | ean13            | 154867313573      |
      | isbn             | 978-3-16-148410-3 |
      | mpn              | mpn3red           |
      | reference        | ref3red           |
      | upc              | 137684192354      |
    And I update combination "product4White" details with following values:
      | ean13            | 1357321357213     |
      | isbn             | 978-3-16-148410-4 |
      | mpn              | mpn3white         |
      | reference        | ref3white         |
      | upc              | 354321354321      |
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
      | upc                | 354321354321     |
    # Search by all types of references matching product4Red combination
    When I search for combinations with locale "english" matching "154867313573" I should get following results:
      | product  | combination | name           | reference | image url                                             |
      | product4 | product4Red | bottle of wine | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "978-3-16-148410-3" I should get following results:
      | product  | combination | name           | reference | image url                                             |
      | product4 | product4Red | bottle of wine | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "mpn3red" I should get following results:
      | product  | combination | name           | reference | image url                                             |
      | product4 | product4Red | bottle of wine | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "ref3red" I should get following results:
      | product  | combination | name           | reference | image url                                             |
      | product4 | product4Red | bottle of wine | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "137684192354" I should get following results:
      | product  | combination | name           | reference | image url                                             |
      | product4 | product4Red | bottle of wine | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    # Search by all types of references matching product4White combination
    When I search for combinations with locale "english" matching "1357321357213" I should get following results:
      | product  | combination   | name           | reference | image url                                             |
      | product4 | product4White | bottle of wine | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "978-3-16-148410-4" I should get following results:
      | product  | combination   | name           | reference | image url                                             |
      | product4 | product4White | bottle of wine | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "mpn3white" I should get following results:
      | product  | combination   | name           | reference | image url                                             |
      | product4 | product4White | bottle of wine | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "ref3white" I should get following results:
      | product  | combination   | name           | reference | image url                                             |
      | product4 | product4White | bottle of wine | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "354321354321" I should get following results:
      | product  | combination   | name           | reference | image url                                             |
      | product4 | product4White | bottle of wine | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    # Search by types that match both combinations, both are returned
    When I search for combinations with locale "english" matching "mpn3" I should get following results:
      | product  | combination   | name           | reference | image url                                             |
      | product4 | product4Red   | bottle of wine | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | product4White | bottle of wine | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "ref3" I should get following results:
      | product  | combination   | name           | reference | image url                                             |
      | product4 | product4Red   | bottle of wine | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | product4White | bottle of wine | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    # Search by types that match two combinations and a product
    When I search for combinations with locale "english" matching "mpn" I should get following results:
      | product  | combination   | name                | reference | image url                                             |
      | product3 |               | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | product4Red   | bottle of wine      | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | product4White | bottle of wine      | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "ref" I should get following results:
      | product  | combination   | name                | reference | image url                                             |
      | product3 |               | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | product4Red   | bottle of wine      | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product4 | product4White | bottle of wine      | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |

  Scenario: Search results include the appropriate images
    Given following image types should be applicable to products:
      | reference     | name           | width | height |
      | cartDefault   | cart_default   | 125   | 125    |
      | homeDefault   | home_default   | 250   | 250    |
      | largeDefault  | large_default  | 800   | 800    |
      | mediumDefault | medium_default | 452   | 452    |
      | smallDefault  | small_default  | 98    | 98     |
    When I add product "lemonade_can" with following information:
      | name[en-US] | can of lemonade     |
      | name[fr-FR] | canette de limonade |
      | type        | standard            |
    And I add product "coke_can" with following information:
      | name[en-US] | can of coke     |
      | name[fr-FR] | canette de coca |
      | type        | standard        |
    # Note: search results are ordered by name
    When I search for combinations with locale "english" matching "can" I should get following results:
      | product      | name            | reference | image url                                             |
      | coke_can     | can of coke     |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | lemonade_can | can of lemonade |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And product "coke_can" should have no images
    When I add new image "image1" named "app_icon.png" to product "coke_can"
    And I add new image "image2" named "logo.jpg" to product "coke_can"
    And I update image "image2" with following information:
      | cover | true |
    Then product "coke_can" should have following images:
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                            | thumbnail url                                      |
      | image1          | false    |               |               | 1        | http://myshop.com/img/p/{image1}.jpg | http://myshop.com/img/p/{image1}-small_default.jpg |
      | image2          | true     |               |               | 2        | http://myshop.com/img/p/{image2}.jpg | http://myshop.com/img/p/{image2}-small_default.jpg |
    # Search returns the cover image url when present
    When I search for combinations with locale "english" matching "can" I should get following results:
      | product      | name            | reference | image url                                             |
      | coke_can     | can of coke     |           | http://myshop.com/img/p/{image2}-home_default.jpg     |
      | lemonade_can | can of lemonade |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |

  Scenario: I associate images to a combination
    Given I add product "lemonTShirt" with following information:
      | name[en-US] | lemon t-shirt  |
      | name[fr-FR] | t-shirt citron |
      | type        | combinations   |
    And product lemonTShirt type should be combinations
    And I generate combinations for product lemonTShirt using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    And product "lemonTShirt" should have following combinations:
      | id reference      | combination name        | reference | attributes           | impact on price | quantity | is default | image url                                               |
      | lemonTShirtSWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | lemonTShirtSBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | lemonTShirtMWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | lemonTShirtMBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{no_picture}-small_default.jpg |
    # No image can be returned for both products
    When I search for combinations with locale "english" matching "lemon" I should get following results:
      | product      | combination       | name            | reference | image url                                             |
      | lemonade_can |                   | can of lemonade |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | lemonTShirt  | lemonTShirtSWhite | lemon t-shirt   |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | lemonTShirt  | lemonTShirtSBlack | lemon t-shirt   |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | lemonTShirt  | lemonTShirtMWhite | lemon t-shirt   |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | lemonTShirt  | lemonTShirtMBlack | lemon t-shirt   |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I add new image "lemonImage1" named "app_icon.png" to product "lemonTShirt"
    And I add new image "lemonImage2" named "logo.jpg" to product "lemonTShirt"
    And I add new image "lemonImage3" named "app_icon.png" to product "lemonTShirt"
    And I add new image "lemonImage4" named "logo.jpg" to product "lemonTShirt"
    # The fallback image is the first one from the product
    And product "lemonTShirt" should have following combinations:
      | id reference      | combination name        | reference | attributes           | impact on price | quantity | is default | image url                                               |
      | lemonTShirtSWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       | http://myshop.com/img/p/{lemonImage1}-small_default.jpg |
      | lemonTShirtSBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{lemonImage1}-small_default.jpg |
      | lemonTShirtMWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      | http://myshop.com/img/p/{lemonImage1}-small_default.jpg |
      | lemonTShirtMBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{lemonImage1}-small_default.jpg |
    # Search results follow the same principle
    When I search for combinations with locale "english" matching "lemon" I should get following results:
      | product      | combination       | name            | reference | image url                                              |
      | lemonade_can |                   | can of lemonade |           | http://myshop.com/img/p/{no_picture}-home_default.jpg  |
      | lemonTShirt  | lemonTShirtSWhite | lemon t-shirt   |           | http://myshop.com/img/p/{lemonImage1}-home_default.jpg |
      | lemonTShirt  | lemonTShirtSBlack | lemon t-shirt   |           | http://myshop.com/img/p/{lemonImage1}-home_default.jpg |
      | lemonTShirt  | lemonTShirtMWhite | lemon t-shirt   |           | http://myshop.com/img/p/{lemonImage1}-home_default.jpg |
      | lemonTShirt  | lemonTShirtMBlack | lemon t-shirt   |           | http://myshop.com/img/p/{lemonImage1}-home_default.jpg |
    And combination "lemonTShirtSWhite" should have no images
    When I associate "[lemonImage2,lemonImage3]" to combination "lemonTShirtSWhite"
    Then combination "lemonTShirtSWhite" should have following images "[lemonImage2,lemonImage3]"
    When I associate "[lemonImage4]" to combination "lemonTShirtMWhite"
    Then combination "lemonTShirtMWhite" should have following images "[lemonImage4]"
    When I associate "[lemonImage4,lemonImage3]" to combination "lemonTShirtMBlack"
    Then combination "lemonTShirtMBlack" should have following images "[lemonImage4,lemonImage3]"
    # Now the combination image is the first one in its own associated images (first by image creation oder)
    And product "lemonTShirt" should have following combinations:
      | id reference      | combination name        | reference | attributes           | impact on price | quantity | is default | image url                                          |
      | lemonTShirtSWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       | http://myshop.com/img/p/{lemonImage2}-small_default.jpg |
      | lemonTShirtSBlack | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{lemonImage1}-small_default.jpg |
      | lemonTShirtMWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      | http://myshop.com/img/p/{lemonImage4}-small_default.jpg |
      | lemonTShirtMBlack | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{lemonImage3}-small_default.jpg |
    # Search results follow the same principle
    When I search for combinations with locale "english" matching "lemon" I should get following results:
      | product      | combination       | name            | reference | image url                                              |
      | lemonade_can |                   | can of lemonade |           | http://myshop.com/img/p/{no_picture}-home_default.jpg  |
      | lemonTShirt  | lemonTShirtSWhite | lemon t-shirt   |           | http://myshop.com/img/p/{lemonImage2}-home_default.jpg |
      | lemonTShirt  | lemonTShirtSBlack | lemon t-shirt   |           | http://myshop.com/img/p/{lemonImage1}-home_default.jpg |
      | lemonTShirt  | lemonTShirtMWhite | lemon t-shirt   |           | http://myshop.com/img/p/{lemonImage4}-home_default.jpg |
      | lemonTShirt  | lemonTShirtMBlack | lemon t-shirt   |           | http://myshop.com/img/p/{lemonImage3}-home_default.jpg |
