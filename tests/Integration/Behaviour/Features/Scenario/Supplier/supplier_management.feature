#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s supplier
@restore-all-tables-before-feature
Feature: Supplier management
  As an employee
  I must be able to add, edit and delete suppliers from Back Office

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "language1" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And single shop context is loaded

  Scenario: Adding new supplier
    When I add new supplier supplier1 with the following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | description[fr-FR]      | la supplier :D     |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | shops                   | [shop1]            |
    Then supplier supplier1 should have following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | description[fr-FR]      | la supplier :D     |
      | meta title[en-US]       | my supplier nr one |
      | meta title[fr-FR]       |                    |
      | meta description[en-US] |                    |
      | meta description[fr-FR] |                    |
      | shops                   | [shop1]            |
#@todo: finish up create with optional params too, different cases + update and delete scenarios.

  Scenario: Delete manufacturer logo image
    Given I edit supplier "supplier1" with the following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | description[fr-FR]      | la supplier :D     |
      | meta title[en-US]       | my supplier nr one |
      | meta title[fr-FR]       |                    |
      | meta description[en-US] |                    |
      | meta description[fr-FR] |                    |
      | shops                   | [shop1]            |
      | logo image              | logo.jpg           |
    And the supplier "supplier1" has a logo image
    When I delete the supplier "supplier1" logo image
    Then the supplier "supplier1" does not have a logo image

  Scenario: Viewing supplier
    Given supplier "acc1" with name "Accessories supplier" exists
    Then supplier "acc1" should have 17 products associated
    And supplier "acc1" should have following details for product "Mountain fox notebook":
      | attribute name        | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      | Paper Type - Ruled    | demo_8_85          | $5.49           | demo_8            |       |     | 300      |
      | Paper Type - Plain    | demo_8_86          | $5.49           | demo_8            |       |     | 300      |
      | Paper Type - Squarred | demo_8_87          | $5.49           | demo_8            |       |     | 300      |
      | Paper Type - Doted    | demo_8_88          | $5.49           | demo_8            |       |     | 300      |
    And supplier "acc1" should have following details for product "Mug The best is yet to come":
      | attribute name | supplier reference | wholesale price | product reference | ean13 | upc | quantity |
      |                | demo_11            | $5.49           | demo_11           |       |     | 300      |
