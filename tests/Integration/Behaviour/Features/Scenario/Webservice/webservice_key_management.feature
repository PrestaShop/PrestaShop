# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s webservice --tags webservice-key-management
@restore-all-tables-before-feature
@webservice-key-management
Feature: Webservice key management
  PrestaShop allows BO users to manage Webservice keys
  As a BO user
  I must be able to create, edit and delete Webservice keys

  Background:
    Given shop "shop1" with name "test_shop" exists

  Scenario: Create Webservice key
    Given I add a new webservice key with specified properties:
      | key              | DFS51LTKBBMBGF5QQRG523JMQYEHU4X7    |
      | description      | Testing webservice key              |
      | is_enabled       | 1                                   |
      | shop_association | shop1                               |
      | permission_GET   | addresses, carriers, carts          |
      | permission_POST  | orders, products, groups            |
      | permission_PUT   | employees, customers, manufacturers |
      | permission_DELETE| suppliers, languages, countries     |
      | permission_HEAD  | taxes, zones                        |
    And webservice key "DFS51LTKBBMBGF5QQRG523JMQYEHU4X7" key should be "DFS51LTKBBMBGF5QQRG523JMQYEHU4X7"
    And webservice key "DFS51LTKBBMBGF5QQRG523JMQYEHU4X7" description should be "Testing webservice key"
    And webservice key "DFS51LTKBBMBGF5QQRG523JMQYEHU4X7" should be enabled
    And webservice key "DFS51LTKBBMBGF5QQRG523JMQYEHU4X7" should have "GET" permission for "addresses, carriers, carts" resources
    And webservice key "DFS51LTKBBMBGF5QQRG523JMQYEHU4X7" should have "POST" permission for "orders, products, groups" resources
    And webservice key "DFS51LTKBBMBGF5QQRG523JMQYEHU4X7" should have "PUT" permission for "employees, customers, manufacturers" resources
    And webservice key "DFS51LTKBBMBGF5QQRG523JMQYEHU4X7" should have "DELETE" permission for "suppliers, languages, countries" resources
    And webservice key "DFS51LTKBBMBGF5QQRG523JMQYEHU4X7" should have "HEAD" permission for "taxes, zones" resources

  Scenario: Creating Webservice key with duplicate key should not be allowed
    Given I add a new webservice key with specified properties:
      | key              | DFS51LTKBBMBGF5QQRG523JMQYEHU4X7 |
      | description      | Testing webservice key           |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |
      | permission_GET   | addresses, carriers, carts       |
    Then I should get error that webservice key is duplicate

  Scenario: Editing Webservice Key
    When I edit webservice key "DFS51LTKBBMBGF5QQRG523JMQYEHU4X7" with specified properties:
      | key              | ABCD1EFGHIJKLM2PQRS345TUVWXYZ678 |
      | description      | My testing WS key                |
      | is_enabled       | 0                                |
    Then webservice key "ABCD1EFGHIJKLM2PQRS345TUVWXYZ678" key should be "ABCD1EFGHIJKLM2PQRS345TUVWXYZ678"
    And webservice key "ABCD1EFGHIJKLM2PQRS345TUVWXYZ678" description should be "My testing WS key"
    And webservice key "ABCD1EFGHIJKLM2PQRS345TUVWXYZ678" should be disabled
    When I edit webservice key "ABCD1EFGHIJKLM2PQRS345TUVWXYZ678" with specified properties:
      | permission_GET   | taxes                            |
      | permission_POST  | suppliers                        |
      | permission_PUT   | employees                        |
      | permission_DELETE| orders                           |
      | permission_HEAD  | addresses                        |
    And webservice key "ABCD1EFGHIJKLM2PQRS345TUVWXYZ678" should have "GET" permission for "taxes" resources
    And webservice key "ABCD1EFGHIJKLM2PQRS345TUVWXYZ678" should have "POST" permission for "suppliers" resources
    And webservice key "ABCD1EFGHIJKLM2PQRS345TUVWXYZ678" should have "PUT" permission for "employees" resources
    And webservice key "ABCD1EFGHIJKLM2PQRS345TUVWXYZ678" should have "DELETE" permission for "orders" resources
    And webservice key "ABCD1EFGHIJKLM2PQRS345TUVWXYZ678" should have "HEAD" permission for "addresses" resources
