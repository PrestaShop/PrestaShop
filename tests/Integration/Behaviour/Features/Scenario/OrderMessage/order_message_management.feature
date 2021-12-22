# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order_message
@restore-all-tables-before-feature
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

  Scenario: Check duplicate order message
    When I specify "name" "My Order Message Name" in default language for order message "OM2"
    And I specify "message" "My Order Message Message" in default language for order message "OM2"
    And I add order message "OM2" with specified properties
    When I specify "name" "My Order Message Name" in default language for order message "OM3"
    And I specify "message" "My Order Message Message" in default language for order message "OM3"
    And I add order message "OM3" with specified properties
    Then I should get error that an order message with this name already exists

  Scenario: Check duplicate order message
    When I specify "name" "My Order Message Name 1" in default language for order message "OM2"
    And I specify "message" "My Order Message Message 1" in default language for order message "OM2"
    And I add order message "OM2" with specified properties
    When I specify "name" "My Order Message Name 2" in default language for order message "OM3"
    And I specify "message" "My Order Message Message 2" in default language for order message "OM3"
    And I add order message "OM3" with specified properties
    When I specify "name" "My Order Message Name 3" in default language for order message "OM3"
    And I edit order message "OM3" with specified properties
    When I specify "name" "My Order Message Name 1" in default language for order message "OM3"
    And I edit order message "OM3" with specified properties
    Then I should get error that an order message with this name already exists

