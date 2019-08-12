# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s currency
@reset-database-before-feature
Feature: Currency Management
  PrestaShop allows BO users to manage currencies
  As a BO user
  I must be able to create, edit and delete currencies in my shop

  Background:
    Given shop "shop1" with name "test_shop" exists

  Scenario: Adding and editing currency
    When I add new currency "currency1" with following properties:
      | iso_code         | EUR       |
      | numeric_iso_code | 978       |
      | exchange_rate    | 0.88      |
      | name             | My Euros  |
      | symbol           | €€        |
      | is_enabled       | 1         |
      | is_custom        | 0         |
      | shop_association | shop1     |
    Then currency "currency1" should be "EUR"
    And currency "currency1" exchange rate should be 0.88
    And currency "currency1" numeric iso code should be 978
    And currency "currency1" name should be "My Euros"
    And currency "currency1" symbol should be "€€"
    And currency "currency1" should have custom false
    And currency "currency1" should have edited true
    And currency "currency1" should have status enabled
    And currency "currency1" should be available in shop "shop1"
    And database contains 1 rows of currency "EUR"
    When I edit currency "currency1" with following properties:
      | iso_code         | GBP       |
      | numeric_iso_code | 978       |
      | exchange_rate    | 0.88      |
      | is_enabled       | 1         |
      | is_custom        | 0         |
      | shop_association | shop1     |
    Then I should get error that isoCode is immutable
    When I edit currency "currency1" with following properties:
      | iso_code         | EUR       |
      | numeric_iso_code | 826       |
      | exchange_rate    | 0.88      |
      | is_enabled       | 1         |
      | is_custom        | 0         |
      | shop_association | shop1     |
    Then I should get error that numericIsoCode is immutable
    When I edit currency "currency1" with following properties:
      | iso_code         | EUR       |
      | numeric_iso_code | 978       |
      | exchange_rate    | 0.88      |
      | is_enabled       | 1         |
      | is_custom        | 1         |
      | shop_association | shop1     |
    Then I should get error that real is immutable
    When I edit currency "currency1" with following properties:
      | iso_code         | EUR       |
      | numeric_iso_code | 978       |
      | exchange_rate    | 1.0       |
      | name             | Euro      |
      | symbol           | €         |
      | is_enabled       | 0         |
      | is_custom        | 0         |
      | shop_association | shop1     |
    Then currency "currency1" should be "EUR"
    And currency "currency1" exchange rate should be 1
    And currency "currency1" numeric iso code should be 978
    And currency "currency1" name should be "Euro"
    And currency "currency1" symbol should be "€"
    And currency "currency1" should have custom false
    And currency "currency1" should have edited false
    And currency "currency1" should have status disabled

  Scenario: Disabling default currency should not be allowed
    Given currency "currency2" with "USD" exists
    And currency "currency2" is default in "shop1" shop
    When I disable currency "currency2"
    Then I should get error that default currency cannot be disabled

  Scenario: Deleting default currency should not be allowed
    Given currency "currency3" with "USD" exists
    And currency "currency3" is default in "shop1" shop
    When I delete currency "currency3"
    Then I should get error that default currency cannot be deleted

  Scenario: Deleting non default currency should be allowed
    When I add new currency "currency4" with following properties:
      | iso_code         | GBP           |
      | numeric_iso_code | 826           |
      | exchange_rate    | 0.88          |
      | name             | British Pound |
      | symbol           | £             |
      | is_enabled       | 1             |
      | is_custom        | 0             |
      | shop_association | shop1         |
    When I delete currency "currency4"
    Then "GBP" currency should be deleted

  Scenario: Adding a new instance of deleted currency should be allowed
    Given currency with "GBP" has been deleted
    When I add new currency "currency5" with following properties:
      | iso_code         | GBP           |
      | numeric_iso_code | 826           |
      | exchange_rate    | 0.88          |
      | name             | British Pound |
      | symbol           | £             |
      | is_enabled       | 1             |
      | is_custom        | 0             |
      | shop_association | shop1         |
    Then currency "currency5" should be "GBP"
    And currency "currency5" exchange rate should be 0.88
    And currency "currency5" numeric iso code should be 826
    And currency "currency5" name should be "British Pound"
    And currency "currency5" symbol should be "£"
    And currency "currency5" should have custom false
    And currency "currency5" should have edited false
    And currency "currency5" should have status enabled
    And currency "currency5" should be available in shop "shop1"

  Scenario: Adding invalid custom currency
    When I add new currency "currency6" with following properties:
      | iso_code         | EUR       |
      | numeric_iso_code | 978       |
      | exchange_rate    | 0.88      |
      | name             | My Euros  |
      | symbol           | €         |
      | is_enabled       | 1         |
      | is_custom        | 1         |
      | shop_association | shop1     |
    Then I should get error that custom currency has invalid isoCode
    When I add new currency "currency6" with following properties:
      | iso_code         | CST       |
      | numeric_iso_code | 978       |
      | exchange_rate    | 0.88      |
      | name             | My Euros  |
      | symbol           | €         |
      | is_enabled       | 1         |
      | is_custom        | 1         |
      | shop_association | shop1     |
    Then I should get error that custom currency has invalid numericIsoCode

  Scenario: Adding and editing custom currency
    When I add new currency "currency7" with following properties:
      | iso_code         | CST          |
      | numeric_iso_code | 777          |
      | exchange_rate    | 0.77         |
      | name             | Custom Money |
      | symbol           | @            |
      | is_enabled       | 1            |
      | is_custom        | 1            |
      | shop_association | shop1        |
    Then currency "currency7" should be "CST"
    And currency "currency7" exchange rate should be 0.77
    And currency "currency7" numeric iso code should be 777
    And currency "currency7" name should be "Custom Money"
    And currency "currency7" symbol should be "@"
    And currency "currency7" should have custom true
    And currency "currency7" should have edited true
    And currency "currency7" should have status enabled
    And currency "currency7" should be available in shop "shop1"
    When I edit currency "currency7" with following properties:
      | iso_code         | CUS           |
      | numeric_iso_code | 666           |
      | exchange_rate    | 0.66          |
      | is_enabled       | 0             |
      | is_custom        | 1             |
      | shop_association | shop1         |
    Then currency "currency7" should be "CUS"
    And currency "currency7" exchange rate should be 0.66
    And currency "currency7" numeric iso code should be 666
    And currency "currency7" name should be "Custom Money"
    And currency "currency7" symbol should be "@"
    And currency "currency7" should have custom true
    And currency "currency7" should have edited true
    And currency "currency7" should have status disabled
    And currency "currency7" should be available in shop "shop1"

  Scenario: Adding then editing invalid custom currency
    When I add new currency "currency8" with following properties:
      | iso_code         | CST          |
      | numeric_iso_code | 777          |
      | exchange_rate    | 0.77         |
      | name             | Custom Money |
      | symbol           | @            |
      | is_enabled       | 1            |
      | is_custom        | 1            |
      | shop_association | shop1        |
    Then currency "currency8" should be "CST"
    And currency "currency8" exchange rate should be 0.77
    And currency "currency8" numeric iso code should be 777
    And currency "currency8" name should be "Custom Money"
    And currency "currency8" symbol should be "@"
    And currency "currency8" should have custom true
    And currency "currency8" should have edited true
    And currency "currency8" should have status enabled
    And currency "currency8" should be available in shop "shop1"
    When I edit currency "currency8" with following properties:
      | iso_code         | EUR           |
      | numeric_iso_code | 666           |
      | exchange_rate    | 0.66          |
      | is_enabled       | 0             |
      | is_custom        | 1             |
      | shop_association | shop1         |
    Then I should get error that custom currency has invalid isoCode
    When I edit currency "currency8" with following properties:
      | iso_code         | CST           |
      | numeric_iso_code | 978           |
      | exchange_rate    | 0.66          |
      | is_enabled       | 0             |
      | is_custom        | 1             |
      | shop_association | shop1         |
    Then I should get error that custom currency has invalid numericIsoCode
    When I edit currency "currency8" with following properties:
      | iso_code         | CST          |
      | numeric_iso_code | 777          |
      | exchange_rate    | 0.66          |
      | is_enabled       | 0             |
      | is_custom        | 0             |
      | shop_association | shop1         |
    Then I should get error that custom is immutable

  Scenario: Adding existing currency should not be allowed
    Given currency "currency9" with "USD" exists
    When I add new currency "currency10" with following properties:
      | iso_code         | USD       |
      | numeric_iso_code | 840       |
      | exchange_rate    | 0.88      |
      | name             | US Dollar |
      | symbol           | $         |
      | is_enabled       | 1         |
      | is_custom        | 0         |
      | shop_association | shop1     |
    Then I should get error that currency already exists

  Scenario: Editing custom currency with existing ISO code or numeric ISO code should not be allowed
    Given currency "currency11" with "CST" exists
    Given currency "currency12" with "CUS" exists
    Then currency "currency11" should be "CST"
    And currency "currency11" numeric iso code should be 777
    Then currency "currency12" should be "CUS"
    And currency "currency12" numeric iso code should be 666
    When I edit currency "currency11" with following properties:
      | iso_code         | CUS           |
      | numeric_iso_code | 777           |
      | exchange_rate    | 0.66          |
      | is_enabled       | 0             |
      | is_custom        | 1             |
      | shop_association | shop1         |
    Then I should get error that currency already exists
    When I edit currency "currency11" with following properties:
      | iso_code         | CST           |
      | numeric_iso_code | 666           |
      | exchange_rate    | 0.66          |
      | is_enabled       | 0             |
      | is_custom        | 1             |
      | shop_association | shop1         |
    Then I should get error that currency already exists

  Scenario: Adding real currency whose ISO code and numeric iso code don't match should not be allowed
    When I add new currency "currency13" with following properties:
      | iso_code         | AUD               |
      | numeric_iso_code | 44                |
      | exchange_rate    | 1.656967          |
      | name             | Australian Dollar |
      | symbol           | $                 |
      | is_enabled       | 1                 |
      | is_custom        | 0                 |
      | shop_association | shop1             |
    Then I should get error that currency iso codes don't match
    When I add new currency "currency13" with following properties:
      | iso_code         | USD               |
      | numeric_iso_code | 36                |
      | exchange_rate    | 1.656967          |
      | name             | Australian Dollar |
      | symbol           | $                 |
      | is_enabled       | 1                 |
      | is_custom        | 0                 |
      | shop_association | shop1             |
    Then I should get error that currency iso codes don't match
    When I add new currency "currency13" with following properties:
      | iso_code         | ASD               |
      | numeric_iso_code | 36                |
      | exchange_rate    | 1.656967          |
      | name             | Australian Dollar |
      | symbol           | $                 |
      | is_enabled       | 1                 |
      | is_custom        | 0                 |
      | shop_association | shop1             |
    Then I should get error that currency iso codes don't match
