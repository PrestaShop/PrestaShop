# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer --tags customer-required-fields
@reset-database-before-feature
@customer-required-fields
Feature: Customer Required fields management
  PrestaShop allows BO users to manage required fields for FO customer profile
  As a BO user
  I must be able to configure required customer fields

  Scenario: Configure required fields for customer
    Given "Partner offers" is "not required"
    And I specify "Partner offers" to be "required"
    When I save required fields for customer
    Then "Partner offers" should be "required"
