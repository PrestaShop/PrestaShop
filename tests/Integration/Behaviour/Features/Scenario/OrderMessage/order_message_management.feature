# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order_message
@reset-database-before-feature
Feature: Order message Management
  In order to have prepared reply messages about order
  As a BO user
  I must be able to configure order messages in advance

  Scenario: Add Order message
    When I specify "name" "Out of stock" in default language for order message "OM1"
    And I specify "message" "Products are out of stock" in default language for order message "OM1"
    When I add order message "OM1" with specified properties
    Then order message "OM1" "name" in default language should be "Out of stock"
    And order message "OM1" "message" in default language should be "Products are out of stock"
