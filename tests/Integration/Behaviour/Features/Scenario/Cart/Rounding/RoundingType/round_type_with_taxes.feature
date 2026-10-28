# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cart --tags round-type-with-taxes
@restore-all-tables-before-feature
@round-type-with-taxes
Feature: Cart and order totals with taxed products for each rounding type
  As a merchant
  I must get cart and order totals that follow the configured rounding type,
  and order totals that add up (products + shipping = total paid)

  # product1-3 cost 8.337 tax excluded, i.e. 10.0044 tax included with a 20% tax,
  # product4 costs 4.449 tax excluded, i.e. 4.693695 tax included with a 5.5% tax:
  # rounding each article, each line or the cart total gives different results.
  Background:
    Given email sending is disabled
    And shipping handling fees are set to 2.0
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And there is a product in the catalog named "product1" with a price of 8.337 and 1000 items in stock
    And there is a product in the catalog named "product2" with a price of 8.337 and 1000 items in stock
    And there is a product in the catalog named "product3" with a price of 8.337 and 1000 items in stock
    And there is a product in the catalog named "product4" with a price of 4.449 and 1000 items in stock
    And I add new zone "zone1" with following properties:
      | name    | zone1 |
      | enabled | true  |
    And there is a country named "country1" and iso code "FR" in zone "zone1"
    And there is a state named "state1" with iso code "TEST-1" in country "country1" and zone "zone1"
    And there is an address named "address1" with postcode "1" in state "state1"
    And there is a tax named "tax20" and rate 20.0%
    And there is a tax rule named "taxrule20" in country "country1" and state "state1" where tax "tax20" is applied
    And product "product1" belongs to tax group "taxrule20"
    And product "product2" belongs to tax group "taxrule20"
    And product "product3" belongs to tax group "taxrule20"
    And there is a tax named "tax55" and rate 5.5%
    And there is a tax rule named "taxrule55" in country "country1" and state "state1" where tax "tax55" is applied
    And product "product4" belongs to tax group "taxrule55"
    And there is a customer named "customer1" whose email is "fake@prestashop.com"
    And address "address1" is associated to customer "customer1"
    And I create carrier "carrier1" with specified properties:
      | name  | carrier 1 |
      | zones | zone1     |
    And I set ranges for carrier "carrier1" with specified properties for all shops:
      | id_zone | range_from | range_to | range_price |
      | zone1   | 0          | 10000    | 5.0         |

  Scenario Outline: 3 different products, quantity 1 each, rounding <rounding type>
    Given specific shop configuration for "rounding type" is set to round <rounding type>
    And I have an empty default cart
    When I am logged in as "customer1"
    And I add 1 items of product "product1" in my cart
    And I add 1 items of product "product2" in my cart
    And I add 1 items of product "product3" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier1" in my cart
    Then my cart total should be precisely <total tax incl> tax included
    And my cart total should be precisely <total tax excl> tax excluded
    When I validate my cart using payment module fake
    Then current cart order total for products should be <products tax incl> tax included
    And current cart order total for products should be <products tax excl> tax excluded
    And current cart order shipping fees should be 7.0 tax included
    And current cart order total paid should be <total tax incl> tax included
    And current cart order total paid should be <total tax excl> tax excluded

    Examples:
      | rounding type | products tax incl | products tax excl | total tax incl | total tax excl |
      | each article  | 30.00             | 25.02             | 37.00          | 32.02          |
      | each line     | 30.00             | 25.02             | 37.00          | 32.02          |
      | cart total    | 30.01             | 25.01             | 37.01          | 32.01          |

  Scenario Outline: 1 product, quantity 3, rounding <rounding type>
    Given specific shop configuration for "rounding type" is set to round <rounding type>
    And I have an empty default cart
    When I am logged in as "customer1"
    And I add 3 items of product "product1" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier1" in my cart
    Then my cart total should be precisely <total tax incl> tax included
    And my cart total should be precisely <total tax excl> tax excluded
    When I validate my cart using payment module fake
    Then current cart order total for products should be <products tax incl> tax included
    And current cart order total for products should be <products tax excl> tax excluded
    And current cart order shipping fees should be 7.0 tax included
    And current cart order total paid should be <total tax incl> tax included
    And current cart order total paid should be <total tax excl> tax excluded

    Examples:
      | rounding type | products tax incl | products tax excl | total tax incl | total tax excl |
      | each article  | 30.00             | 25.02             | 37.00          | 32.02          |
      | each line     | 30.01             | 25.01             | 37.01          | 32.01          |
      | cart total    | 30.01             | 25.01             | 37.01          | 32.01          |

  Scenario Outline: 1 product, quantity 100, rounding <rounding type>
    Given specific shop configuration for "rounding type" is set to round <rounding type>
    And I have an empty default cart
    When I am logged in as "customer1"
    And I add 100 items of product "product1" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier1" in my cart
    Then my cart total should be precisely <total tax incl> tax included
    And my cart total should be precisely <total tax excl> tax excluded
    When I validate my cart using payment module fake
    Then current cart order total for products should be <products tax incl> tax included
    And current cart order total for products should be <products tax excl> tax excluded
    And current cart order total paid should be <total tax incl> tax included
    And current cart order total paid should be <total tax excl> tax excluded

    Examples:
      | rounding type | products tax incl | products tax excl | total tax incl | total tax excl |
      | each article  | 1000.00           | 834.00            | 1007.00        | 841.00         |
      | each line     | 1000.44           | 833.70            | 1007.44        | 840.70         |
      | cart total    | 1000.44           | 833.70            | 1007.44        | 840.70         |

  Scenario Outline: 2 products with different tax rates, rounding <rounding type>
    Given specific shop configuration for "rounding type" is set to round <rounding type>
    And I have an empty default cart
    When I am logged in as "customer1"
    And I add 1 items of product "product1" in my cart
    And I add 1 items of product "product4" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier1" in my cart
    Then my cart total should be precisely <total tax incl> tax included
    And my cart total should be precisely <total tax excl> tax excluded
    When I validate my cart using payment module fake
    Then current cart order total for products should be <products tax incl> tax included
    And current cart order total for products should be <products tax excl> tax excluded
    And current cart order total paid should be <total tax incl> tax included
    And current cart order total paid should be <total tax excl> tax excluded

    Examples:
      | rounding type | products tax incl | products tax excl | total tax incl | total tax excl |
      | each article  | 14.69             | 12.79             | 21.69          | 19.79          |
      | each line     | 14.69             | 12.79             | 21.69          | 19.79          |
      | cart total    | 14.70             | 12.79             | 21.70          | 19.79          |

  @restore-cart-rules-after-scenario
  Scenario Outline: 3 different products with a 10% cart rule, rounding <rounding type>
    Given specific shop configuration for "rounding type" is set to round <rounding type>
    And I have an empty default cart
    And there is a cart rule "cartrule10" with following properties:
      | name[en-US]         | cartrule10 |
      | priority            | 1          |
      | discount_percentage | 10         |
    When I am logged in as "customer1"
    And I add 1 items of product "product1" in my cart
    And I add 1 items of product "product2" in my cart
    And I add 1 items of product "product3" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier1" in my cart
    Then my cart total should be precisely <total tax incl> tax included
    And my cart total should be precisely <total tax excl> tax excluded
    When I validate my cart using payment module fake
    Then current cart order total for products should be <products tax incl> tax included
    And current cart order total for products should be <products tax excl> tax excluded
    And current cart order total discount should be 3.00 tax included
    And current cart order total discount should be 2.50 tax excluded
    And current cart order total paid should be <total tax incl> tax included
    And current cart order total paid should be <total tax excl> tax excluded

    Examples:
      | rounding type | products tax incl | products tax excl | total tax incl | total tax excl |
      | each article  | 30.00             | 25.02             | 34.00          | 29.52          |
      | each line     | 30.00             | 25.02             | 34.00          | 29.52          |
      | cart total    | 30.01             | 25.01             | 34.01          | 29.51          |

  @restore-cart-rules-after-scenario
  Scenario Outline: 3 different products with a 5.00 tax included cart rule, rounding <rounding type>
    Given specific shop configuration for "rounding type" is set to round <rounding type>
    And I have an empty default cart
    And there is a cart rule "cartrule5incl" with following properties:
      | name[en-US]           | cartrule5incl |
      | priority              | 1             |
      | discount_amount       | 5             |
      | discount_currency     | usd           |
      | discount_includes_tax | true          |
    When I am logged in as "customer1"
    And I add 1 items of product "product1" in my cart
    And I add 1 items of product "product2" in my cart
    And I add 1 items of product "product3" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier1" in my cart
    Then my cart total should be precisely <total tax incl> tax included
    And my cart total should be precisely <total tax excl> tax excluded
    When I validate my cart using payment module fake
    Then current cart order total for products should be <products tax incl> tax included
    And current cart order total for products should be <products tax excl> tax excluded
    And current cart order total discount should be 5.00 tax included
    And current cart order total discount should be 4.17 tax excluded
    And current cart order total paid should be <total tax incl> tax included
    And current cart order total paid should be <total tax excl> tax excluded

    Examples:
      | rounding type | products tax incl | products tax excl | total tax incl | total tax excl |
      | each article  | 30.00             | 25.02             | 32.00          | 27.85          |
      | each line     | 30.00             | 25.02             | 32.00          | 27.85          |
      | cart total    | 30.01             | 25.01             | 32.01          | 27.84          |

  @restore-cart-rules-after-scenario
  Scenario Outline: 3 different products with a 5.00 tax excluded cart rule, rounding <rounding type>
    Given specific shop configuration for "rounding type" is set to round <rounding type>
    And I have an empty default cart
    And there is a cart rule "cartrule5excl" with following properties:
      | name[en-US]           | cartrule5excl |
      | priority              | 1             |
      | discount_amount       | 5             |
      | discount_currency     | usd           |
      | discount_includes_tax | false         |
    When I am logged in as "customer1"
    And I add 1 items of product "product1" in my cart
    And I add 1 items of product "product2" in my cart
    And I add 1 items of product "product3" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier1" in my cart
    Then my cart total should be precisely <total tax incl> tax included
    And my cart total should be precisely <total tax excl> tax excluded
    When I validate my cart using payment module fake
    Then current cart order total for products should be <products tax incl> tax included
    And current cart order total for products should be <products tax excl> tax excluded
    And current cart order total discount should be 6.00 tax included
    And current cart order total discount should be 5.00 tax excluded
    And current cart order total paid should be <total tax incl> tax included
    And current cart order total paid should be <total tax excl> tax excluded

    Examples:
      | rounding type | products tax incl | products tax excl | total tax incl | total tax excl |
      | each article  | 30.00             | 25.02             | 31.00          | 27.02          |
      | each line     | 30.00             | 25.02             | 31.00          | 27.02          |
      | cart total    | 30.01             | 25.01             | 31.01          | 27.01          |

  @restore-cart-rules-after-scenario
  Scenario Outline: 3 different products with a free shipping cart rule, rounding <rounding type>
    Given specific shop configuration for "rounding type" is set to round <rounding type>
    And I have an empty default cart
    And there is a cart rule "freeshipping" with following properties:
      | name[en-US]   | freeshipping |
      | priority      | 1            |
      | free_shipping | true         |
    When I am logged in as "customer1"
    And I add 1 items of product "product1" in my cart
    And I add 1 items of product "product2" in my cart
    And I add 1 items of product "product3" in my cart
    And I select address "address1" in my cart
    And I select carrier "carrier1" in my cart
    Then my cart total should be precisely <total tax incl> tax included
    And my cart total should be precisely <total tax excl> tax excluded

    Examples:
      | rounding type | total tax incl | total tax excl |
      | each article  | 30.00          | 25.02          |
      | each line     | 30.00          | 25.02          |
      | cart total    | 30.01          | 25.01          |
