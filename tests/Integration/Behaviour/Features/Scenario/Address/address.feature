# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s address
@reset-database-before-feature
Feature: Address
  PrestaShop allows BO users to manage addresses
  As a BO user
  I should be able to customize addresses

  Background:
    #  from the user point of view manufacturer is brand
    Given I add new manufacturer "testBrand" with following properties:
      | name             | testBrand                          |
      | short_description| Makes best shoes in Europe         |
      | description      | Lorem ipsum dolor sit amets ornare |
      | meta_title       | Perfect quality shoes              |
      | meta_description |                                    |
      | meta_keywords    | Boots, shoes, slippers             |
      | enabled          | true                               |

  Scenario: add brand address
    When I add new brand address "testBrandAddress" with following details:
      | Brand            | testBrand                          |
      | Last name        | testLastName                       |
      | First name       | testFirstName                      |
      | Address          | test street 123                    |
      | City             | Kaunas                             |
      | Country          | Lithuania                          |
    Then brand address "testBrandAddress" should have following details:
      | Brand            | testBrand                          |
      | Last name        | testLastName                       |
      | First name       | testFirstName                      |
      | Address          | test street 123                    |
      | City             | Kaunas                             |
      | Country          | Lithuania                          |

  Scenario: add customer address
    Given I create a customer "Tadas" with following properties:
      | firstName        | Tadas                              |
      | lastName         | Davidsonas                         |
      | email            | tadas.davidsonas@invertus.eu       |
      | password         | secret                             |
    When I add new address to customer "Tadas" with following details:
      | Address alias    | tadas-address                      |
      | First name       | Tadas                              |
      | Last name        | Davidsonas                         |
      | Address          | Work address st. 1234567890        |
      | City             | Kaunas                             |
      | Country          | Lithuania                          |
      | Postal code      | 12345                              |
    Then customer "Tadas" should have address "tadas-address" with following details:
      | Address alias    | tadas-address                      |
      | First name       | Tadas                              |
      | Last name        | Davidsonas                         |
      | Address          | Work address st. 1234567890        |
      | City             | Kaunas                             |
      | Country          | Lithuania                          |
      | Postal code      | 12345                              |
