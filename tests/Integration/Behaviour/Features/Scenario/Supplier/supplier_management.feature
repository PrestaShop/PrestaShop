#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s supplier
@reset-database-before-feature
Feature: Supplier management
  As an employee
  I must be able to add, edit and delete suppliers from Back Office

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "language1" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And single shop context is loaded

  Scenario: Adding new supplier
    When I add new supplier supplier1 with following properties:
      | name                    | my supplier 1      |
      | address                 | Donelaicio st. 1   |
      | city                    | Kaunas             |
      | country                 | Lithuania          |
      | enabled                 | true               |
      | description[en-US]      | just a supplier    |
      | description[fr-FR]      | la supplier :D     |
      | meta title[en-US]       | my supplier nr one |
      | meta description[en-US] |                    |
      | meta keywords[en-US]    | sup,1              |
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
      | meta keywords[en-US]    | sup,1              |
      | meta keywords[fr-FR]    |                    |
      | shops                   | [shop1]            |
#@todo: finish up create with optional params too, different cases + update and delete scenarios.
