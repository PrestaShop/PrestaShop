# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s currency
@reset-database-before-feature
Feature: Currency Management
  PrestaShop allows BO users to manage currencies
  As a BO user
  I must be able to create, edit and delete currencies in my shop

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "language1" with locale "en-US" exists
    And language "language2" with locale "fr-FR" exists

  Scenario: Adding and editing currency
    When I add new currency "currency1" with following properties:
      | iso_code         | EUR       |
      | exchange_rate    | 0.88      |
      | name             | My Euros  |
      | symbol           | €€        |
      | is_enabled       | 1         |
      | is_unofficial    | 0         |
      | shop_association | shop1     |
    Then I should get no error
    And currency "currency1" should be "EUR"
    And currency "currency1" exchange rate should be 0.88
    And currency "currency1" numeric iso code should be 978
    And currency "currency1" name should be "My Euros"
    And currency "currency1" symbol should be "€€"
    And currency "currency1" precision should be 2
    And currency "currency1" should have unofficial false
    And currency "currency1" should have modified true
    And currency "currency1" should have status enabled
    And currency "currency1" should be available in shop "shop1"
    And database contains 1 rows of currency "EUR"
    When I edit currency "currency1" with following properties:
      | iso_code         | EUR   |
      | exchange_rate    | 1.0   |
      | name             | Euro  |
      | symbol           | €     |
      | is_enabled       | 0     |
      | is_unofficial    | 0     |
      | shop_association | shop1 |
    Then currency "currency1" should be "EUR"
    And currency "currency1" exchange rate should be 1
    And currency "currency1" numeric iso code should be 978
    And currency "currency1" name should be "Euro"
    And currency "currency1" symbol should be "€"
    And currency "currency1" precision should be 2
    And currency "currency1" should have unofficial false
    And currency "currency1" should have modified false
    And currency "currency1" should have status disabled
    When I edit currency "currency1" with following properties:
      | precision | 0 |
    Then currency "currency1" should be "EUR"
    And currency "currency1" exchange rate should be 1
    And currency "currency1" numeric iso code should be 978
    And currency "currency1" name should be "Euro"
    And currency "currency1" symbol should be "€"
    And currency "currency1" precision should be 0
    And currency "currency1" should have unofficial false
    And currency "currency1" should have modified false
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
      | iso_code               | GBP              |
      | exchange_rate          | 0.88             |
      | name                   | Pound            |
      | symbol                 | ££               |
      | is_enabled             | 1                |
      | is_unofficial          | 0                |
      | shop_association       | shop1            |
      | transformations[fr-FR] | leftWithoutSpace |
    And database contains 1 rows of currency "GBP"
    And currency "currency4" should have pattern "¤#,##0.00" for language "fr-FR"
    And currency "currency4" should have modified true
    When I delete currency "currency4"
    Then "GBP" currency should be deleted
    And database contains 1 rows of currency "GBP"

  Scenario: Adding a new instance of deleted currency should be allowed (it's not duplicated and values are refreshed)
    Given currency with "GBP" has been deleted
    And database contains 1 rows of currency "GBP"
    When I add new currency "currency5" with following properties:
      | iso_code         | GBP           |
      | exchange_rate    | 0.66          |
      | precision        | 2             |
      | is_enabled       | 0             |
      | is_unofficial    | 0             |
      | shop_association | shop1         |
    Then I should get no error
    And database contains 1 rows of currency "GBP"
    And currency with "GBP" is not deleted
    And currency "currency5" should be "GBP"
    And currency "currency5" exchange rate should be 0.66
    And currency "currency5" numeric iso code should be 826
    And currency "currency5" name should be "British Pound"
    And currency "currency5" symbol should be "£"
    And currency "currency5" precision should be 2
    And currency "currency5" should have unofficial false
    And currency "currency5" should have modified false
    And currency "currency5" should have status disabled
    And currency "currency5" should be available in shop "shop1"
    And currency "currency5" should have pattern empty for language "fr-FR"

  Scenario: Adding invalid unofficial currency
    When I add new currency "currency6" with following properties:
      | iso_code         | EUR      |
      | exchange_rate    | 0.88     |
      | name             | My Euros |
      | symbol           | €        |
      | is_enabled       | 1        |
      | is_unofficial    | 1        |
      | shop_association | shop1    |
    Then I should get error that unofficial currency is invalid

  Scenario: Adding and editing unofficial currency
    When I add new currency "currency7" with following properties:
      | iso_code         | CST              |
      | exchange_rate    | 0.77             |
      | name             | Unofficial Money |
      | symbol           | @                |
      | is_enabled       | 1                |
      | is_unofficial    | 1                |
      | shop_association | shop1            |
    Then I should get no error
    And database contains 1 rows of currency "CST"
    And currency "currency7" should be "CST"
    And currency "currency7" exchange rate should be 0.77
    And currency "currency7" numeric iso code should be null
    And currency "currency7" name should be "Unofficial Money"
    And currency "currency7" symbol should be "@"
    And currency "currency7" precision should be 0
    And currency "currency7" should have unofficial true
    And currency "currency7" should have modified true
    And currency "currency7" should have status enabled
    And currency "currency7" should be available in shop "shop1"
    When I edit currency "currency7" with following properties:
      | iso_code         | CUS           |
      | exchange_rate    | 0.66          |
      | precision        | 2             |
      | is_enabled       | 0             |
      | is_unofficial    | 1             |
      | shop_association | shop1         |
    Then I should get no error
    And database contains 0 rows of currency "CST"
    And database contains 1 rows of currency "CUS"
    And currency "currency7" should be "CUS"
    And currency "currency7" exchange rate should be 0.66
    And currency "currency7" numeric iso code should be null
    And currency "currency7" name should be "Unofficial Money"
    And currency "currency7" symbol should be "@"
    And currency "currency7" precision should be 2
    And currency "currency7" should have unofficial true
    And currency "currency7" should have modified true
    And currency "currency7" should have status disabled
    And currency "currency7" should be available in shop "shop1"
    When I edit currency "currency7" with following properties:
      | iso_code         | CUS           |
      | exchange_rate    | 0.88          |
      | precision        | 3             |
      | is_enabled       | 1             |
      | is_unofficial    | 1             |
      | shop_association | shop1         |
    Then I should get no error
    And currency "currency7" should be "CUS"
    And currency "currency7" exchange rate should be 0.88
    And currency "currency7" numeric iso code should be null
    And currency "currency7" name should be "Unofficial Money"
    And currency "currency7" symbol should be "@"
    And currency "currency7" precision should be 3
    And currency "currency7" should have unofficial true
    And currency "currency7" should have modified true
    And currency "currency7" should have status enabled
    And currency "currency7" should be available in shop "shop1"

  Scenario: Adding then editing invalid unofficial currency
    When I add new currency "currency8" with following properties:
      | iso_code         | CST              |
      | exchange_rate    | 0.77             |
      | precision        | 1                |
      | name             | Unofficial Money |
      | symbol           | @                |
      | is_enabled       | 1                |
      | is_unofficial    | 1                |
      | shop_association | shop1            |
    Then I should get no error
    And database contains 1 rows of currency "CST"
    And currency "currency8" should be "CST"
    And currency "currency8" exchange rate should be 0.77
    And currency "currency8" numeric iso code should be null
    And currency "currency8" name should be "Unofficial Money"
    And currency "currency8" symbol should be "@"
    And currency "currency8" precision should be 1
    And currency "currency8" should have unofficial true
    And currency "currency8" should have modified true
    And currency "currency8" should have status enabled
    And currency "currency8" should be available in shop "shop1"
    When I edit currency "currency8" with following properties:
      | iso_code         | EUR   |
      | exchange_rate    | 0.66  |
      | is_enabled       | 0     |
      | is_unofficial    | 1     |
      | shop_association | shop1 |
    Then I should get error that unofficial currency is invalid

  Scenario: Adding existing currency should not be allowed
    Given currency "currency9" with "USD" exists
    When I add new currency "currency10" with following properties:
      | iso_code         | USD       |
      | exchange_rate    | 0.88      |
      | name             | US Dollar |
      | symbol           | $         |
      | is_enabled       | 1         |
      | is_unofficial    | 0         |
      | shop_association | shop1     |
    Then I should get error that currency already exists

  Scenario: Editing unofficial currency with existing unofficial ISO code should not be allowed
    Given currency "currency11" with "CST" exists
    Given currency "currency12" with "CUS" exists
    Then currency "currency11" should be "CST"
    And currency "currency11" numeric iso code should be null
    Then currency "currency12" should be "CUS"
    And currency "currency12" numeric iso code should be null
    And database contains 1 rows of currency "CST"
    And database contains 1 rows of currency "CUS"
    When I edit currency "currency11" with following properties:
      | iso_code         | CUS   |
      | exchange_rate    | 0.66  |
      | is_enabled       | 0     |
      | is_unofficial    | 1     |
      | shop_association | shop1 |
    Then I should get error that currency already exists
    And database contains 1 rows of currency "CST"
    And database contains 1 rows of currency "CUS"

  Scenario: Adding real currency with basic input is automatically filled
    When I add new currency "currency14" with following properties:
      | iso_code         | AUD       |
      | exchange_rate    | 0.88      |
      | is_enabled       | 1         |
      | is_unofficial    | 0         |
      | shop_association | shop1     |
    Then I should get no error
    And currency "currency14" should be "AUD"
    And currency "currency14" exchange rate should be 0.88
    And currency "currency14" numeric iso code should be 036
    And currency "currency14" name should be "Australian Dollar"
    And currency "currency14" symbol should be "$"
    And currency "currency14" precision should be 2
    And currency "currency14" should have unofficial false
    And currency "currency14" should have modified false
    And currency "currency14" should have status enabled
    And currency "currency14" should be available in shop "shop1"

  Scenario: Adding unofficial currency with basic input
    When I add new currency "currency15" with following properties:
      | iso_code         | BTC       |
      | exchange_rate    | 0.88      |
      | is_enabled       | 1         |
      | is_unofficial    | 1         |
      | shop_association | shop1     |
    Then I should get no error
    And currency "currency15" should be "BTC"
    And currency "currency15" exchange rate should be 0.88
    And currency "currency15" numeric iso code should be null
    And currency "currency15" name should be "BTC"
    And currency "currency15" symbol should be "BTC"
    And currency "currency15" precision should be 0
    And currency "currency15" should have unofficial true
    And currency "currency15" should have modified true
    And currency "currency15" should have status enabled
    And currency "currency15" should be available in shop "shop1"

  Scenario: Adding and edit official currency with custom pattern
    When I add new currency "currency16" with following properties:
      | iso_code               | JPY           |
      | exchange_rate          | 0.08          |
      | is_enabled             | 1             |
      | is_unofficial          | 0             |
      | shop_association       | shop1         |
      | transformations[fr-FR] | leftWithSpace |
    Then I should get no error
    And currency "currency16" should be "JPY"
    And currency "currency16" exchange rate should be 0.08
    And currency "currency16" numeric iso code should be 392
    And currency "currency16" name should be "Japanese Yen"
    And currency "currency16" symbol should be "¥"
    And currency "currency16" precision should be 0
    And currency "currency16" should have unofficial false
    And currency "currency16" should have modified false
    And currency "currency16" should have status enabled
    And currency "currency16" should be available in shop "shop1"
    And currency "currency16" should have pattern "¤ #,##0.00" for language "fr-FR"
    And currency "currency16" should have pattern empty for language "en-EN"
    And database contains 1 rows of currency "JPY"
    When I edit currency "currency16" with following properties:
      | iso_code               | JPY            |
      | exchange_rate          | 0.08           |
      | is_enabled             | 1              |
      | is_unofficial          | 0              |
      | shop_association       | shop1          |
      | transformations[en-US] | rightWithSpace |
    And currency "currency16" should be "JPY"
    And currency "currency16" exchange rate should be 0.08
    And currency "currency16" numeric iso code should be 392
    And currency "currency16" name should be "Japanese Yen"
    And currency "currency16" symbol should be "¥"
    And currency "currency16" precision should be 0
    And currency "currency16" should have unofficial false
    And currency "currency16" should have modified false
    And currency "currency16" should have status enabled
    And currency "currency16" should be available in shop "shop1"
    And currency "currency16" should have pattern empty for language "fr-FR"
    And currency "currency16" should have pattern "#,##0.00 ¤" for language "en-US"

  Scenario: Adding and edit unofficial currency with custom pattern
    When I add new currency "currency17" with following properties:
      | iso_code               | JPP               |
      | name                   | Japan Private Yen |
      | symbol                 | Yen               |
      | exchange_rate          | 0.8               |
      | is_enabled             | 1                 |
      | is_unofficial          | 1                 |
      | shop_association       | shop1             |
      | transformations[fr-FR] | leftWithoutSpace  |
    Then I should get no error
    And currency "currency17" should be "JPP"
    And currency "currency17" exchange rate should be 0.8
    And currency "currency17" numeric iso code should be null
    And currency "currency17" name should be "Japan Private Yen"
    And currency "currency17" symbol should be "Yen"
    And currency "currency17" precision should be 0
    And currency "currency17" should have unofficial true
    And currency "currency17" should have modified true
    And currency "currency17" should have status enabled
    And currency "currency17" should be available in shop "shop1"
    And currency "currency17" should have pattern "¤#,##0.00" for language "fr-FR"
    And currency "currency17" should have pattern empty for language "en-EN"
    And database contains 1 rows of currency "JPP"
    When I edit currency "currency17" with following properties:
      | iso_code               | JPP               |
      | exchange_rate          | 0.8               |
      | is_enabled             | 1                 |
      | is_unofficial          | 1                 |
      | shop_association       | shop1             |
      | transformations[en-US] | rightWithoutSpace |
    And currency "currency17" should be "JPP"
    And currency "currency17" exchange rate should be 0.8
    And currency "currency17" numeric iso code should be null
    And currency "currency17" name should be "Japan Private Yen"
    And currency "currency17" symbol should be "Yen"
    And currency "currency17" precision should be 0
    And currency "currency17" should have unofficial true
    And currency "currency17" should have modified true
    And currency "currency17" should have status enabled
    And currency "currency17" should be available in shop "shop1"
    And currency "currency17" should have pattern empty for language "fr-FR"
    And currency "currency17" should have pattern "#,##0.00¤" for language "en-US"

  Scenario: Adding a new instance of deleted unofficial currency should be allowed (it's not duplicated and values are refreshed)
    Given I delete currency "currency17"
    And currency with "JPP" has been deleted
    And database contains 1 rows of currency "JPP"
    When I add new currency "currency18" with following properties:
      | iso_code         | JPP   |
      | exchange_rate    | 0.66  |
      | precision        | 2     |
      | is_enabled       | 0     |
      | is_unofficial    | 1     |
      | shop_association | shop1 |
    Then I should get no error
    And database contains 1 rows of currency "JPP"
    And currency with "JPP" is not deleted
    And currency "currency18" should be "JPP"
    And currency "currency18" exchange rate should be 0.66
    And currency "currency18" numeric iso code should be null
    And currency "currency18" name should be "JPP"
    And currency "currency18" symbol should be "JPP"
    And currency "currency18" precision should be 2
    And currency "currency18" should have unofficial true
    And currency "currency18" should have modified true
    And currency "currency18" should have status disabled
    And currency "currency18" should be available in shop "shop1"
    And currency "currency18" should have pattern empty for language "fr-FR"
    And currency "currency18" should have pattern empty for language "en-US"

  Scenario: Adding invalid named currency
    When I add new currency "currency19" with following properties:
      | iso_code         | IVL       |
      | exchange_rate    | 0.88      |
      | name             | <>        |
      | symbol           | €         |
      | is_enabled       | 1         |
      | is_unofficial    | 1         |
      | shop_association | shop1     |
    Then I should get error that currency name is invalid
    When I add new currency "currency19" with following properties:
      | iso_code         | AOA       |
      | exchange_rate    | 0.88      |
      | name             | <>        |
      | symbol           | €         |
      | is_enabled       | 1         |
      | is_unofficial    | 0         |
      | shop_association | shop1     |
    Then I should get error that currency name is invalid
    When I edit currency "currency17" with following properties:
      | iso_code               | JPP               |
      | exchange_rate          | 0.8               |
      | name                   | <>                |
      | is_enabled             | 1                 |
      | is_unofficial          | 1                 |
      | shop_association       | shop1             |
      | transformations[en-US] | rightWithoutSpace |
    Then I should get error that currency name is invalid
    When I edit currency "currency16" with following properties:
      | iso_code               | JPY            |
      | exchange_rate          | 0.08           |
      | name                   | <>             |
      | is_enabled             | 1              |
      | is_unofficial          | 0              |
      | shop_association       | shop1          |
      | transformations[en-US] | rightWithSpace |
    Then I should get error that currency name is invalid
