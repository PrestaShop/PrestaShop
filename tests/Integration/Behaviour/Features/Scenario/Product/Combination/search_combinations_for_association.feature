# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags search-combinations
@restore-products-before-feature
@restore-languages-after-feature
@reset-img-after-feature
@clear-cache-before-feature
@search-combinations
@product-combination
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
    And I add product "cider_bottle" with following information:
      | name[en-US] | bottle of cider    |
      | name[fr-FR] | bouteille de cidre |
      | type        | standard           |
    When I search for combinations with locale "english" matching "beer" I should get following results:
      | product     | name           | reference | image url                                             |
      | beer_bottle | bottle of beer |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "bOtT" I should get following results:
      | product      | name            | reference | image url                                             |
      | beer_bottle  | bottle of beer  |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | cider_bottle | bottle of cider |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "french" matching "biere" I should get following results:
      | product     | name               | reference | image url                                             |
      | beer_bottle | bouteille de biere |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "french" matching "BoU" I should get following results:
      | product      | name               | reference | image url                                             |
      | beer_bottle  | bouteille de biere |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | cider_bottle | bouteille de cidre |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "french" matching "beer" I should get no results
    And I search for combinations with locale "english" matching "biere" I should get no results

  Scenario: I can search combinations by references
    When I add product "champaign_bottle" with following information:
      | name[en-US] | bottle of champaign    |
      | name[fr-FR] | bouteille de champagne |
      | type        | standard               |
    When I search for combinations with locale "english" matching "978-3-16-148410-0" I should get no results
    And I search for combinations with locale "english" matching "72527273070" I should get no results
    And I search for combinations with locale "english" matching "978020137962" I should get no results
    And I search for combinations with locale "english" matching "mpn1" I should get no results
    And I search for combinations with locale "english" matching "ref1" I should get no results
    When I update product "champaign_bottle" with following values:
      | isbn      | 978-3-16-148410-0 |
      | upc       | 72527273070       |
      | ean13     | 978020137962      |
      | mpn       | mpn1              |
      | reference | ref1              |
    Then product "champaign_bottle" should have following details:
      | product detail | value             |
      | isbn           | 978-3-16-148410-0 |
      | upc            | 72527273070       |
      | ean13          | 978020137962      |
      | mpn            | mpn1              |
      | reference      | ref1              |
    # Search by all types of references matching champaign_bottle
    When I search for combinations with locale "english" matching "978-3-16-148410-0" I should get following results:
      | product          | name                | reference | image url                                             |
      | champaign_bottle | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "72527273070" I should get following results:
      | product          | name                | reference | image url                                             |
      | champaign_bottle | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "978020137962" I should get following results:
      | product          | name                | reference | image url                                             |
      | champaign_bottle | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "mpn1" I should get following results:
      | product          | name                | reference | image url                                             |
      | champaign_bottle | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "ref1" I should get following results:
      | product          | name                | reference | image url                                             |
      | champaign_bottle | bottle of champaign | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |

  Scenario: I can search combinations by combination references
    Given I add product "wine_bottle" with following information:
      | name[en-US] | bottle of wine   |
      | name[fr-FR] | bouteille de vin |
      | type        | combinations     |
    And I generate combinations for product wine_bottle using following attributes:
      | Color | [Red,White,Pink] |
    And product "wine_bottle" should have following combinations:
      | id reference      | combination name | reference | attributes    | impact on price | quantity | is default |
      | wine_bottle_red   | Color - Red      |           | [Color:Red]   | 0               | 0        | true       |
      | wine_bottle_white | Color - White    |           | [Color:White] | 0               | 0        | false      |
      | wine_bottle_pink  | Color - Pink     |           | [Color:Pink]  | 0               | 0        | false      |
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
    When I update combination "wine_bottle_red" with following values:
      | ean13     | 154867313573      |
      | isbn      | 978-3-16-148410-3 |
      | mpn       | mpn3red           |
      | reference | ref3red           |
      | upc       | 137684192354      |
    And I update combination "wine_bottle_white" with following values:
      | ean13     | 1357321357213     |
      | isbn      | 978-3-16-148410-4 |
      | mpn       | mpn3white         |
      | reference | ref3white         |
      | upc       | 354321354321      |
    Then combination "wine_bottle_red" should have following details:
      | combination detail | value             |
      | ean13              | 154867313573      |
      | isbn               | 978-3-16-148410-3 |
      | mpn                | mpn3red           |
      | reference          | ref3red           |
      | upc                | 137684192354      |
    Then combination "wine_bottle_white" should have following details:
      | combination detail | value             |
      | ean13              | 1357321357213     |
      | isbn               | 978-3-16-148410-4 |
      | mpn                | mpn3white         |
      | reference          | ref3white         |
      | upc                | 354321354321      |
    # General reference on product will be used for pink wine which has no reference on the combination
    When I update product "wine_bottle" with following values:
      | reference | ref3wine |
    Then product "wine_bottle" should have following details:
      | product detail | value    |
      | isbn           |          |
      | upc            |          |
      | ean13          |          |
      | mpn            |          |
      | reference      | ref3wine |
    # Search by all types of references matching wine_bottle_red combination
    When I search for combinations with locale "english" matching "154867313573" I should get following results:
      | product     | combination     | name                        | reference | image url                                             |
      | wine_bottle | wine_bottle_red | bottle of wine: Color - Red | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "978-3-16-148410-3" I should get following results:
      | product     | combination     | name                        | reference | image url                                             |
      | wine_bottle | wine_bottle_red | bottle of wine: Color - Red | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "mpn3red" I should get following results:
      | product     | combination     | name                        | reference | image url                                             |
      | wine_bottle | wine_bottle_red | bottle of wine: Color - Red | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "ref3red" I should get following results:
      | product     | combination     | name                        | reference | image url                                             |
      | wine_bottle | wine_bottle_red | bottle of wine: Color - Red | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "137684192354" I should get following results:
      | product     | combination     | name                        | reference | image url                                             |
      | wine_bottle | wine_bottle_red | bottle of wine: Color - Red | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    # Search by all types of references matching wine_bottle_white combination
    When I search for combinations with locale "english" matching "1357321357213" I should get following results:
      | product     | combination       | name                          | reference | image url                                             |
      | wine_bottle | wine_bottle_white | bottle of wine: Color - White | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "978-3-16-148410-4" I should get following results:
      | product     | combination       | name                          | reference | image url                                             |
      | wine_bottle | wine_bottle_white | bottle of wine: Color - White | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "mpn3white" I should get following results:
      | product     | combination       | name                          | reference | image url                                             |
      | wine_bottle | wine_bottle_white | bottle of wine: Color - White | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "ref3white" I should get following results:
      | product     | combination       | name                          | reference | image url                                             |
      | wine_bottle | wine_bottle_white | bottle of wine: Color - White | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "354321354321" I should get following results:
      | product     | combination       | name                          | reference | image url                                             |
      | wine_bottle | wine_bottle_white | bottle of wine: Color - White | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    # Search by types that match multiple combinations
    When I search for combinations with locale "english" matching "mpn3" I should get following results:
      | product     | combination       | name                          | reference | image url                                             |
      | wine_bottle | wine_bottle_red   | bottle of wine: Color - Red   | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | wine_bottle | wine_bottle_white | bottle of wine: Color - White | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "ref3" I should get following results:
      | product     | combination       | name                          | reference | image url                                             |
      | wine_bottle | wine_bottle_red   | bottle of wine: Color - Red   | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | wine_bottle | wine_bottle_white | bottle of wine: Color - White | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | wine_bottle | wine_bottle_pink  | bottle of wine: Color - Pink  | ref3wine  | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    # Search by types that match multiple combinations and a product
    When I search for combinations with locale "english" matching "mpn" I should get following results:
      | product          | combination       | name                          | reference | image url                                             |
      | champaign_bottle |                   | bottle of champaign           | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | wine_bottle      | wine_bottle_red   | bottle of wine: Color - Red   | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | wine_bottle      | wine_bottle_white | bottle of wine: Color - White | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I search for combinations with locale "english" matching "ref" I should get following results:
      | product          | combination       | name                          | reference | image url                                             |
      | champaign_bottle |                   | bottle of champaign           | ref1      | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | wine_bottle      | wine_bottle_red   | bottle of wine: Color - Red   | ref3red   | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | wine_bottle      | wine_bottle_white | bottle of wine: Color - White | ref3white | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | wine_bottle      | wine_bottle_pink  | bottle of wine: Color - Pink  | ref3wine  | http://myshop.com/img/p/{no_picture}-home_default.jpg |

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
    When I add new image "can_image1" named "app_icon.png" to product "coke_can"
    And I add new image "can_image2" named "logo.jpg" to product "coke_can"
    And I update image "can_image2" with following information:
      | cover | true |
    Then product "coke_can" should have following images:
      | image reference | is cover | legend[en-US] | legend[fr-FR] | position | image url                                | thumbnail url                                          |
      | can_image1      | false    |               |               | 1        | http://myshop.com/img/p/{can_image1}.jpg | http://myshop.com/img/p/{can_image1}-small_default.jpg |
      | can_image2      | true     |               |               | 2        | http://myshop.com/img/p/{can_image2}.jpg | http://myshop.com/img/p/{can_image2}-small_default.jpg |
    # Search returns the cover image url when present
    When I search for combinations with locale "english" matching "can" I should get following results:
      | product      | name            | reference | image url                                             |
      | coke_can     | can of coke     |           | http://myshop.com/img/p/{can_image2}-home_default.jpg |
      | lemonade_can | can of lemonade |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |

  Scenario: I associate images to a combination
    Given I add product "lemon_tshirt" with following information:
      | name[en-US] | lemon t-shirt  |
      | name[fr-FR] | t-shirt citron |
      | type        | combinations   |
    And product lemon_tshirt type should be combinations
    And I generate combinations for product lemon_tshirt using following attributes:
      | Size  | [S,M]         |
      | Color | [White,Black] |
    And product "lemon_tshirt" should have following combinations:
      | id reference         | combination name        | reference | attributes           | impact on price | quantity | is default | image url                                              |
      | lemon_tshirt_s_white | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | lemon_tshirt_s_black | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | lemon_tshirt_m_white | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | lemon_tshirt_m_black | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{no_picture}-small_default.jpg |
    # No image can be returned for both products
    When I search for combinations with locale "english" matching "lemon" I should get following results:
      | product      | combination          | name                                   | reference | image url                                             |
      | lemonade_can |                      | can of lemonade                        |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | lemon_tshirt | lemon_tshirt_s_white | lemon t-shirt: Size - S, Color - White |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | lemon_tshirt | lemon_tshirt_s_black | lemon t-shirt: Size - S, Color - Black |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | lemon_tshirt | lemon_tshirt_m_white | lemon t-shirt: Size - M, Color - White |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | lemon_tshirt | lemon_tshirt_m_black | lemon t-shirt: Size - M, Color - Black |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And I add new image "lemon_image1" named "app_icon.png" to product "lemon_tshirt"
    And I add new image "lemon_image2" named "logo.jpg" to product "lemon_tshirt"
    And I add new image "lemon_image3" named "app_icon.png" to product "lemon_tshirt"
    And I add new image "lemon_image4" named "logo.jpg" to product "lemon_tshirt"
    # The fallback image is the first one from the product
    And product "lemon_tshirt" should have following combinations:
      | id reference         | combination name        | reference | attributes           | impact on price | quantity | is default | image url                                                |
      | lemon_tshirt_s_white | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       | http://myshop.com/img/p/{lemon_image1}-small_default.jpg |
      | lemon_tshirt_s_black | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{lemon_image1}-small_default.jpg |
      | lemon_tshirt_m_white | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      | http://myshop.com/img/p/{lemon_image1}-small_default.jpg |
      | lemon_tshirt_m_black | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{lemon_image1}-small_default.jpg |
    # Search results follow the same principle
    When I search for combinations with locale "english" matching "lemon" I should get following results:
      | product      | combination          | name                                   | reference | image url                                               |
      | lemonade_can |                      | can of lemonade                        |           | http://myshop.com/img/p/{no_picture}-home_default.jpg   |
      | lemon_tshirt | lemon_tshirt_s_white | lemon t-shirt: Size - S, Color - White |           | http://myshop.com/img/p/{lemon_image1}-home_default.jpg |
      | lemon_tshirt | lemon_tshirt_s_black | lemon t-shirt: Size - S, Color - Black |           | http://myshop.com/img/p/{lemon_image1}-home_default.jpg |
      | lemon_tshirt | lemon_tshirt_m_white | lemon t-shirt: Size - M, Color - White |           | http://myshop.com/img/p/{lemon_image1}-home_default.jpg |
      | lemon_tshirt | lemon_tshirt_m_black | lemon t-shirt: Size - M, Color - Black |           | http://myshop.com/img/p/{lemon_image1}-home_default.jpg |
    And combination "lemon_tshirt_s_white" should have no images
    When I associate "[lemon_image2,lemon_image3]" to combination "lemon_tshirt_s_white"
    Then combination "lemon_tshirt_s_white" should have following images "[lemon_image2,lemon_image3]"
    When I associate "[lemon_image4]" to combination "lemon_tshirt_m_white"
    Then combination "lemon_tshirt_m_white" should have following images "[lemon_image4]"
    When I associate "[lemon_image4,lemon_image3]" to combination "lemon_tshirt_m_black"
    Then combination "lemon_tshirt_m_black" should have following images "[lemon_image4,lemon_image3]"
    # Now the combination image is the first one in its own associated images (first by image creation oder)
    And product "lemon_tshirt" should have following combinations:
      | id reference         | combination name        | reference | attributes           | impact on price | quantity | is default | image url                                                |
      | lemon_tshirt_s_white | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       | http://myshop.com/img/p/{lemon_image2}-small_default.jpg |
      | lemon_tshirt_s_black | Size - S, Color - Black |           | [Size:S,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{lemon_image1}-small_default.jpg |
      | lemon_tshirt_m_white | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      | http://myshop.com/img/p/{lemon_image4}-small_default.jpg |
      | lemon_tshirt_m_black | Size - M, Color - Black |           | [Size:M,Color:Black] | 0               | 0        | false      | http://myshop.com/img/p/{lemon_image3}-small_default.jpg |
    # Search results follow the same principle
    When I search for combinations with locale "english" matching "lemon" I should get following results:
      | product      | combination          | name                                   | reference | image url                                               |
      | lemonade_can |                      | can of lemonade                        |           | http://myshop.com/img/p/{no_picture}-home_default.jpg   |
      | lemon_tshirt | lemon_tshirt_s_white | lemon t-shirt: Size - S, Color - White |           | http://myshop.com/img/p/{lemon_image2}-home_default.jpg |
      | lemon_tshirt | lemon_tshirt_s_black | lemon t-shirt: Size - S, Color - Black |           | http://myshop.com/img/p/{lemon_image1}-home_default.jpg |
      | lemon_tshirt | lemon_tshirt_m_white | lemon t-shirt: Size - M, Color - White |           | http://myshop.com/img/p/{lemon_image4}-home_default.jpg |
      | lemon_tshirt | lemon_tshirt_m_black | lemon t-shirt: Size - M, Color - Black |           | http://myshop.com/img/p/{lemon_image3}-home_default.jpg |

  Scenario: I perform a search for candidate to be packed and I get result, but no pack is available
    Given I add product "packedProduct" with following information:
      | name[en-US] | pack of shots of Diplomatico Rum |
      | type        | pack                             |
    And I add product "secondPackedProduct" with following information:
      | name[en-US] | pack of shots of White Rum |
      | type        | pack                       |
    And I add product "product1" with following information:
      | name[en-US] | shot of White Rum         |
      | name[fr-FR] | petit verre de Rhum Blanc |
      | type        | standard                  |
    And I add product "product2" with following information:
      | name[en-US] | shot of Kir Breton        |
      | name[fr-FR] | petit verre de Kir Breton |
      | type        | standard                  |
    When I search for products with locale "english" matching "kir breton" for "packedProduct" I should get following results:
      | product  | name               | reference | image url                                             |
      | product2 | shot of Kir Breton |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    When I search for products with locale "french" matching "petit verre" for "packedProduct" I should get following results:
      | product  | name                      | reference | image url                                             |
      | product2 | petit verre de Kir Breton |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
      | product1 | petit verre de Rhum Blanc |           | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    When I search for combinations with locale "english" matching "pack of shots" for packs I should get no results
    When I search for combinations with locale "english" matching "Diplomatico" for packs I should get no results

