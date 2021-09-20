# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags search-products
@reset-database-before-feature
@clear-cache-before-feature
@search-products
Feature: Search products to associate them in the BO
  As an employee
  I need to be able to search for products in the BO to associate them

  Background:
    Given language "english" with locale "en-US" exists
    Given language "french" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    When I add product "product1" with following information:
      | name[en-US] | bottle of beer     |
      | name[fr-FR] | bouteille de biere |
      | type        | standard           |
    And I add product "product2" with following information:
      | name[en-US] | bottle of cider    |
      | name[fr-FR] | bouteille de cidre |
      | type        | standard           |

  Scenario: I can search products by name
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
