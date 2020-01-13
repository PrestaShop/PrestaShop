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
    Given I create customer "testFirstName" with following details:
      | firstName        | testFirstName                      |
      | lastName         | testLastName                       |
      | email            | test.davidsonas@invertus.eu        |
      | password         | secret                             |
    When I add new address to customer "testFirstName" with following details:
      | Address alias    | test-address                       |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |
    Then customer "testFirstName" should have address "test-address" with following details:
      | Address alias    | test-address                       |
      | First name       | testFirstName                      |
      | Last name        | testLastName                       |
      | Address          | Work address st. 1234567890        |
      | City             | Birmingham                         |
      | Country          | United States                      |
      | State            | Alabama                            |
      | Postal code      | 12345                              |

