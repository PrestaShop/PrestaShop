# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule --tags set-mixed-cart-rule-restrictions
@restore-all-tables-before-feature
@set-mixed-cart-rule-restrictions
Feature: Set cart rule restrictions in BO
  PrestaShop allows BO users to add and remove various restriction rules which defines
  how other cart rules, countries, products, carriers or customer groups affects validity of cart rule in cart.

  Background:
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    And currency "usd" is the default one
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
    And I create cart rule "rule_free_shipping_1" with following properties:
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
    And I create cart rule "rule_50_percent" with following properties:
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
    And I create cart rule "rule_70_percent" with following properties:
      | name[en-US]                  | Half the price         |
      | is_active                    | true                   |
      | allow_partial_use            | true                   |
      | priority                     | 3                      |
      | valid_from                   | 2022-01-01 11:00:00    |
      | valid_to                     | 3001-01-01 12:00:00    |
      | total_quantity               | 10                     |
      | quantity_per_user            | 12                     |
      | free_shipping                | false                  |
      | code                         | rule_70_percent        |
      | discount_percentage          | 70                     |
      | apply_to_discounted_products | false                  |
      | discount_application_type    | order_without_shipping |
    And cart rule "rule_free_shipping_1" should have the following properties:
      | restricted cart rules |  |
    And cart rule "rule_50_percent" should have the following properties:
      | restricted cart rules |  |
    And cart rule "rule_70_percent" should have the following properties:
      | restricted cart rules |  |
    And cart rule rule_free_shipping_1 should have no product restriction rules
    And cart rule rule_50_percent should have no product restriction rules

  Scenario: Restrict cart rule combinations and various products restrictions
    When I restrict following cart rules for cart rule rule_free_shipping_1:
      | restricted cart rules | rule_50_percent,rule_70_percent |
    And I add a restriction for cart rule rule_free_shipping_1, which requires at least 1 product in cart matching one of these rules:
      | type          | references                 |
      | products      | product1                   |
      | products      | product2                   |
      | products      | product3                   |
      | categories    | clothes,men                |
      | manufacturers | studioDesign,graphicCorner |
    And I add a restriction for cart rule rule_free_shipping_1, which requires at least 2 product in cart matching one of these rules:
      | type       | references |
      | attributes | S,M        |
      | attributes | L          |
    And I add a restriction for cart rule rule_free_shipping_1, which requires at least 4 products in cart matching one of these rules:
      | type      | references                          |
      | suppliers | fashionSupplier,accessoriesSupplier |
    And I save all the restrictions for cart rule rule_free_shipping_1
    Then cart rule "rule_free_shipping_1" should have the following properties:
      | restricted cart rules | rule_50_percent,rule_70_percent |
    And cart rule "rule_free_shipping_1" should have the following product restriction rule groups:
      | groupReference | quantity | rules count |
      | group_nr_1     | 1        | 5           |
      | group_nr_2     | 2        | 2           |
      | group_nr_3     | 4        | 1           |
    And the cart rule restriction group "group_nr_1" should have the following rules:
      | type          | references                 |
      | products      | product1                   |
      | products      | product2                   |
      | products      | product3                   |
      | categories    | clothes,men                |
      | manufacturers | studioDesign,graphicCorner |
    And the cart rule restriction group "group_nr_2" should have the following rules:
      | type       | references |
      | attributes | S,M        |
      | attributes | L          |
    And the cart rule restriction group "group_nr_3" should have the following rules:
      | type      | references                          |
      | suppliers | fashionSupplier,accessoriesSupplier |
    And cart rule rule_50_percent should have no product restriction rules
