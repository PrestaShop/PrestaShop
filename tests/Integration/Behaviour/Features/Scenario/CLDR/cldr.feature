# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cldr
@reset-database-before-feature
Feature: CLDR display for prices

  Background:
    Given shop "shop1" with name "test_shop" exists

  Scenario: Display EUR
    When I add a new language "language1" with following properties:
      | name             | English (US) |
      | iso_code         | en           |
      | language_code    | en-us        |
      | locale           | en-US        |
      | date_format_lite | m/d/Y        |
      | date_format_full | m/d/Y H:i:s  |
      | is_rtl           | 0            |
    And I add a new language "language2" with following properties:
      | name             | French       |
      | iso_code         | fr           |
      | language_code    | fr-fr        |
      | locale           | fr-FR        |
      | date_format_lite | d/m/Y        |
      | date_format_full | d/m/Y H:i:s  |
      | is_rtl           | 0            |
    And I add new currency "currency1" with following properties:
      | iso_code         | EUR           |
      | exchange_rate    | 0.63          |
      | is_enabled       | 1             |
      | shop_association | shop1         |
    Then language "language1" should be "en-US"
    Then language "language2" should be "fr-FR"
    And currency "currency1" should be "EUR"
    And display a price of 14789.5426 "EUR" with locale "en-US" should look like "€14,789.54"
    And display a price of 14789.5426 "EUR" with locale "fr-FR" should look like "14 789,54 €"

  Scenario: Display a deleted currency
    Given language "language1" should be "en-US"
    And currency "currency1" should be "EUR"
    And there should be 1 currencies of "EUR"
    And display a price of 14789.5426 "EUR" with locale "en-US" should look like "€14,789.54"
    When I delete currency "currency1"
    Then display a price of 14789.5426 "EUR" with locale "en-US" should look like "€14,789.54"
    # Check that the CLDR doesn't add the currency in database
    And there should be 1 currencies of "EUR"

  Scenario: Display a disabled currency
    Given language "language1" should be "en-US"
    And I add new currency "currency1" with following properties:
      | iso_code         | USD           |
      | exchange_rate    | 0.89          |
      | is_enabled       | 1             |
      | shop_association | shop1         |
    And there should be 1 currencies of "USD"
    And display a price of 14789.5426 "USD" with locale "en-US" should look like "$14,789.54"
    When I disable currency "currency1"
    Then display a price of 14789.5426 "USD" with locale "en-US" should look like "$14,789.54"
    # Check that the CLDR doesn't add the currency in database
    And there should be 1 currencies of "USD"
