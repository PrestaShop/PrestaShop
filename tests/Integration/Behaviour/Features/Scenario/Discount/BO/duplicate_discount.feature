# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s discount --tags duplicate-discount
@restore-all-tables-before-feature
@restore-languages-after-feature
@duplicate-discount
Feature: Duplicate discount
  PrestaShop allows BO users to duplicate discounts
  As a BO user
  I must be able to duplicate discounts

  Background:
    Given shop "shop1" with name "test_shop" exists
    Given there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    Given currency "usd" is the default one
    And language with iso code "en" is the default one
    Given group "visitor" named "Visitor" exists
    Given group "guest" named "Guest" exists

  Scenario: Duplicate a discount with code
    When I create a "cart_level" discount "discount_with_code" with following properties:
      | name[en-US]       | Promotion           |
      | active            | true                |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | code              | PROMO_CART_2019     |
      | reduction_percent | 10.0                |
    And I duplicate the discount "discount_with_code" as "discount_with_code_copy"
    Then discount "discount_with_code_copy" should have the following properties:
      | name[en-US]       | copy of Promotion   |
      | type              | cart_level          |
      | active            | false               |
      | valid_from        | 2019-01-01 11:05:00 |
      | valid_to          | 2019-12-01 00:00:00 |
      | reduction_percent | 10.0                |
    And discount "discount_with_code_copy" shouldn't have "PROMO_CART_2019" as code

  Scenario: Duplicate discount with conditions on carriers
    Given I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    And I add new zone "zone2" with following properties:
      | name    | zone2 |
      | enabled | true  |
    Given I create carrier "carrier1" with specified properties:
      | name             | Carrier 1                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | active           | true                               |
      | max_width        | 1454                               |
      | max_height       | 1234                               |
      | max_depth        | 1111                               |
      | max_weight       | 3864                               |
      | group_access     | visitor, guest                     |
      | delay[en-US]     | Shipping delay                     |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | zones            | zone1                              |
      | rangeBehavior    | disabled                           |
    And I create carrier "carrier2" with specified properties:
      | name             | Carrier 2                          |
      | grade            | 1                                  |
      | trackingUrl      | http://example.com/track.php?num=@ |
      | active           | true                               |
      | max_width        | 1454                               |
      | max_height       | 1234                               |
      | max_depth        | 1111                               |
      | max_weight       | 3864                               |
      | group_access     | visitor, guest                     |
      | delay[en-US]     | Shipping delay                     |
      | shippingHandling | false                              |
      | isFree           | true                               |
      | shippingMethod   | weight                             |
      | zones            | zone1                              |
      | rangeBehavior    | disabled                           |
    When I create a "free_shipping" discount "discount_with_carriers" with following properties:
      | name[en-US] | Promotion         |
      | carriers    | carrier1,carrier2 |
    And I duplicate the discount "discount_with_carriers" as "discount_with_carriers_copy"
    Then discount "discount_with_carriers_copy" should have the following properties:
      | name[en-US] | copy of Promotion |
      | active      | false             |
      | carriers    | carrier1,carrier2 |

  Scenario: Duplicate a discount limited to multiple customer groups
    Given I create a customer group "group1" with the following details:
      | name[en-US]             | VIP Members       |
      | reduction               | 0.0               |
      | displayPriceTaxExcluded | false             |
      | showPrice               | true              |
      | shopIds                 | shop1             |
    And I create a customer group "group2" with the following details:
      | name[en-US]             | Premium Members   |
      | reduction               | 0.0               |
      | displayPriceTaxExcluded | false             |
      | showPrice               | true              |
      | shopIds                 | shop1             |
    When I create a "cart_level" discount "discount_with_groups" with following properties:
      | name[en-US]              | VIP and Premium discount |
      | active                   | true                     |
      | valid_from               | 2025-01-01 00:00:00      |
      | valid_to                 | 2025-12-31 23:59:59      |
      | reduction_percent        | 15.0                     |
      | customer_groups          | group1,group2            |
      | total_quantity           | 100                      |
      | quantity_per_user        | 1                        |
      | minimum_product_quantity | 0                        |
    And I duplicate the discount "discount_with_groups" as "discount_with_groups_copy"
    Then discount "discount_with_groups_copy" should have the following properties:
      | name[en-US]       | copy of VIP and Premium discount |
      | type              | cart_level                       |
      | active            | false                            |
      | valid_from        | 2025-01-01 00:00:00              |
      | valid_to          | 2025-12-31 23:59:59              |
      | reduction_percent | 15.0                             |
      | customer_groups   | group1,group2                    |

  Scenario: Duplicate discount with country trigger
    Given country "france" with iso code "FR" exists
    And country "spain" with iso code "ES" exists
    When I create a "free_shipping" discount "discount_with_country" with following properties:
      | name[en-US] | Promotion    |
      | countries   | france,spain |
    And I duplicate the discount "discount_with_country" as "discount_with_country_copy"
    Then discount "discount_with_country_copy" should have the following properties:
      | name[en-US]              | copy of Promotion |
      | type                     | free_shipping     |
      | minimum_product_quantity | 0                 |
      | active                   | false             |
      | countries                | spain,france      |

  Scenario: Duplicate discount with supplier trigger
    Given I add new supplier supplier1 with the following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | shops                   | [shop1]            |
    When I create a "free_shipping" discount "discount_with_supplier" with following properties:
      | name[en-US]                 | Promotion |
      | productConditionQuantity    | 42        |
      | productCondition[suppliers] | supplier1 |
    And I duplicate the discount "discount_with_supplier" as "discount_with_supplier_copy"
    Then discount "discount_with_supplier_copy" should have the following properties:
      | name[en-US]                 | copy of Promotion |
      | type                        | free_shipping     |
      | minimum_product_quantity    | 0                 |
      | active                      | false             |
      | productConditionQuantity    | 42                |
      | productCondition[suppliers] | supplier1         |

  Scenario: Duplicate discount with category trigger
    Given category "home" in default language named "Home" exists
    When I create a "free_shipping" discount "discount_with_category" with following properties:
      | name[en-US]                  | Promotion |
      | productConditionQuantity     | 42        |
      | productCondition[categories] | home      |
    And I duplicate the discount "discount_with_category" as "discount_with_category_copy"
    Then discount "discount_with_category_copy" should have the following properties:
      | name[en-US]                  | copy of Promotion |
      | type                         | free_shipping     |
      | minimum_product_quantity     | 0                 |
      | active                       | false             |
      | productConditionQuantity     | 42                |
      | productCondition[categories] | home              |

  Scenario: Duplicate discount with brand trigger
    Given I add new manufacturer "rocket" with following properties:
      | name                     | Rocket                             |
      | short_description[en-US] | Cigarettes manufacturer            |
      | description[en-US]       | Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare.|
      | meta_title[en-US]        | You smoke - you die!               |
      | meta_description[en-US]  | The sun is shining and the weather is sweet|
      | enabled                  | true                               |
    When I create a "free_shipping" discount "discount_with_brand" with following properties:
      | name[en-US]                     | Promotion |
      | productConditionQuantity        | 42        |
      | productCondition[manufacturers] | rocket    |
    And I duplicate the discount "discount_with_brand" as "discount_with_brand_copy"
    Then discount "discount_with_brand_copy" should have the following properties:
      | name[en-US]                     | copy of Promotion |
      | type                            | free_shipping     |
      | minimum_product_quantity        | 0                 |
      | active                          | false             |
      | productConditionQuantity        | 42                |
      | productCondition[manufacturers] | rocket            |

  Scenario: Duplicate discount with brand and category triggers
    Given I add new manufacturer "rocket" with following properties:
      | name                     | Rocket                             |
      | short_description[en-US] | Cigarettes manufacturer            |
      | description[en-US]       | Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare.|
      | meta_title[en-US]        | You smoke - you die!               |
      | meta_description[en-US]  | The sun is shining and the weather is sweet|
      | enabled                  | true                               |
    And category "home" in default language named "Home" exists
    When I create a "free_shipping" discount "discount_with_brand_and_category" with following properties:
      | name[en-US]                     | Promotion |
      | productConditionQuantity        | 42        |
      | productCondition[manufacturers] | rocket    |
      | productCondition[categories]    | home      |
    And I duplicate the discount "discount_with_brand_and_category" as "discount_with_brand_and_category_copy"
    Then discount "discount_with_brand_and_category_copy" should have the following properties:
      | name[en-US]                     | copy of Promotion |
      | type                            | free_shipping     |
      | minimum_product_quantity        | 0                 |
      | active                          | false             |
      | productConditionQuantity        | 42                |
      | productCondition[manufacturers] | rocket            |
      | productCondition[categories]    | home              |
