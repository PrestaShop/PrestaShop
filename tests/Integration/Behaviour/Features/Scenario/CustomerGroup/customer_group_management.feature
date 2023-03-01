# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_group --tags customer-group-management
@restore-all-tables-before-feature
@customer-group-management
Feature: CustomerGroup Management
  PrestaShop allows BO users to manage customer groups in the Customers > Customers > Groups page
  As a BO user
  I must be able to create customer groups

  Background:
    Given groups feature is activated

  Scenario: Create a simple customer group
    Given language "fr" with locale "fr-FR" exists
    When I create a Customer Group "CustomerGroup1" with the following details:
      | name[en-US]        | Name EN |
      | name[fr-FR]        | Name FR |
      | reduction          | 1.23    |
      | priceDisplayMethod | 1       |
      | showPrice          | true    |
    When I query Customer Group "CustomerGroup1" I should get a Customer Group with properties:
      | reduction          | 1.23 |
      | priceDisplayMethod | 1    |
      | showPrice          | true |
    Then customer group "CustomerGroup1" localized "name" should be:
      | locale | value   |
      | en-US  | Name EN |
    Then customer group "CustomerGroup1" localized "name" should be:
      | locale | value   |
      | fr-FR  | Name FR |
