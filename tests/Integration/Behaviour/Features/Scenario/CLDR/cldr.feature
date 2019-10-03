# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cldr
@reset-database-before-feature
Feature: CLDR display for prices

  # It's important to prepare all the currencies status before we display any price, because LocaleRepository
  # caches the locale which contains the price specifications of all currencies available at the time they were created.
  Background:
    Given shop "shop1" with name "test_shop" exists
    Given language "language1" with locale "en-US" exists
    Given language "language2" with locale "fr-FR" exists
    Given currency "currency1" with isoCode "USD" exists
    Given currency "currency2" with isoCode "EUR" exists
    Given currency "currency3" with isoCode "AUD" exists
    And I delete currency "currency2"
    And I disable currency "currency3"

  Scenario: Display USD
    Then display a price of 14789.5426 "USD" with locale "en-US" should look like "$14,789.54"
    Then display a price of 14789.5426 "USD" with locale "fr-FR" should look like "14 789,54 $"

  Scenario: Display a deleted EUR currency
    And there should be 1 currencies of "EUR"
    And currency with "EUR" has been deleted
    Then display a price of 14789.5426 "EUR" with locale "en-US" should look like "€14,789.54"
    And display a price of 14789.5426 "EUR" with locale "fr-FR" should look like "14 789,54 €"
    # Check that the CLDR doesn't add the currency in database
    And there should be 1 currencies of "EUR"

  Scenario: Display a disabled currency
    And there should be 1 currencies of "AUD"
    And currency with "AUD" has been deactivated
    # We use narrow symbols that's why australian dollar is displayed with $ and not A$
    Then display a price of 14789.5426 "AUD" with locale "en-US" should look like "$14,789.54"
    And display a price of 14789.5426 "AUD" with locale "fr-FR" should look like "14 789,54 $"
    # Check that the CLDR doesn't add the currency in database
    And there should be 1 currencies of "AUD"

