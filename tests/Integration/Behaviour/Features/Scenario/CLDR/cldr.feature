# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cldr
@restore-all-tables-before-feature
@clear-cache-after-feature
Feature: CLDR display for prices

  # It's important to prepare all the currencies status before we display any price, because LocaleRepository
  # caches the locale which contains the price specifications of all currencies available at the time they were created.
  Background:
    Given shop "shop1" with name "test_shop" exists
      And language "language1" with locale "en-US" exists
      And language "language2" with locale "fr-FR" exists
      And language "language3" with locale "hi-IN" exists
      And language "language4" with locale "sg-CF" exists
      And language "language5" with locale "nl-NL" exists
      And language "language6" with locale "he-IL" exists
      And language "language7" with locale "ar-EG" exists
      And currency "currency1" with ISO code "USD" exists
      And currency "currency2" with ISO code "EUR" exists
      And currency "currency3" with ISO code "AUD" exists
      And currency "currency4" with unofficial ISO code "ZZZ" exists
      And currency "currency5" with unofficial ISO code "YYY" exists
      And currency "currency6" with ISO code "GBP" exists
    # We customize a deleted currency because they were previously ignored and only reference data was used
    When I set the pattern "¤#,##0.00" for currency "currency1" in locale "fr-FR"
      And I set the pattern "#,##0.00 ¤" for currency "currency2" in locale "en-US"
      And I set the pattern "#,##,##0.00¤" for currency "currency3" in locale "hi-IN"
      And I set the pattern "¤ #,##0.00" for currency "currency5" in locale "fr-FR"
      And I set the pattern "¤ #,##0.00" for currency "currency6" in locale "fr-FR"
      And I set the pattern "¤ #,##0.00;¤ -#,##0.00" for currency "currency3" in locale "fr-FR"
      And I set the pattern "¤#,##0.00;¤#,##0.00-" for currency "currency5" in locale "nl-NL"
      And I set the pattern "‏#,##0.00¤;‏-#,##0.00¤" for currency "currency2" in locale "he-IL"
      And I delete currency "currency2"
      And I disable currency "currency3"
      And I delete currency "currency5"

  # IMPORTANT NOTE: most spaces here are non-breaking spaces
  Scenario: Display USD
    Then a price of 14789.5426 using "USD" in locale "en-US" should look like "$14,789.54"
      And a price of 1444789.5426 using "USD" in locale "hi-IN" should look like "$14,44,789.54"
      And a price of 14789.5426 using "USD" in locale "sg-CF" should look like "$14.789,54"
      And a price of 14789.5426 using "USD" in locale "nl-NL" should look like "$ 14.789,54"

  Scenario: Display a deleted EUR currency
    Given database contains 1 rows of currency "EUR"
      And currency with "EUR" has been deleted
    Then a price of 1444789.5426 using "EUR" in locale "hi-IN" should look like "€14,44,789.54"
      And a price of 14789.5426 using "EUR" in locale "fr-FR" should look like "14 789,54 €"
      And a price of 14789.5426 using "EUR" in locale "sg-CF" should look like "€14.789,54"
      And a price of 14789.5426 using "EUR" in locale "nl-NL" should look like "€ 14.789,54"
      # Check that the CLDR doesn't add the currency in database
      And database contains 1 rows of currency "EUR"

  Scenario: Display a disabled currency
    Given database contains 1 rows of currency "AUD"
      And currency with "AUD" has been deactivated
    # We use narrow symbols that's why australian dollar is displayed with $ and not A$
    Then a price of 14789.5426 using "AUD" in locale "en-US" should look like "$14,789.54"
      And a price of 14789.5426 using "AUD" in locale "sg-CF" should look like "$14.789,54"
      And a price of 14789.5426 using "AUD" in locale "nl-NL" should look like "$ 14.789,54"
      # Check that the CLDR doesn't add the currency in database
      And database contains 1 rows of currency "AUD"

  Scenario: Display an unofficial currency
    Given database contains 1 rows of currency "ZZZ"
    Then a price of 14789.5426 using "ZZZ" in locale "en-US" should look like "ZZZ14,789.54"
      And a price of 14789.5426 using "ZZZ" in locale "fr-FR" should look like "14 789,54 ZZZ"
      And a price of 14789.5426 using "ZZZ" in locale "sg-CF" should look like "ZZZ14.789,54"
      And a price of 14789.5426 using "ZZZ" in locale "nl-NL" should look like "ZZZ 14.789,54"
      # Check that the CLDR doesn't add the currency in database
      And database contains 1 rows of currency "ZZZ"

  Scenario: Display a deleted unofficial currency
    Given database contains 1 rows of currency "YYY"
    And currency with "YYY" has been deleted
    Then a price of 14789.5426 using "YYY" in locale "en-US" should look like "YYY14,789.54"
      And a price of 14789.5426 using "YYY" in locale "fr-FR" should look like "YYY 14 789,54"
      And a price of 14789.5426 using "YYY" in locale "sg-CF" should look like "YYY14.789,54"
      # Check that the CLDR doesn't add the currency in database
      And database contains 1 rows of currency "YYY"

  Scenario: Display customized currencies
    Then a price of 14789.5426 using "USD" in locale "fr-FR" should look like "$14 789,54"
    Then a price of 14789.5426 using "EUR" in locale "en-US" should look like "14,789.54 €"
    Then a price of 1444789.5426 using "AUD" in locale "hi-IN" should look like "14,44,789.54$"
    Then a price of 14789.5426 using "GBP" in locale "fr-FR" should look like "£ 14 789,54"
    Then a price of 14789.5426 using "AUD" in locale "fr-FR" should look like "$ 14 789,54"
    Then a price of 14789.5426 using "YYY" in locale "nl-NL" should look like "YYY14.789,54"

  Scenario: Display negative prices
    Then a price of "-14789.5426" using "USD" in locale "fr-FR" should look like "-$14 789,54"
    Then a price of "-14789.5426" using "EUR" in locale "en-US" should look like "-14,789.54 €"
    Then a price of "-1444789.5426" using "AUD" in locale "hi-IN" should look like "-14,44,789.54$"
    Then a price of "-14789.5426" using "GBP" in locale "fr-FR" should look like "-£ 14 789,54"
    Then a price of "-14789.5426" using "EUR" in locale "sg-CF" should look like "€-14.789,54"
    Then a price of "-14789.5426" using "USD" in locale "nl-NL" should look like "$ -14.789,54"
    Then a price of "-14789.5426" using "AUD" in locale "fr-FR" should look like "$ -14 789,54"
    Then a price of "-14789.5426" using "YYY" in locale "nl-NL" should look like "YYY14.789,54-"

  Scenario: Display RTL prices
    Then a price of 14789.5426 using "USD" in locale "he-IL" should look like "‏14,789.54 $"
    Then a price of 14789.5426 using "USD" in locale "ar-EG" should look like "14,789.54 $"
    Then a price of 14789.5426 using "EUR" in locale "he-IL" should look like "‏14,789.54€"
    Then a price of 14789.5426 using "EUR" in locale "ar-EG" should look like "14,789.54 €"
    Then a price of 14789.5426 using "YYY" in locale "he-IL" should look like "‏14,789.54 YYY"
    Then a price of 14789.5426 using "YYY" in locale "ar-EG" should look like "14,789.54 YYY"
