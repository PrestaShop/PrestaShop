# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule --tags edit-cart-rule
@restore-all-tables-before-feature
@edit-cart-rule
Feature: Add cart rule
  PrestaShop allows BO users to edit cart rules
  As a BO user
  I must be able to edit cart rules

  Background:
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    And currency "usd" is the default one
    And language with iso code "en" is the default one
    And attribute group "Size" named "Size" in en language exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "S" named "S" in en language exists
    And attribute "M" named "M" in en language exists
    And attribute "White" named "White" in en language exists
    And I create cart rule "cart_rule_1" with following properties:
      | name[en-US]                      | cart rule 1         |
      | highlight                        | true                |
      | active                           | true                |
      | allow_partial_use                | true                |
      | priority                         | 1                   |
      | valid_from                       | 2019-01-01 11:05:00 |
      | valid_to                         | 2019-12-01 00:00:00 |
      | total_quantity                   | 11                  |
      | quantity_per_user                | 3                   |
      | free_shipping                    | true                |
      | minimum_amount_tax_included      | false               |
      | minimum_amount_shipping_included | false               |
    And cart rule "cart_rule_1" should have the following properties:
      | name[en-US]       | cart rule 1         |
      | highlight         | true                |
      | active            | true                |
      | allow_partial_use | true                |
      | priority          | 1                   |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | total_quantity    | 11                  |
      | quantity_per_user | 3                   |
      | free_shipping     | true                |
      | minimum_amount    |                     |
      # when currency is not provided the default one is used
      | discount_currency | usd                 |
      | code              |                     |

  @restore-cart-rules-after-scenario
  Scenario: I edit cart rule and change various properties
    When I edit cart rule cart_rule_1 with following properties:
      | name[en-US]                      | cart rule 1 edited  |
      | highlight                        | false               |
      | active                           | false               |
      | allow_partial_use                | false               |
      | priority                         | 120                 |
      | valid_from                       | 2019-01-01 11:05:01 |
      | valid_to                         | 2020-12-01 00:00:00 |
      | total_quantity                   | 100                 |
      | quantity_per_user                | 1                   |
      | free_shipping                    | true                |
      | minimum_amount                   | 10                  |
      | minimum_amount_currency          | chf                 |
      | minimum_amount_tax_included      | true                |
      | minimum_amount_shipping_included | true                |
      | code                             | abcxyz              |
    Then cart rule "cart_rule_1" should have the following properties:
      | name[en-US]                      | cart rule 1 edited  |
      | highlight                        | false               |
      | active                           | false               |
      | allow_partial_use                | false               |
      | priority                         | 120                 |
      | valid_from                       | 2019-01-01 11:05:01 |
      | valid_to                         | 2020-12-01 00:00:00 |
      | total_quantity                   | 100                 |
      | quantity_per_user                | 1                   |
      | free_shipping                    | true                |
      | minimum_amount                   | 10                  |
      | minimum_amount_currency          | chf                 |
      | minimum_amount_tax_included      | true                |
      | minimum_amount_shipping_included | true                |
      | discount_amount                  | 0                   |
      | discount_includes_tax            | false               |
      | code                             | abcxyz              |

  Scenario: I edit cart rule and remove free shipping when it is the only action.
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping | false |
    Then I should get cart rule error about "missing action"
    And cart rule "cart_rule_1" should have the following properties:
      | free_shipping | true |

  @restore-cart-rules-after-scenario
  Scenario: I edit cart rule by adding amount discount action.
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping             | true                   |
      | discount_amount           | 10.5                   |
      | discount_includes_tax     | true                   |
      | discount_currency         | chf                    |
      | discount_application_type | order_without_shipping |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping             | true                   |
      | discount_amount           | 10.5                   |
      | discount_includes_tax     | true                   |
      | discount_currency         | chf                    |
      | discount_application_type | order_without_shipping |
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping             | false                  |
      | discount_amount           | 11                     |
      | discount_includes_tax     | false                  |
      | discount_currency         | usd                    |
      | discount_application_type | order_without_shipping |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping             | false                  |
      | discount_amount           | 11                     |
      | discount_includes_tax     | false                  |
      | discount_currency         | usd                    |
      | discount_application_type | order_without_shipping |
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping             | false            |
      | discount_amount           | 11               |
      | discount_includes_tax     | false            |
      | discount_currency         | usd              |
      | discount_application_type | specific_product |
      | discount_product          | product1         |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping             | false            |
      | discount_amount           | 11               |
      | discount_includes_tax     | false            |
      | discount_currency         | usd              |
      | discount_application_type | specific_product |
      | discount_product          | product1         |

  Scenario: I edit cart rule by applying specific product discount, but not providing the specific product itself
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping             | true             |
      | discount_amount           | 10.5             |
      | discount_includes_tax     | true             |
      | discount_currency         | chf              |
      | discount_application_type | specific_product |
    Then I should get cart rule error about "required specific product"

  @restore-cart-rules-after-scenario
  Scenario: I edit cart rule by adding percentage discount action.
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping                | true                   |
      | discount_percentage          | 85.5                   |
      | discount_application_type    | order_without_shipping |
      | apply_to_discounted_products | true                   |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping                | true                   |
      | discount_percentage          | 85.5                   |
      | discount_application_type    | order_without_shipping |
      | apply_to_discounted_products | true                   |
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping                | false            |
      | discount_percentage          | 10               |
      | discount_application_type    | cheapest_product |
      | apply_to_discounted_products | false            |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping                | false            |
      | discount_percentage          | 10               |
      | discount_application_type    | cheapest_product |
      | apply_to_discounted_products | false            |
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping                | false            |
      | discount_percentage          | 10               |
      | discount_application_type    | cheapest_product |
      | apply_to_discounted_products | false            |
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping                | false            |
      | discount_percentage          | 10               |
      | discount_application_type    | specific_product |
      | apply_to_discounted_products | true             |
      | discount_product             | product1         |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping                | false            |
      | discount_percentage          | 10               |
      | discount_application_type    | specific_product |
      | apply_to_discounted_products | true             |
      | discount_product             | product1         |
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping                | false                  |
      | discount_percentage          | 10                     |
      | discount_application_type    | order_without_shipping |
      | apply_to_discounted_products | true                   |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping                | false                  |
      | discount_percentage          | 10                     |
      | discount_application_type    | order_without_shipping |
      | apply_to_discounted_products | true                   |
      | discount_product             |                        |

  @restore-cart-rules-after-scenario
  Scenario: I edit cart rule by adding gift product action.
    Given I add product "product1" with following information:
      | name[en-US] | Presta camera |
      | type        | standard      |
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping | false    |
      | gift_product  | product1 |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping | false    |
      | gift_product  | product1 |
    Given I add product "product2" with following information:
      | name[en-US] | Combicorn    |
      | type        | combinations |
    And I generate combinations for product "product2" using following attributes:
      | Size  | [S,M]   |
      | Color | [White] |
    And product "product2" should have following combinations:
      | id reference   | combination name        | reference | attributes           | impact on price | quantity | is default | image url                                              |
      | product2SWhite | Size - S, Color - White |           | [Size:S,Color:White] | 0               | 0        | true       | http://myshop.com/img/p/{no_picture}-small_default.jpg |
      | product2MWhite | Size - M, Color - White |           | [Size:M,Color:White] | 0               | 0        | false      | http://myshop.com/img/p/{no_picture}-small_default.jpg |
    When I edit cart rule cart_rule_1 with following properties:
      | gift_product     | product2       |
      | gift_combination | product2SWhite |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping    | false          |
      | gift_product     | product2       |
      | gift_combination | product2SWhite |
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping    | true           |
      | gift_product     | product2       |
      | gift_combination | product2SWhite |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping                | true                   |
      | gift_product                 | product2               |
      | gift_combination             | product2SWhite         |
      # assert default values of reduction fields
      | discount_percentage          | 0                      |
      | discount_amount              | 0                      |
      | discount_currency            | usd                    |
      | discount_application_type    | order_without_shipping |
      | apply_to_discounted_products | true                   |
    When I edit cart rule cart_rule_1 with following properties:
      | free_shipping | true     |
      | gift_product  | product1 |
    Then cart rule "cart_rule_1" should have the following properties:
      | free_shipping                | true                   |
      | gift_product                 | product1               |
      | gift_combination             |                        |
      # assert default values of reduction fields
      | discount_percentage          | 0                      |
      | discount_amount              | 0                      |
      | discount_currency            | usd                    |
      | discount_application_type    | order_without_shipping |
      | apply_to_discounted_products | true                   |

  @restore-cart-rules-after-scenario
  Scenario: I edit cart rule by alternating customer
    Given there is customer "JohnDoe" with email "pub@prestashop.com"
    And cart rule "cart_rule_1" should have the following properties:
      | customer |  |
    When I edit cart rule cart_rule_1 with following properties:
      | customer | JohnDoe |
    Then cart rule "cart_rule_1" should have the following properties:
      | customer | JohnDoe |
    When I edit cart rule cart_rule_1 with following properties:
      | customer |  |
    Then cart rule "cart_rule_1" should have the following properties:
      | customer |  |

  Scenario: I should not be able to edit cart rule with already existing code
    Given I create cart rule "cart_rule_2" with following properties:
      | name[en-US]       | Cart Rule 2         |
      | highlight         | true                |
      | active            | true                |
      | allow_partial_use | true                |
      | priority          | 1                   |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | total_quantity    | 10                  |
      | quantity_per_user | 2                   |
      | free_shipping     | true                |
      | code              | testcode1           |
    And cart rule cart_rule_2 should have the following properties:
      | code | testcode1 |
    And I create cart rule "cart_rule_3" with following properties:
      | name[en-US]       | Cart Rule 3         |
      | highlight         | true                |
      | active            | true                |
      | allow_partial_use | true                |
      | priority          | 1                   |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | total_quantity    | 10                  |
      | quantity_per_user | 2                   |
      | free_shipping     | true                |
      | code              | testcode2           |
    When I edit cart rule cart_rule_2 with following properties:
      | code | testcode1 |
    When I edit cart rule cart_rule_3 with following properties:
      | code | testcode1 |
    Then I should get cart rule error about "non unique cart rule code"
    And cart rule cart_rule_3 should have the following properties:
      | code | testcode2 |
    # make sure we do not raise error when cart rule is submitted with the same code as it was before
    When I edit cart rule cart_rule_3 with following properties:
      | code | testcode2 |
    Then cart rule cart_rule_3 should have the following properties:
      | code | testcode2 |

  Scenario: Update cart rule status
    Given cart rule with reference "cart_rule_1" is enabled
    When I disable cart rule with reference "cart_rule_1"
    Then cart rule with reference "cart_rule_1" is disabled
    When I enable cart rule with reference "cart_rule_1"
    Then cart rule with reference "cart_rule_1" is enabled

  Scenario: Update multiple cart rules status
    Given I create cart rule "cart_rule_4" with following properties:
      | name[en-US]                      | Cart Rule 4         |
      | highlight                        | true                |
      | active                           | false               |
      | allow_partial_use                | true                |
      | priority                         | 1                   |
      | valid_from                       | 2019-01-01 11:05:00 |
      | valid_to                         | 2019-12-01 00:00:00 |
      | total_quantity                   | 10                  |
      | quantity_per_user                | 2                   |
      | free_shipping                    | true                |
      | minimum_amount                   | 10                  |
      | minimum_amount_currency          | usd                 |
      | minimum_amount_tax_included      | true                |
      | minimum_amount_shipping_included | true                |
    And I create cart rule "cart_rule_5" with following properties:
      | name[en-US]                      | Cart Rule 5         |
      | highlight                        | true                |
      | active                           | false               |
      | allow_partial_use                | true                |
      | priority                         | 1                   |
      | valid_from                       | 2019-01-01 11:05:00 |
      | valid_to                         | 2019-12-01 00:00:00 |
      | total_quantity                   | 10                  |
      | quantity_per_user                | 2                   |
      | free_shipping                    | true                |
      | minimum_amount                   | 10                  |
      | minimum_amount_currency          | usd                 |
      | minimum_amount_tax_included      | true                |
      | minimum_amount_shipping_included | true                |
    And cart rule cart_rule_4 should have the following properties:
      | active | false |
    And cart rule cart_rule_5 should have the following properties:
      | active | false |
    And I bulk enable cart rules "cart_rule_4,cart_rule_5"
    Then cart rule with reference "cart_rule_4" is enabled
    And cart rule with reference "cart_rule_5" is enabled
    When I bulk disable cart rules "cart_rule_4,cart_rule_5"
    Then cart rule with reference "cart_rule_4" is disabled
    And cart rule with reference "cart_rule_5" is disabled
    When I bulk enable cart rules "cart_rule_4"
    Then cart rule with reference "cart_rule_4" is enabled
    But cart rule with reference "cart_rule_5" is disabled
    When I bulk disable cart rules "cart_rule_4,cart_rule_5"
    Then cart rule with reference "cart_rule_4" is disabled
    And cart rule with reference "cart_rule_5" is disabled

  Scenario: Update cart rule shop association when multishop feature is on
    Given I enable multishop feature
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And single shop context is loaded
    When I create cart rule "cart_rule_6" with following properties:
      | name[en-US]   | Cart Rule 6 |
      | free_shipping | true        |
    Then cart rule cart_rule_6 should have the following properties:
      | active           | true  |
      | associated shops | shop1 |
    When I edit cart rule cart_rule_6 with following properties:
      | active | false |
    Then cart rule cart_rule_6 should have the following properties:
      | active           | false |
      | associated shops | shop1 |
    When I edit cart rule cart_rule_6 with following properties:
      | associated shops | shop1,shop2 |
    Then cart rule cart_rule_6 should have the following properties:
      | associated shops | shop1,shop2 |
    When I edit cart rule cart_rule_6 with following properties:
      | associated shops | shop2 |
    Then cart rule cart_rule_6 should have the following properties:
      | associated shops | shop2 |
    When I edit cart rule cart_rule_6 with following properties:
      | associated shops |  |
    Then I should get cart rule error about "invalid shop association"
    And cart rule cart_rule_6 should have the following properties:
      | associated shops | shop2 |
