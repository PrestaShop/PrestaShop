#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s store
@restore-all-tables-before-feature
Feature: Store
  In order to be able to manage shops in Shop Parameters > Contact > Shops
  As a BO user
  I should be able to add a store and toggle its status

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "language1" with locale "fr-FR" exists
    And language with iso code "en" is the default one
    And single shop context is loaded

  Scenario: Add new store
    When I add new store "store1" with the following details:
      | country         | France                        |
      | postcode        | 75009                         |
      | city            | Paris                         |
      | latitude        | 48.88061405611175             |
      | longitude       | 2.3279244006658972            |
      | phone           | +33 (0)1 40 18 30 04          |
      | fax             | +33 (0)1 40 18 30 04          |
      | email           | contact@prestashop.com        |
      | shops           | [shop1]                       |
      | name[en-US]     | PrestaShop                    |
      | address1[en-US] | 4 Jules Lefebvre St.          |
      | address1[fr-FR] | 4 Rue Jules Lefebvre          |
      | address2[en-US] | Paris 75009, France           |
      | address2[fr-FR] | 75009 Paris                   |
      | hours[en-US]    | 09:00AM - 06:30PM             |
      | hours[fr-FR]    | 9h à 18h30                    |
      | note[en-US]     | Best store of course.         |
      | note[fr-FR]     | Le meilleur magasin bien sûr. |
    Then store "store1" should have the following details:
      | active          | true                          |
      | country         | France                        |
      | postcode        | 75009                         |
      | city            | Paris                         |
      | latitude        | 48.88061405611175             |
      | longitude       | 2.3279244006658972            |
      | phone           | +33 (0)1 40 18 30 04          |
      | fax             | +33 (0)1 40 18 30 04          |
      | email           | contact@prestashop.com        |
      | shops           | [shop1]                       |
      | name[en-US]     | PrestaShop                    |
      | name[fr-FR]     |                               |
      | address1[en-US] | 4 Jules Lefebvre St.          |
      | address1[fr-FR] | 4 Rue Jules Lefebvre          |
      | address2[en-US] | Paris 75009, France           |
      | address2[fr-FR] | 75009 Paris                   |
      | hours[en-US]    | 09:00AM - 06:30PM"            |
      | hours[fr-FR]    | 9h à 18h30                    |
      | note[en-US]     | Best store of course.         |
      | note[fr-FR]     | Le meilleur magasin bien sûr. |


  Scenario: Toggle existing store
    Given the store "store1" should have status enabled
    When I toggle "store1"
    Then the store "store1" should have status disabled
    When I toggle "store1"
    Then the store "store1" should have status enabled
