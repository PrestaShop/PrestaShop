# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-combination-content
@restore-products-before-feature
@restore-languages-after-feature
@clear-cache-before-feature
@product-combination
@update-combination-content
Feature: Update product combination descriptions and meta tags in Back Office (BO)
  As an employee
  I need to be able to override the product descriptions and meta tags for a combination

  Background:
    Given language "french" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And attribute group "Color" named "Color" in en language exists
    And attribute "White" named "White" in en language exists
    And attribute "Black" named "Black" in en language exists
    And I add product "product1" with following information:
      | name[en-US] | universal T-shirt |
      | type        | combinations      |
    And product "product1" combinations list search criteria is set to defaults
    And I generate combinations for product product1 using following attributes:
      | Color | [White,Black] |
    And product "product1" should have following combinations:
      | id reference  | combination name | reference | attributes    | impact on price | quantity | is default |
      | product1White | Color - White    |           | [Color:White] | 0               | 0        | true       |
      | product1Black | Color - Black    |           | [Color:Black] | 0               | 0        | false      |

  Scenario: Combination content is empty by default so the product values are used
    Then combination "product1Black" should have following content:
      | description[en-US]       |  |
      | description_short[en-US] |  |
      | link_rewrite[en-US]      |  |
      | meta_description[en-US]  |  |
      | meta_title[en-US]        |  |

  Scenario: I override the product content for a combination
    When I update combination "product1Black" content with following values:
      | description[en-US]       | <p>The black one</p>       |
      | description[fr-FR]       | <p>Le noir</p>             |
      | description_short[en-US] | <p>Black summary</p>       |
      | link_rewrite[en-US]      | black-t-shirt              |
      | meta_description[en-US]  | Black T-shirt, no stains   |
      | meta_title[en-US]        | Black universal T-shirt    |
      | meta_title[fr-FR]        | T-shirt universel noir     |
    Then combination "product1Black" should have following content:
      | description[en-US]       | <p>The black one</p>       |
      | description[fr-FR]       | <p>Le noir</p>             |
      | description_short[en-US] | <p>Black summary</p>       |
      | description_short[fr-FR] |                            |
      | link_rewrite[en-US]      | black-t-shirt              |
      | link_rewrite[fr-FR]      |                            |
      | meta_description[en-US]  | Black T-shirt, no stains   |
      | meta_title[en-US]        | Black universal T-shirt    |
      | meta_title[fr-FR]        | T-shirt universel noir     |
    And combination "product1White" should have following content:
      | description[en-US] |  |
      | meta_title[en-US]  |  |
    # Partial update keeps the other values, an empty value resets the override
    When I update combination "product1Black" content with following values:
      | meta_title[en-US] |  |
    Then combination "product1Black" should have following content:
      | description[en-US] | <p>The black one</p>   |
      | meta_title[en-US]  |                        |
      | meta_title[fr-FR]  | T-shirt universel noir |

  Scenario: I cannot set invalid content
    When I update combination "product1Black" content with following values:
      | meta_title[en-US] | Invalid <title> |
    Then I should get error that product "meta_title" is invalid
    When I update combination "product1Black" content with following values:
      | description[en-US] | <script>alert(1)</script> |
    Then I should get error that product "description" is invalid
    When I update combination "product1Black" content with following values:
      | link_rewrite[en-US] | black t-shirt |
    Then I should get error that product "link_rewrite" is invalid
