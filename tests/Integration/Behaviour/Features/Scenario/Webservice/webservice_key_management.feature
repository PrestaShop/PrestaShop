# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s webservice
@reset-database-before-feature
Feature: Webservice key management
  PrestaShop allows BO users to manage Webservice keys
  As a BO user
  I must be able to create, edit and delete Webservice keys

  Background:
    Given shop "shop1" with name "test_shop" exists

  Scenario: Create Webservice key
    Given I specify following properties for new webservice key "key1":
      | key              | DFS51LTKBBMBGF5QQRG523JMQYEHU4X7 |
      | description      | Testing webservice key           |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |
    And I specify "View" permission for "addresses, carriers, carts" resources for new webservice key "key1"
    And I specify "Add" permission for "orders, products, groups" resources for new webservice key "key1"
    And I specify "Modify" permission for "employees, customers, manufacturers" resources for new webservice key "key1"
    And I specify "Delete" permission for "suppliers, languages, countries" resources for new webservice key "key1"
    And I specify "Fast view" permission for "taxes, zones" resources for new webservice key "key1"
    When I add webservice key "key1" with specified properties
    Then webservice key "key1" key should be "DFS51LTKBBMBGF5QQRG523JMQYEHU4X7"
    And webservice key "key1" description should be "Testing webservice key"
    And webservice key "key1" should be enabled
    And webservice key "key1" should have "View" permission for "addresses, carriers, carts" resources
    And webservice key "key1" should have "Add" permission for "orders, products, groups" resources
    And webservice key "key1" should have "Modify" permission for "employees, customers, manufacturers" resources
    And webservice key "key1" should have "Delete" permission for "suppliers, languages, countries" resources
    And webservice key "key1" should have "Fast view" permission for "taxes, zones" resources

  Scenario: Creating Webservice key with duplicate key should not be allowed
    Given I specify following properties for new webservice key "key1":
      | key              | DFS51LTKBBMBGF5QQRG523JMQYEHU4X7 |
      | description      | Testing webservice key           |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |
    And I specify "View" permission for "addresses, carriers, carts" resources for new webservice key "key1"
    And I add webservice key "key1" with specified properties
    Given I specify following properties for new webservice key "key2":
      | key              | DFS51LTKBBMBGF5QQRG523JMQYEHU4X7 |
      | description      | Testing webservice key           |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |
    And I specify "View" permission for "addresses, carriers, carts" resources for new webservice key "key2"
    When I add webservice key "key2" with specified properties
    Then I should get error that webservice key is duplicate

  Scenario: Editing Webservice Key
    When I edit webservice key "key1" with specified properties:
      | key              | ABCD1EFGHIJKLM2PQRS345TUVWXYZ678 |
      | description      | My testing WS key                |
      | is_enabled       | 0                                |
    Then webservice key "key1" key should be "ABCD1EFGHIJKLM2PQRS345TUVWXYZ678"
    And webservice key "key1" description should be "My testing WS key"
    And webservice key "key1" should be disabled
