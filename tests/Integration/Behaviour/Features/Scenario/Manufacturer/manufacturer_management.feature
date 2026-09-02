#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s manufacturer
@restore-all-tables-before-feature
@restore-all-tables-after-feature
Feature: Manufacturer management
  As an employee
  I must be able to add, edit and delete manufacturers

  Background:
    Given language "english" with locale "en-US" exists
    And language "french" with locale "fr-FR" exists

  Scenario: Adding new manufacturer
    When I add new manufacturer "shoeman" with following properties:
      | name                     | Best shoes                                                                                                                                              |
      | short_description[en-US] | Makes best shoes in Europe                                                                                                                              |
      | short_description[fr-FR] | Fabrique les meilleures chaussures en Europe                                                                                                            |
      | description[en-US]       | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare.    |
      | description[fr-FR]       | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare. FR |
      | meta_title[en-US]        | Perfect quality shoes                                                                                                                                   |
      | meta_title[fr-FR]        | Chaussures de qualité parfaite                                                                                                                          |
      | meta_description[en-US]  | Great shoes crafted by artisans                                                                                                                         |
      | meta_description[fr-FR]  | Supers chaussures créées par des artisans                                                                                                               |
      | enabled                  | true                                                                                                                                                    |
    Then manufacturer "shoeman" should have the following properties:
      | name                     | Best shoes                                                                                                                                              |
      | short_description[en-US] | Makes best shoes in Europe                                                                                                                              |
      | short_description[fr-FR] | Fabrique les meilleures chaussures en Europe                                                                                                            |
      | description[en-US]       | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare.    |
      | description[fr-FR]       | Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare. FR |
      | meta_title[en-US]        | Perfect quality shoes                                                                                                                                   |
      | meta_title[fr-FR]        | Chaussures de qualité parfaite                                                                                                                          |
      | meta_description[en-US]  | Great shoes crafted by artisans                                                                                                                         |
      | meta_description[fr-FR]  | Supers chaussures créées par des artisans                                                                                                               |
      | enabled                  | true                                                                                                                                                    |

  Scenario: Editing manufacturer
    When I edit manufacturer "shoeman" with following properties:
      | name                     | Worst shoes                              |
      | short_description[en-US] | Worst slippers in EU                     |
      | short_description[fr-FR] | Pires tongs d'europe                     |
      | description[en-US]       | Quick and dirty description              |
      | description[fr-FR]       | Description vite euf                     |
      | meta_title[en-US]        | Worst quality shoes                      |
      | meta_title[fr-FR]        | Chaussures de qualité pourrave           |
      | meta_description[en-US]  | You'd better walk bare foot              |
      | meta_description[fr-FR]  | Je les porterais même pas avec tes pieds |
      | enabled                  | false                                    |
    Then manufacturer "shoeman" should have the following properties:
      | name                     | Worst shoes                              |
      | short_description[en-US] | Worst slippers in EU                     |
      | short_description[fr-FR] | Pires tongs d'europe                     |
      | description[en-US]       | Quick and dirty description              |
      | description[fr-FR]       | Description vite euf                     |
      | meta_title[en-US]        | Worst quality shoes                      |
      | meta_title[fr-FR]        | Chaussures de qualité pourrave           |
      | meta_description[en-US]  | You'd better walk bare foot              |
      | meta_description[fr-FR]  | Je les porterais même pas avec tes pieds |
      | enabled                  | false                                    |

  Scenario: Delete manufacturer logo image
    Given I edit manufacturer "shoeman" with following properties:
      | name              | worst-shoes                 |
      | short_description | Worst slippers in EU        |
      | meta_title        | Worst quality shoes         |
      | description       |                             |
      | meta_description  | You'd better walk bare foot |
      | enabled           | false                       |
      | logo image        | logo.jpg                    |
    And the manufacturer "shoeman" has a logo image
    When I delete the manufacturer "shoeman" logo image
    Then the manufacturer "shoeman" does not have a logo image

  Scenario: Enable and disable manufacturer status
    Given manufacturer "shoeman" is disabled
    When I enable manufacturer "shoeman"
    Then manufacturer "shoeman" should be enabled
    When I disable manufacturer "shoeman"
    Then manufacturer "shoeman" should be disabled

  Scenario: Enabling and disabling multiple manufacturers in bulk action
    When I add new manufacturer "baller" with following properties:
      | name                     | Baller                                                                                                                   |
      | short_description[en-US] | Makes big balls                                                                                                          |
      | description[en-US]       | consectetur adipiscing elit. Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare. |
      | meta_title[en-US]        | Have some balls                                                                                                          |
      | enabled                  | false                                                                                                                    |
    And I add new manufacturer "rocket" with following properties:
      | name                     | Rocket                                                                                      |
      | short_description[en-US] | Cigarettes manufacturer                                                                     |
      | description[en-US]       | Morbi at nulla id mi gravida blandit a non erat. Mauris nec lorem vel odio sagittis ornare. |
      | meta_title[en-US]        | You smoke - you die!                                                                        |
      | meta_description[en-US]  | The sun is shining and the weather is sweet                                                 |
      | enabled                  | true                                                                                        |
    When I enable multiple manufacturers: "baller, rocket" using bulk action
    Then manufacturers: "baller, rocket" should be enabled
    When I disable multiple manufacturers: "baller, rocket" using bulk action
    Then manufacturers: "baller, rocket" should be disabled

  Scenario: Deleting manufacturer right after changing its name
    When I edit manufacturer "shoeman" with following properties:
      | name | Shoeman |
    Then manufacturer "shoeman" should have the following properties:
      | name | Shoeman |
    When I delete manufacturer "shoeman"
    Then manufacturer "shoeman" should be deleted

  Scenario: Deleting multiple manufacturers in bulk action
    When I delete manufacturers: "baller, rocket" using bulk action
    Then manufacturers: "baller, rocket" should be deleted

  Scenario: Get manufacturer for viewing
    When I add new manufacturer "shoeman" with following properties:
      | name                     | testBrand                          |
      | short_description[en-US] | Makes best shoes in Europe         |
      | description[en-US]       | Lorem ipsum dolor sit amets ornare |
      | meta_title[en-US]        | Perfect quality shoes              |
      | meta_description[en-US]  |                                    |
      | enabled                  | true                               |
    And I add new brand address "testBrandAddress" with following details:
      | Brand      | testBrand       |
      | Last name  | testLastName    |
      | First name | testFirstName   |
      | Address    | test street 123 |
      | City       | Kaunas          |
      | Country    | Lithuania       |
    And I add new brand address "testBrandAddress2" with following details:
      | Brand      | testBrand         |
      | Last name  | testLastName      |
      | First name | testFirstName     |
      | Address    | test street 12345 |
      | City       | Vilnius           |
      | Country    | Lithuania         |
    Then manufacturer "shoeman" should have 2 addresses and 0 products
