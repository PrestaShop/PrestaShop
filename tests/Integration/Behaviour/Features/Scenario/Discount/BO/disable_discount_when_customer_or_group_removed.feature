# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags disable-discount-when-customer-or-group-removed
# See https://github.com/PrestaShop/PrestaShop/issues/40109
@disable-discount-when-customer-or-group-removed
@restore-cart-rules-after-scenario
Feature: Disable discount when the only selected customer or customer group is removed
  When the only selected customer or the only selected customer group for a discount is removed,
  the discount must apply to all customers but be disabled so it is no longer applied.

  Background:
    Given groups feature is activated
    And I enable feature flag "discount"
    And shop configuration for "PS_CART_RULE_FEATURE_ACTIVE" is set to 1
    And the default shop is referenced as "shop1"
    And there is a currency named "usd" with iso code "USD" and exchange rate of 1.0

  Scenario: Discount for a single customer is disabled and applies to all when that customer is deleted
    Given I create a customer "discountCustomer" with following properties:
      | firstName | Jean   |
      | lastName  | Dupont |
      | email     | jean.dupont.discount@prestashop.com |
      | password  | PrestaShopForever1_! |
    And I create a "cart_level" discount "discountForCustomer" with following properties:
      | name[en-US]            | Discount for Jean    |
      | code                   | CUST_DISCOUNT_40109  |
      | active                 | true                 |
      | reduction_amount       | 5                    |
      | reduction_currency     | usd                  |
      | reduction_tax_included | false                |
      | customer               | discountCustomer     |
    Then discount "discountForCustomer" is enabled and still applies to customer "discountCustomer"
    When I delete customer "discountCustomer" and allow it to register again
    Then discount "discountForCustomer" applies to all customers and is disabled

  Scenario: Discount for a single customer stays enabled and still applies to that customer when the customer is disabled
    Given I create a customer "disabledCustomer" with following properties:
      | firstName | Marie  |
      | lastName  | Martin |
      | email     | marie.martin.discount@prestashop.com |
      | password  | PrestaShopForever1_! |
    And I create a "cart_level" discount "discountForDisabledCustomer" with following properties:
      | name[en-US]            | Discount for Marie   |
      | code                   | CUST_DISABLED_40109  |
      | active                 | true                 |
      | reduction_amount       | 5                    |
      | reduction_currency     | usd                  |
      | reduction_tax_included | false                |
      | customer               | disabledCustomer     |
    Then discount "discountForDisabledCustomer" is enabled and still applies to customer "disabledCustomer"
    When I disable customer "disabledCustomer"
    Then discount "discountForDisabledCustomer" is enabled and still applies to customer "disabledCustomer"

  Scenario: Discount for only one customer group is disabled and applies to all when that group is deleted
    Given I create a customer group "DiscountGroupA" with the following details:
      | name[en-US]             | Discount Group A |
      | reduction               | 0                |
      | displayPriceTaxExcluded | true             |
      | showPrice               | true             |
      | shopIds                 | shop1            |
    And I create a "cart_level" discount "discountForGroupA" with following properties:
      | name[en-US]            | Discount for Group A   |
      | code                   | GROUP_A_DISCOUNT_40109 |
      | active                 | true                   |
      | reduction_amount       | 10                     |
      | reduction_currency     | usd                    |
      | reduction_tax_included | false                  |
      | customer_groups        | DiscountGroupA         |
    Then discount "discountForGroupA" is enabled and applies only to group "DiscountGroupA"
    When I delete customer group "DiscountGroupA"
    Then discount "discountForGroupA" applies to all customers and is disabled

  Scenario: Discount for two customer groups stays enabled and applies only to remaining group when one group is deleted
    Given I create a customer group "DiscountGroupB" with the following details:
      | name[en-US]             | Discount Group B |
      | reduction               | 0                |
      | displayPriceTaxExcluded | true             |
      | showPrice               | true             |
      | shopIds                 | shop1            |
    And I create a customer group "DiscountGroupC" with the following details:
      | name[en-US]             | Discount Group C |
      | reduction               | 0                |
      | displayPriceTaxExcluded | true             |
      | showPrice               | true             |
      | shopIds                 | shop1            |
    And I create a "cart_level" discount "discountForGroupBOrC" with following properties:
      | name[en-US]            | Discount for Group B or C    |
      | code                   | GROUP_BC_DISCOUNT_40109      |
      | active                 | true                         |
      | reduction_amount       | 15                           |
      | reduction_currency     | usd                          |
      | reduction_tax_included | false                        |
      | customer_groups        | DiscountGroupB,DiscountGroupC |
    Then discount "discountForGroupBOrC" should have the following properties:
      | active          | true                          |
      | customer_groups | DiscountGroupB,DiscountGroupC |
    When I delete customer group "DiscountGroupB"
    Then discount "discountForGroupBOrC" is enabled and applies only to group "DiscountGroupC"

  Scenario: Discount for a customer group remains active in BO but is blocked dynamically in FO when Customer groups feature is disabled
    Given I create a customer group "DiscountGroupD" with the following details:
      | name[en-US]             | Discount Group D |
      | reduction               | 0                |
      | displayPriceTaxExcluded | true             |
      | showPrice               | true             |
      | shopIds                 | shop1            |
    And I create a "cart_level" discount "discountForGroupD" with following properties:
      | name[en-US]            | Discount for Group D   |
      | code                   | GROUP_D_DISCOUNT_40109 |
      | active                 | true                   |
      | reduction_amount       | 20                     |
      | reduction_currency     | usd                    |
      | reduction_tax_included | false                  |
      | customer_groups        | DiscountGroupD         |
    Then discount "discountForGroupD" is enabled and applies only to group "DiscountGroupD"
    When groups feature is deactivated
    Then discount "discountForGroupD" is enabled and applies only to group "DiscountGroupD"
