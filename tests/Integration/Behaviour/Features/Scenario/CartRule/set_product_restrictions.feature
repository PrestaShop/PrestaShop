# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule --tags set-product-restrictions
@restore-all-tables-before-feature
@set-product-restrictions
Feature: Set cart rule product restrictions in BO
  PrestaShop allows BO users to add and remove product restrictions of cart rule,
  Product restrictions are a rulesets that defines what & how many products must be in a cart for cart rule to take effect.
  As a BO user I must be able to edit cart rules products restrictions

  Background:
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    And currency "usd" is the default one
    And language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "L" named "L" in en language exists
    And category "home" in default language named "Home" exists
    And category "home" is the default one
    And category "clothes" in default language named "Clothes" exists
    And category "clothes" parent is category "home"
    And category "men" in default language named "Men" exists
    And category "men" parent is category "clothes"
    And category "women" in default language named "Women" exists
    And category "women" parent is category "clothes"
    And manufacturer studioDesign named "Studio Design" exists
    And manufacturer graphicCorner named "Graphic Corner" exists
    And supplier fashionSupplier named "Fashion supplier" exists
    And supplier accessoriesSupplier named "Accessories supplier" exists
    And I add product "product1" with following information:
      | name[en-US] | bottle of beer |
      | type        | virtual        |
    And I add product "product2" with following information:
      | name[en-US] | T-shirt nr1 |
      | type        | standard    |
    And I add product "product3" with following information:
      | name[en-US] | Shirt - Dom & Jquery |
      | type        | standard             |
    And there is a cart rule "rule_free_shipping_1" with following properties:
      | name[en-US]       | free shipping 1      |
      | is_active         | true                 |
      | allow_partial_use | false                |
      | priority          | 1                    |
      | valid_from        | 2022-01-01 11:00:00  |
      | valid_to          | 3001-01-01 12:00:00  |
      | total_quantity    | 10                   |
      | quantity_per_user | 10                   |
      | free_shipping     | true                 |
      | code              | rule_free_shipping_1 |
    And there is a cart rule "rule_50_percent" with following properties:
      | name[en-US]                  | Half the price         |
      | is_active                    | true                   |
      | allow_partial_use            | true                   |
      | priority                     | 2                      |
      | valid_from                   | 2022-01-01 11:00:00    |
      | valid_to                     | 3001-01-01 12:00:00    |
      | total_quantity               | 10                     |
      | quantity_per_user            | 12                     |
      | free_shipping                | false                  |
      | code                         | rule_50_percent        |
      | discount_percentage          | 50                     |
      | apply_to_discounted_products | false                  |
      | discount_application_type    | order_without_shipping |
    And I clear all product restrictions for cart rule rule_free_shipping_1
    And I clear all product restrictions for cart rule rule_50_percent
    And I save all the restrictions for cart rule rule_free_shipping_1
    And I save all the restrictions for cart rule rule_50_percent
    And cart rule "rule_free_shipping_1" should have no product restriction rules
    And cart rule "rule_50_percent" should have no product restriction rules

  Scenario: Restrict cart rule products and clear all the restrictions
    When I add a restriction for cart rule rule_free_shipping_1, which requires at least 5 products in cart matching one of these rules:
      | type     | references        |
      | products | product1,product2 |
    And I add a restriction for cart rule rule_free_shipping_1, which requires at least 1 product in cart matching one of these rules:
      | type     | references |
      | products | product1   |
      | products | product2   |
      | products | product3   |
    And I save all the restrictions for cart rule rule_free_shipping_1
    Then cart rule "rule_free_shipping_1" should have the following product restriction rule groups:
      | groupReference | quantity | rules count |
      | group_nr_1     | 5        | 1           |
      | group_nr_2     | 1        | 3           |
    And the cart rule restriction group "group_nr_1" should have the following rules:
      | type     | references        |
      | products | product1,product2 |
    And the cart rule restriction group "group_nr_2" should have the following rules:
      | type     | references |
      | products | product1   |
      | products | product2   |
      | products | product3   |
    And cart rule "rule_50_percent" should have no product restriction rules
    When I clear all product restrictions for cart rule rule_free_shipping_1
    And I save all the restrictions for cart rule rule_free_shipping_1
    Then cart rule "rule_free_shipping_1" should have no product restriction rules

  Scenario: Restrict cart rule products by defining attribute matching rules
    When I add a restriction for cart rule rule_50_percent, which requires at least 7 products in cart matching one of these rules:
      | type       | references |
      | attributes | S,M        |
    And I add a restriction for cart rule rule_50_percent, which requires at least 2 product in cart matching one of these rules:
      | type       | references |
      | attributes | S,M        |
      | attributes | L          |
    And I save all the restrictions for cart rule rule_50_percent
    Then cart rule "rule_50_percent" should have the following product restriction rule groups:
      | groupReference | quantity | rules count |
      | group_nr_1     | 7        | 1           |
      | group_nr_2     | 2        | 2           |
    And the cart rule restriction group "group_nr_1" should have the following rules:
      | type       | references |
      | attributes | S,M        |
    And the cart rule restriction group "group_nr_2" should have the following rules:
      | type       | references |
      | attributes | S,M        |
      | attributes | L          |
    And cart rule "rule_free_shipping_1" should have no product restriction rules

  Scenario: Restrict cart rule products by defining category matching rules
    When I add a restriction for cart rule rule_50_percent, which requires at least 3 products in cart matching one of these rules:
      | type       | references |
      | categories | home       |
    And I add a restriction for cart rule rule_50_percent, which requires at least 2 product in cart matching one of these rules:
      | type       | references  |
      | categories | clothes,men |
      | categories | women       |
    And I save all the restrictions for cart rule rule_50_percent
    Then cart rule "rule_50_percent" should have the following product restriction rule groups:
      | groupReference | quantity | rules count |
      | group_nr_1     | 3        | 1           |
      | group_nr_2     | 2        | 2           |
    And the cart rule restriction group "group_nr_1" should have the following rules:
      | type       | references |
      | categories | home       |
    And the cart rule restriction group "group_nr_2" should have the following rules:
      | type       | references  |
      | categories | clothes,men |
      | categories | women       |

  Scenario: Restrict cart rule products by defining manufacturer matching rules
    When I add a restriction for cart rule rule_50_percent, which requires at least 2 products in cart matching one of these rules:
      | type          | references   |
      | manufacturers | studioDesign |
    And I add a restriction for cart rule rule_50_percent, which requires at least 1 product in cart matching one of these rules:
      | type          | references    |
      | manufacturers | graphicCorner |
    And I save all the restrictions for cart rule rule_50_percent
    And I add a restriction for cart rule rule_free_shipping_1, which requires at least 4 products in cart matching one of these rules:
      | type          | references                 |
      | manufacturers | studioDesign,graphicCorner |
    And I save all the restrictions for cart rule rule_free_shipping_1
    Then cart rule "rule_50_percent" should have the following product restriction rule groups:
      | groupReference | quantity | rules count |
      | 50_nr_1        | 2        | 1           |
      | 50_nr_2        | 1        | 1           |
    And the cart rule restriction group "50_nr_1" should have the following rules:
      | type          | references   |
      | manufacturers | studioDesign |
    And the cart rule restriction group "50_nr_2" should have the following rules:
      | type          | references    |
      | manufacturers | graphicCorner |
    And cart rule "rule_free_shipping_1" should have the following product restriction rule groups:
      | groupReference | quantity | rules count |
      | free_nr_1      | 4        | 1           |
    And the cart rule restriction group "free_nr_1" should have the following rules:
      | type          | references                 |
      | manufacturers | studioDesign,graphicCorner |

  Scenario: Restrict cart rule products by defining supplier matching rules
    When I add a restriction for cart rule rule_50_percent, which requires at least 2 products in cart matching one of these rules:
      | type      | references      |
      | suppliers | fashionSupplier |
    And I add a restriction for cart rule rule_50_percent, which requires at least 1 product in cart matching one of these rules:
      | type      | references          |
      | suppliers | accessoriesSupplier |
    And I save all the restrictions for cart rule rule_50_percent
    And I add a restriction for cart rule rule_free_shipping_1, which requires at least 4 products in cart matching one of these rules:
      | type      | references                          |
      | suppliers | fashionSupplier,accessoriesSupplier |
    And I save all the restrictions for cart rule rule_free_shipping_1
    Then cart rule "rule_50_percent" should have the following product restriction rule groups:
      | groupReference | quantity | rules count |
      | 50_nr_1        | 2        | 1           |
      | 50_nr_2        | 1        | 1           |
    And the cart rule restriction group "50_nr_1" should have the following rules:
      | type      | references      |
      | suppliers | fashionSupplier |
    And the cart rule restriction group "50_nr_2" should have the following rules:
      | type      | references          |
      | suppliers | accessoriesSupplier |
    And cart rule "rule_free_shipping_1" should have the following product restriction rule groups:
      | groupReference | quantity | rules count |
      | free_nr_1      | 4        | 1           |
    And the cart rule restriction group "free_nr_1" should have the following rules:
      | type      | references                          |
      | suppliers | fashionSupplier,accessoriesSupplier |

  Scenario: Restrict cart rule products by defining mixed rules
    When I add a restriction for cart rule rule_free_shipping_1, which requires at least 1 product in cart matching one of these rules:
      | type       | references        |
      | products   | product1,product2 |
      | products   | product3          |
      | attributes | S,M               |
      | categories | clothes,men       |
      | categories | women             |
    And I add a restriction for cart rule rule_free_shipping_1, which requires at least 2 products in cart matching one of these rules:
      | type          | references                          |
      | suppliers     | fashionSupplier,accessoriesSupplier |
      | manufacturers | graphicCorner                       |
    And I save all the restrictions for cart rule rule_free_shipping_1
    Then cart rule "rule_free_shipping_1" should have the following product restriction rule groups:
      | groupReference | quantity | rules count |
      | free_nr_1      | 1        | 5           |
      | free_nr_2      | 2        | 2           |
    And the cart rule restriction group "free_nr_1" should have the following rules:
      | type       | references        |
      | products   | product1,product2 |
      | products   | product3          |
      | attributes | S,M               |
      | categories | clothes,men       |
      | categories | women             |
    And the cart rule restriction group "free_nr_2" should have the following rules:
      | type          | references                          |
      | suppliers     | fashionSupplier,accessoriesSupplier |
      | manufacturers | graphicCorner                       |
    # We add some restrictions again to already existing rule to see if all previously added rules are replaced correctly
    When I add a restriction for cart rule rule_free_shipping_1, which requires at least 4 products in cart matching one of these rules:
      | type       | references        |
      | products   | product3          |
      | categories | women             |
    And I save all the restrictions for cart rule rule_free_shipping_1
    Then cart rule "rule_free_shipping_1" should have the following product restriction rule groups:
      | groupReference | quantity | rules count |
      | free_nr_1      | 4        | 2           |
    And the cart rule restriction group "free_nr_1" should have the following rules:
      | type       | references        |
      | products   | product3          |
      | categories | women             |
    # Checking that previous product restrictions can be removed
    And I clear all product restrictions for cart rule rule_free_shipping_1
    And I save all the restrictions for cart rule rule_free_shipping_1
    Then cart rule "rule_free_shipping_1" should have no product restriction rules

  Scenario: Provide restrictions with empty list of rules
    When I add a restriction for cart rule rule_free_shipping_1, which requires at least 1 product in cart matching one of these rules:
      | type     | references |
      | products |            |
    Then I should get cart rule error about "empty restriction rule ids"
    And cart rule rule_free_shipping_1 should have no product restriction rules

  Scenario: Rule to require any quantity of product matching certain rules
    When I add a restriction for cart rule rule_free_shipping_1, which requires any quantity of product in cart matching one of these rules:
      | type       | references  |
      | products   | product1    |
      | categories | clothes,men |
    And I add a restriction for cart rule rule_free_shipping_1, which requires any quantity of product in cart matching one of these rules:
      | type      | references                          |
      | suppliers | fashionSupplier,accessoriesSupplier |
    And I add a restriction for cart rule rule_free_shipping_1, which requires at least 100 product in cart matching one of these rules:
      | type          | references    |
      | manufacturers | graphicCorner |
    And I save all the restrictions for cart rule rule_free_shipping_1
    Then cart rule "rule_free_shipping_1" should have the following product restriction rule groups:
      | groupReference | quantity | rules count |
      | free_nr_1      | 0        | 2           |
      | free_nr_2      | 0        | 1           |
      | free_nr_3      | 100      | 1           |
    And the cart rule restriction group "free_nr_1" should have the following rules:
      | type       | references  |
      | products   | product1    |
      | categories | clothes,men |
    And the cart rule restriction group "free_nr_2" should have the following rules:
      | type      | references                          |
      | suppliers | fashionSupplier,accessoriesSupplier |
    And the cart rule restriction group "free_nr_3" should have the following rules:
      | type          | references    |
      | manufacturers | graphicCorner |

  Scenario: Provide restriction group with empty list of restrictions
    When I add a restriction for cart rule rule_50_percent, which requires at least 1 product, but I provide empty list of rules
    Then I should get cart rule error about "empty restriction rules"
    And cart rule rule_50_percent should have no product restriction rules
