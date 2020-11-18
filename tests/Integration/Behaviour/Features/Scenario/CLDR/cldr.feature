# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cldr
@reset-database-before-feature
Feature: CLDR display for prices

  # It's important to prepare all the currencies status before we display any price, because LocaleRepository
  # caches the locale which contains the price specifications of all currencies available at the time they were created.
  Background:
    Given shop "shop1" with name "test_shop" exists
      And language "language1" with locale "en-US" exists
      And language "language2" with locale "fr-FR" exists
      And currency "currency1" with ISO code "USD" exists
      And currency "currency2" with ISO code "EUR" exists
      And currency "currency3" with ISO code "AUD" exists
    When I delete currency "currency2"
      And I disable currency "currency3"

  Scenario: Display USD
    Then a price of 14789.5426 using "USD" in locale "en-US" should look like "$14,789.54"
      And a price of 14789.5426 using "USD" in locale "fr-FR" should look like "14 789,54 $"

  Scenario: Display a deleted EUR currency
    Given database contains 1 rows of currency "EUR"
      And currency with "EUR" has been deleted
    Then a price of 14789.5426 using "EUR" in locale "en-US" should look like "€14,789.54"
      And a price of 14789.5426 using "EUR" in locale "fr-FR" should look like "14 789,54 €"
      # Check that the CLDR doesn't add the currency in database
      And database contains 1 rows of currency "EUR"

  Scenario: Display a disabled currency
    Given database contains 1 rows of currency "AUD"
      And currency with "AUD" has been deactivated
    # We use narrow symbols that's why australian dollar is displayed with $ and not A$
    Then a price of 14789.5426 using "AUD" in locale "en-US" should look like "$14,789.54"
      And a price of 14789.5426 using "AUD" in locale "fr-FR" should look like "14 789,54 $"
      # Check that the CLDR doesn't add the currency in database
      And database contains 1 rows of currency "AUD"

