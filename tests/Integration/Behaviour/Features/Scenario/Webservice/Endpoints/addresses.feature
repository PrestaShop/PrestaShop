# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s webservice --tags webservice-endpoints-addresses
@restore-all-tables-before-feature
@webservice-endpoints-addresses
Feature: Webservice key management
  PrestaShop allows BO users to manage Webservice keys
  As a BO user
  I must be able to create, edit and delete Webservice keys

  Background:
    Given shop "shop1" with name "test_shop" exists
    And I restore tables "webservice_account,webservice_account_shop,webservice_permission"
    And shop configuration for "PS_WEBSERVICE" is set to 1
    And I add a new webservice key with specified properties:
      | key              | DISABLEDDISABLEDDISABLEDDISABLED |
      | description      | Disabled key                     |
      | is_enabled       | 0                                |
      | shop_association | shop1                            |
    And I add a new webservice key with specified properties:
      | key              | ENABLEDENABLEDENABLEDENABLEDENAB |
      | description      | Enabled key                      |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |

  Scenario: Test if Empty WS Key
    When I use Webservice with key "" to list "addresses"
    Then I should get 1 error
    And I should get an error with code 17 and message "Authentication key is empty"

  Scenario: Test if Bad WS Key
    When I use Webservice with key "ABCDEINVALIDDDDDDDD" to list "addresses"
    Then I should get 1 error
    And I should get an error with code 18 and message "Invalid authentication key format"

  Scenario: Test if Disabled WS Key
    When I use Webservice with key "DISABLEDDISABLEDDISABLEDDISABLED" to list "addresses"
    Then I should get 2 errors
    And I should get an error with code 20 and message "Authentification key is not active"
    And I should get an error with code 21 and message "No permission for this authentication key"

  Scenario: Test if No Permissions WS Key
    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to list "addresses"
    Then I should get 1 error
    And I should get an error with code 21 and message "No permission for this authentication key"

    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to fast view "addresses"
    Then I should get 1 error
    And I should get an error with code 21 and message "No permission for this authentication key"

    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to remove "addresses" with reference "unknown"
    Then I should get 1 error
    And I should get an error with code 21 and message "No permission for this authentication key"

    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to add "addresses" for reference "unknown"
    Then I should get 1 error
    And I should get an error with code 21 and message "No permission for this authentication key"

    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to update "addresses" for reference "unknown"
    Then I should get 1 error
    And I should get an error with code 21 and message "No permission for this authentication key"

  Scenario: Test View Data
    When I edit webservice key "ENABLEDENABLEDENABLEDENABLEDENAB" with specified properties:
      | description      | Enabled key with Permissions        |
      | permission_GET   | addresses                           |
      | permission_POST  | addresses                           |
      | permission_PUT   | addresses                           |
      | permission_DELETE| addresses                           |
    Then webservice key "ENABLEDENABLEDENABLEDENABLEDENAB" should have "GET" permission for "addresses" resources
    And webservice key "ENABLEDENABLEDENABLEDENABLEDENAB" should have "POST" permission for "addresses" resources
    And webservice key "ENABLEDENABLEDENABLEDENABLEDENAB" should have "PUT" permission for "addresses" resources
    And webservice key "ENABLEDENABLEDENABLEDENABLEDENAB" should have "DELETE" permission for "addresses" resources
    And I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to list "addresses"
    Then I should get 6 items of type "address"

  Scenario: Manipulate data
    # I need permission before I can add the address
    Given I edit webservice key "ENABLEDENABLEDENABLEDENABLEDENAB" with specified properties:
      | description      | Enabled key with Permissions        |
      | permission_GET   | addresses                           |
      | permission_POST  | addresses                           |
      | permission_PUT   | addresses                           |
      | permission_DELETE| addresses                           |
    ## Add an address
    And I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to add "addresses" for reference "address_6"
      | key             | value          |
      | id              |                |
      | id_customer     | 0              |
      | id_manufacturer | 0              |
      | id_supplier     | 0              |
      | id_country      | 8              |
      | id_state        |                |
      | alias           | dadadada       |
      | company         | dadadada       |
      | lastname        | dadadada       |
      | firstname       | dadadada       |
      | vat_number      |                |
      | address1        | 767 dadadada   |
      | address2        |                |
      | postcode        | 50320          |
      | city            | La Haye-Pesnel |
      | other           |                |
      | phone           | (212) 336-1440 |
      | phone_mobile    |                |
      | dni             |                |
      | deleted         | 0              |
    ## Check if it is added
    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to list "addresses"
    Then I should get 7 items of type "address"
    ## Check if data are updated
    And using Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to view "addresses" for reference "address_6", I should get following properties:
      | key             | value          |
      | id_customer     | 0              |
      | id_manufacturer | 0              |
      | id_supplier     | 0              |
      | id_country      | 8              |
      | id_state        | 0              |
      | alias           | dadadada       |
      | company         | dadadada       |
      | lastname        | dadadada       |
      | firstname       | dadadada       |
      | vat_number      |                |
      | address1        | 767 dadadada   |
      | address2        |                |
      | postcode        | 50320          |
      | city            | La Haye-Pesnel |
      | other           |                |
      | phone           | (212) 336-1440 |
      | phone_mobile    |                |
      | dni             |                |
      | deleted         | 0              |
    ## Update the address
    And I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to update "addresses" for reference "address_6"
      | key             | value          |
      | id_customer     | 0              |
      | id_manufacturer | 0              |
      | id_supplier     | 0              |
      | id_country      | 8              |
      | id_state        |                |
      | alias           | alias          |
      | company         | company        |
      | lastname        | lastname       |
      | firstname       | firstname      |
      | vat_number      | ABCDEF         |
      | address1        | 767 dadadada   |
      | address2        |                |
      | postcode        | 35000          |
      | city            | Rennes         |
      | other           |                |
      | phone           |                |
      | phone_mobile    | 0600000000     |
      | dni             |                |
      | deleted         | 0              |
    ## Check if data are updated
    And using Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to view "addresses" for reference "address_6", I should get following properties:
      | key             | value          |
      | id_customer     | 0              |
      | id_manufacturer | 0              |
      | id_supplier     | 0              |
      | id_country      | 8              |
      | id_state        | 0              |
      | alias           | alias          |
      | company         | company        |
      | lastname        | lastname       |
      | firstname       | firstname      |
      | vat_number      | ABCDEF         |
      | address1        | 767 dadadada   |
      | address2        |                |
      | postcode        | 35000          |
      | city            | Rennes         |
      | other           |                |
      | phone           |                |
      | phone_mobile    | 0600000000     |
      | dni             |                |
      | deleted         | 0              |
    ## Delete an address
    And I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to remove "addresses" with reference "address_6"
    ## Check if it is deleted
    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to list "addresses"
    Then I should get 6 items of type "address"
