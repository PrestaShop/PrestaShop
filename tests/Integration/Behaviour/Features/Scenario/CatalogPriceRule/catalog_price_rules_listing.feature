#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s catalog_price_rule --tags catalog-price-rule-listing
@restore-products-before-feature
@clear-cache-before-feature
@catalog-price-rule
@catalog-price-rule-listing

Feature: List catalog price rules for product in Back Office (BO)
  As an employee
  I need to be able to see all catalog price rules in product page

  Background:
    Given language with iso code "en" is the default one
    And shop "testShop" with name "test_shop" exists
    And there is a currency named "usd" with iso code "USD" and exchange rate of 0.92
    And country "UnitedStates" with iso code "US" exists
    And group "visitor" named "Visitor" exists
    And category "home" in default language named "Home" exists
    And category "home" is the default one
    And category "women" in default language named "Women" exists
    And category "accessories" in default language named "Accessories" exists
    And manufacturer studioDesign named "Studio Design" exists
    And attribute group "Color" named "Color" in en language exists
    And attribute "Red" named "Red" in en language exists
    And attribute "Blue" named "Blue" in en language exists

  Scenario: I can see a list of catalog price rules
    Given I add product "product1" with following information:
      | name[en-US] | dress    |
      | type        | standard |
    And I add product "product2" with following information:
      | name[en-US] | skirt    |
      | type        | standard |
    And I add catalog price rule "catalogPriceRuleReference1" with following details:
      | name            | catalog price rule 1 |
      | currency        | usd                  |
      | country         | UnitedStates         |
      | group           | visitor              |
      | from quantity   | 1                    |
      | reduction type  | amount               |
      | reduction value | 111.50               |
      | shop            | testShop             |
      | includes tax    | true                 |
      | price           | 50                   |
    Then catalog price rule "catalogPriceRuleReference1" should have following details:
      | catalog price rule detail | value                |
      | name                      | catalog price rule 1 |
      | currency                  | usd                  |
      | country                   | UnitedStates         |
      | group                     | visitor              |
      | from quantity             | 1                    |
      | reduction type            | amount               |
      | reduction value           | 111.50               |
      | shop                      | testShop             |
      | includes tax              | true                 |
      | price                     | 50                   |
    When I add catalog price rule "catalogPriceRuleReference2" with following details:
      | name            | catalog price rule 2 |
      | currency        | usd                  |
      | country         | UnitedStates         |
      | group           | visitor              |
      | from quantity   | 1                    |
      | reduction type  | amount               |
      | reduction value | 50                   |
      | shop            | testShop             |
      | includes tax    | true                 |
      | price           | 10                   |
    Then catalog price rule "catalogPriceRuleReference2" should have following details:
      | catalog price rule detail | value                |
      | name                      | catalog price rule 2 |
      | currency                  | usd                  |
      | country                   | UnitedStates         |
      | group                     | visitor              |
      | from quantity             | 1                    |
      | reduction type            | amount               |
      | reduction value           | 50                   |
      | shop                      | testShop             |
      | includes tax              | true                 |
      | price                     | 10                   |
    When I add catalog price rule "catalogPriceRuleReference3" with following details:
      | name            | catalog price rule 3 |
      | currency        | usd                  |
      | country         | UnitedStates         |
      | group           | visitor              |
      | from quantity   | 5                    |
      | reduction type  | amount               |
      | reduction value | 10                   |
      | shop            | testShop             |
      | includes tax    | true                 |
      | price           | 20                   |
    And I add catalog price rule "catalogPriceRuleReference4" with following details:
      | name            | catalog price rule 4 |
      | currency        | usd                  |
      | country         | UnitedStates         |
      | group           | visitor              |
      | from quantity   | 5                    |
      | reduction type  | amount               |
      | reduction value | 10                   |
      | shop            | testShop             |
      | includes tax    | true                 |
      | price           | 20                   |
    And I add catalog price rule "catalogPriceRuleReference5" with following details:
      | name            | catalog price rule 5 |
      | currency        | usd                  |
      | country         | UnitedStates         |
      | group           | visitor              |
      | from quantity   | 5                    |
      | reduction type  | amount               |
      | reduction value | 10                   |
      | shop            | testShop             |
      | includes tax    | true                 |
      | price           | 20                   |
    Then I should be able to see following list of catalog price rules with language "en" with limit 2 offset 0 and total 5 and product "product1":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 1 | usd      | UnitedStates | visitor | 1             | amount         | 111.50          | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd      | UnitedStates | visitor | 1             | amount         | 50              | testShop | true         | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 5 and product "product1":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 1 | usd      | UnitedStates | visitor | 1             | amount         | 111.50          | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd      | UnitedStates | visitor | 1             | amount         | 50              | testShop | true         | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 5 and product "product2":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 1 | usd      | UnitedStates | visitor | 1             | amount         | 111.50          | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd      | UnitedStates | visitor | 1             | amount         | 50              | testShop | true         | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 2 and total 5 and product "product1":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |

#  Here I keep adding conditions to catalog price rules and make product1 satisfy that condition.
#  So catalog price rule list should stay the same for product1 and keep getting smaller for product2
  Scenario: I can see correctly with category condition
    When I add following conditions to catalog price rule "catalogPriceRuleReference1":
      | type     | value |
      | category | women |
    And I assign product product1 to following categories:
      | categories       | [home, women] |
      | default category | women         |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 5 and product "product1":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 1 | usd      | UnitedStates | visitor | 1             | amount         | 111.50          | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd      | UnitedStates | visitor | 1             | amount         | 50              | testShop | true         | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 4 and product "product2":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 2 | usd      | UnitedStates | visitor | 1             | amount         | 50              | testShop | true         | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |

  Scenario: I can see correctly with supplier condition
    When I add new supplier supplier1 with the following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | shops                   | [testShop]         |
    And I associate suppliers to product "product1"
      | supplier  | product_supplier  |
      | supplier1 | product1Supplier1 |
    And I add following conditions to catalog price rule "catalogPriceRuleReference2":
      | type     | value     |
      | supplier | supplier1 |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 5 and product "product1":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 1 | usd      | UnitedStates | visitor | 1             | amount         | 111.50          | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd      | UnitedStates | visitor | 1             | amount         | 50              | testShop | true         | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 3 and product "product2":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |

  Scenario: I can see correctly with manufacturer condition
    When I update product "product1" with following values:
      | manufacturer | studioDesign |
    And I add following conditions to catalog price rule "catalogPriceRuleReference3":
      | type         | value        |
      | manufacturer | studioDesign |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 5 and product "product1":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 1 | usd      | UnitedStates | visitor | 1             | amount         | 111.50          | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd      | UnitedStates | visitor | 1             | amount         | 50              | testShop | true         | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 2 and product "product2":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |

  Scenario: I can see correctly with feature condition
    When I create product feature "testFeature" with specified properties:
      | name[en-US]      | test     |
      | associated shops | testShop |
    And I create feature value "testFeatureValue" for feature "testFeature" with following properties:
      | value[en-US] | Value |
    And I set to product "product1" the following feature values:
      | feature     | feature_value    |
      | testFeature | testFeatureValue |
    Then I add following conditions to catalog price rule "catalogPriceRuleReference4":
      | type    | value            |
      | feature | testFeatureValue |
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 5 and product "product1":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 1 | usd      | UnitedStates | visitor | 1             | amount         | 111.50          | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd      | UnitedStates | visitor | 1             | amount         | 50              | testShop | true         | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 1 and product "product2":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |

  Scenario: I can see correctly with attribute condition
    When I add product product3 with following information:
      | name[en-US] | Combination product |
      | type        | combinations        |
    And I generate combinations for product product3 using following attributes:
      | Color | [Red,Blue] |
    Then I add following conditions to catalog price rule "catalogPriceRuleReference5":
      | type      | value |
      | attribute | Blue  |
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 4 and product "product1":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 1 | usd      | UnitedStates | visitor | 1             | amount         | 111.50          | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd      | UnitedStates | visitor | 1             | amount         | 50              | testShop | true         | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And "product2" should have no catalog price rules with language "en"
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 1 and product "product3":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |

  Scenario: I can see correctly filtered list with one condition group with multiple conditions
    When I add catalog price rule "catalogPriceRuleReference6" with following details:
      | name            | catalog price rule 6 |
      | currency        | usd                  |
      | country         | UnitedStates         |
      | group           | visitor              |
      | from quantity   | 5                    |
      | reduction type  | amount               |
      | reduction value | 10                   |
      | shop            | testShop             |
      | includes tax    | true                 |
      | price           | 20                   |
    And I add following conditions to catalog price rule "catalogPriceRuleReference6":
      | type     | value            |
      | category | women            |
      | feature  | testFeatureValue |
      | supplier | supplier1        |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 5 and product "product1":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 1 | usd      | UnitedStates | visitor | 1             | amount         | 111.50          | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd      | UnitedStates | visitor | 1             | amount         | 50              | testShop | true         | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 6 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And "product2" should have no catalog price rules with language "en"
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 1 and product "product3":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |

  Scenario: I can see correct list with multiple condition groups
    When I add catalog price rule "catalogPriceRuleReference7" with following details:
      | name            | catalog price rule 7 |
      | currency        | usd                  |
      | country         | UnitedStates         |
      | group           | visitor              |
      | from quantity   | 5                    |
      | reduction type  | amount               |
      | reduction value | 10                   |
      | shop            | testShop             |
      | includes tax    | true                 |
      | price           | 20                   |
    And I add following conditions to catalog price rule "catalogPriceRuleReference7":
      | type      | value |
      | attribute | Blue  |
    And I add following conditions to catalog price rule "catalogPriceRuleReference7":
      | type     | value            |
      | category | women            |
      | feature  | testFeatureValue |
      | supplier | supplier1        |
    Then I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 6 and product "product1":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 1 | usd      | UnitedStates | visitor | 1             | amount         | 111.50          | testShop | true         | 50    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 2 | usd      | UnitedStates | visitor | 1             | amount         | 50              | testShop | true         | 10    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 3 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 4 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 6 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 7 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
    And "product2" should have no catalog price rules with language "en"
    And I should be able to see following list of catalog price rules with language "en" with limit 50 offset 0 and total 2 and product "product3":
      | name                 | currency | country      | group   | from quantity | reduction type | reduction value | shop     | includes tax | price | from                | to                  |
      | catalog price rule 5 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
      | catalog price rule 7 | usd      | UnitedStates | visitor | 5             | amount         | 10              | testShop | true         | 20    | 0000-00-00 00:00:00 | 0000-00-00 00:00:00 |
