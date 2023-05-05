# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart_rule --tags set-cart-rule-restrictions
@restore-all-tables-before-feature
@set-cart-rule-restrictions
Feature: Set cart rule restrictions in BO
  PrestaShop allows BO users to add and remove restrictions of cart rule combinations,
  which defines which cart rules can or cannot be compatible in the same cart
  As a BO user I must be able to edit cart rules compatibility

  Background:
    Given shop "shop1" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a currency named "chf" with iso code "CHF" and exchange rate of 1.25
    And currency "usd" is the default one
    And language with iso code "en" is the default one
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
      | name[en-US]                            | Half the price      |
      | is_active                              | true                |
      | allow_partial_use                      | true                |
      | priority                               | 2                   |
      | valid_from                             | 2022-01-01 11:00:00 |
      | valid_to                               | 3001-01-01 12:00:00 |
      | total_quantity                         | 10                  |
      | quantity_per_user                      | 12                  |
      | free_shipping                          | false               |
      | code                                   | rule_50_percent     |
      | reduction_percentage                   | 50                  |
      | reduction_apply_to_discounted_products | false               |
    And I create cart rule "rule_70_percent" with following properties:
      | name[en-US]                            | Half the price      |
      | is_active                              | true                |
      | allow_partial_use                      | true                |
      | priority                               | 3                   |
      | valid_from                             | 2022-01-01 11:00:00 |
      | valid_to                               | 3001-01-01 12:00:00 |
      | total_quantity                         | 10                  |
      | quantity_per_user                      | 12                  |
      | free_shipping                          | false               |
      | code                                   | rule_70_percent     |
      | reduction_percentage                   | 70                  |
      | reduction_apply_to_discounted_products | false               |
    And cart rule "rule_free_shipping_1" should have the following properties:
      | is_active             | true                 |
      | free_shipping         | true                 |
      | priority              | 1                    |
      | valid_from            | 2022-01-01 11:00:00  |
      | valid_to              | 3001-01-01 12:00:00  |
      | code                  | rule_free_shipping_1 |
      | restricted cart rules |                      |
    And cart rule "rule_50_percent" should have the following properties:
      | is_active                              | true                   |
      | priority                               | 2                      |
      | valid_from                             | 2022-01-01 11:00:00    |
      | valid_to                               | 3001-01-01 12:00:00    |
      | free_shipping                          | false                  |
      | code                                   | rule_50_percent        |
      | reduction_percentage                   | 50                     |
      | reduction_apply_to_discounted_products | false                  |
      | discount_application_type              | order_without_shipping |
      | restricted cart rules                  |                        |
    And cart rule "rule_70_percent" should have the following properties:
      | is_active                              | true                   |
      | priority                               | 3                      |
      | valid_from                             | 2022-01-01 11:00:00    |
      | valid_to                               | 3001-01-01 12:00:00    |
      | free_shipping                          | false                  |
      | code                                   | rule_70_percent        |
      | reduction_percentage                   | 70                     |
      | reduction_apply_to_discounted_products | false                  |
      | discount_application_type              | order_without_shipping |
      | restricted cart rules                  |                        |

  Scenario: Restrict cart rule combinations
    When I restrict following cart rules for cart rule rule_free_shipping_1:
      | rule_50_percent |
    Then cart rule "rule_free_shipping_1" should have the following properties:
      | restricted cart rules | rule_50_percent |
    And cart rule "rule_50_percent" should have the following properties:
      | restricted cart rules | rule_free_shipping_1 |
    And cart rule "rule_70_percent" should have the following properties:
      | restricted cart rules |  |
    When I restrict following cart rules for cart rule rule_free_shipping_1:
      | rule_50_percent |
      | rule_70_percent |
    Then cart rule "rule_free_shipping_1" should have the following properties:
      | restricted cart rules | rule_50_percent,rule_70_percent |
    And cart rule "rule_50_percent" should have the following properties:
      | restricted cart rules | rule_free_shipping_1 |
    And cart rule "rule_70_percent" should have the following properties:
      | restricted cart rules | rule_free_shipping_1 |

  @restore-cart-rules-before-scenario
  Scenario: Restrict cart rule combinations providing the same cart rule that is being edited in the restricted cart rules list
    Given cart rule "rule_70_percent" should have the following properties:
      | restricted cart rules |  |
    When I restrict following cart rules for cart rule rule_70_percent:
      | rule_50_percent |
      | rule_70_percent |
    Then I should get cart rule error about "invalid cart rule restriction"
    And cart rule "rule_70_percent" should have the following properties:
      | restricted cart rules |  |

# @todo: to be removed and FO cart rule application will be handled in separate PR because there are too many things to fix now
#  Scenario: 1 product in cart, cart rules are inserted correctly
#    And I add the following products to my cart:
#      | id reference | quantity |
#      | product1     | 1        |
#    And I should have 1 different products in my cart
#    And I select address "address1" in my cart
#    And I select carrier "carrier1" in my cart
#    And cart shipping fees should be 7.0
#    And my cart total should be 107.0 tax excluded
#    And cart rule referenced as "rule_free_shipping_1" can be applied to my cart
#    And cart rule referenced as "rule_50_percent" can be applied to my cart
#    And cart rule referenced as "rule_70_percent" can be applied to my cart
#    When I apply the discount code "rule_free_shipping_1" to my cart
#    # @todo: shipping fees are still 7.0 using the method bellow, because that one explicitly doesn't count free_shipping to avoid some loop,
#    #        but they are well calculated when calculating cart total
#    # Then cart shipping fees should be 0.0
#    Then my cart total should be 100.0 tax excluded
#    And cart rule referenced as "rule_50_percent" can be applied to my cart
#    And cart rule referenced as "rule_70_percent" can be applied to my cart
#    # @todo: by current behavior restricted rule = the ones compatible, but we will change the behavior depending on some configuration value,
#    # because it makes sense making all cart rules compatable by default, but only restricted ones would be incompatable.
#    # so the following line for now means that only rule_50_percent should be compatable with rule_free_shipping_1,
#    # therefore the rule_70_percent should not
#    When I restrict following cart rules for cart rule rule_free_shipping_1:
#      | rule_50_percent |
#    Then cart rule "rule_free_shipping_1" should have the following properties:
#      | restricted cart rules | rule_50_percent |
#    And cart rule "rule_50_percent" should have the following properties:
#      | restricted cart rules | rule_free_shipping_1 |
#    And cart rule "rule_70_percent" should have the following properties:
#      | restricted cart rules |  |
#    And cart rule referenced as "rule_50_percent" can be applied to my cart
#    # rule_70_percent can still be applied because it has higher priority so when applied it will replace the other "uncombinable" cart rule that was in cart already
#    # see CartRule L:909
#    # there are couple problems:
#    # 1. checkValidity method removes previous cart rule if its priority lower
#    # 2. checkValidity method is not called when using $cart->addCartRule (which is used when adding cart rule in behat bellow)
#    # for above mentioned reasons, the lines bellow won't pass :D Instead I need to reimplement additional step in a way that it works in FO (validates the cart rule when adding it)
#    And cart rule referenced as "rule_70_percent" can be applied to my cart
#    When I apply the discount code "rule_70_percent" to my cart
#    # this validates that free shipping rule has been replaced by the 70% discount ((100 total + 7 shipping) * 0.3 the 70% discount %)
#    #@todo: it doesn't work for above mentioned reasons now. (it will produce result of 30.0 because both of cart rules are added as the checkValidity is not called)
#    Then my cart total should be 32.1 tax excluded
#
#
#    When I use the discount "cartrule1"
#    Then cart rule "cartrule2" can be applied to my cart
#    When I use the discount "cartrule2"
#    When at least one cart rule applies today for customer with id 0
