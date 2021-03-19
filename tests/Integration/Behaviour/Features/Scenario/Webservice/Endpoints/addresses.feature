# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s webservice --tags webservice-endpoints-addresses
@reset-database-before-feature
@webservice-endpoints-addresses
Feature: Webservice key management
  PrestaShop allows BO users to manage Webservice keys
  As a BO user
  I must be able to create, edit and delete Webservice keys

  Background:
    Given shop "shop1" with name "test_shop" exists
    And shop configuration for "PS_WEBSERVICE" is set to 1
    And I specify following properties for new webservice key "key1":
      | key              | AZERTYUIOPQSDFGHJKLMWXCVBN123456 |
      | description      | Disabled key                     |
      | is_enabled       | 0                                |
      | shop_association | shop1                            |
    And I add webservice key "key1" with specified properties
    And I specify following properties for new webservice key "key2":
      | key              | ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEF |
      | description      | Enabled key                      |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |
    And I add webservice key "key2" with specified properties

  Scenario: Test if Empty WS Key
    When I use the Webservice Key ""
    And I request the Webservice with the method GET on the endpoint "addresses"
    Then I should get a number of 1 error
    And I should get an error with code 17 and message "Authentication key is empty"

  Scenario: Test if Bad WS Key
    When I use the Webservice Key "ABCDE"
    And I request the Webservice with the method GET on the endpoint "addresses"
    Then I should get a number of 1 error
    And I should get an error with code 18 and message "Invalid authentication key format"

  Scenario: Test if Disabled WS Key
    When I use the Webservice Key "AZERTYUIOPQSDFGHJKLMWXCVBN123456"
    And I request the Webservice with the method GET on the endpoint "addresses"
    Then I should get a number of 2 errors
    And I should get an error with code 20 and message "Authentification key is not active"
    And I should get an error with code 21 and message "No permission for this authentication key"

  Scenario: Test if No Permissions WS Key
    When I use the Webservice Key "ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEF"
    And I request the Webservice with the method GET on the endpoint "addresses"
    Then I should get a number of 1 error
    And I should get an error with code 21 and message "No permission for this authentication key"

    When I use the Webservice Key "ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEF"
    And I request the Webservice with the method HEAD on the endpoint "addresses"
    Then I should get a number of 1 error
    And I should get an error with code 21 and message "No permission for this authentication key"

    When I use the Webservice Key "ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEF"
    And I request the Webservice with the method DELETE on the endpoint "addresses"
    Then I should get a number of 1 error
    And I should get an error with code 21 and message "No permission for this authentication key"

    When I use the Webservice Key "ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEF"
    And I request the Webservice with the method POST on the endpoint "addresses"
    Then I should get a number of 1 error
    And I should get an error with code 21 and message "No permission for this authentication key"

    When I use the Webservice Key "ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEF"
    And I request the Webservice with the method PUT on the endpoint "addresses"
    Then I should get a number of 1 error
    And I should get an error with code 21 and message "No permission for this authentication key"

  Scenario: Test View Data
    Given I specify "View" permission for "addresses" resources for new webservice key "key2"
    And I specify "Add" permission for "addresses" resources for new webservice key "key2"
    And I specify "Modify" permission for "addresses" resources for new webservice key "key2"
    And I specify "Delete" permission for "addresses" resources for new webservice key "key2"
    And I edit webservice key "key2" with specified properties:
      | description      | Enabled key with Permissions        |
    Then webservice key "key2" should have "View" permission for "addresses" resources
    And webservice key "key2" should have "Add" permission for "addresses" resources
    And webservice key "key2" should have "Modify" permission for "addresses" resources
    And webservice key "key2" should have "Delete" permission for "addresses" resources
    When I use the Webservice Key "ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEF"
    And I request the Webservice with the method GET on the endpoint "addresses"
    Then I should get a number of 6 items of type "address"

  Scenario: Manipulate data
    When I use the Webservice Key "ABCDEFGHIJKLMNOPQRSTUVWXYZABCDEF"
    ## Add an address
    And I request the Webservice with the method POST on the endpoint "addresses"
      | key             | value          |
      | id              |                |
      | id_customer     | 0              |
      | id_manufacturer | 0              |
      | id_supplier     | 0              |
      | id_warehouse    | 0              |
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
    When I request the Webservice with the method GET on the endpoint "addresses"
    Then I should get a number of 7 items of type "address"
    ## Check if data are updated
    And I request the Webservice with the method GET on the endpoint "addresses/7"
    And I check the last webservice request has these values:
      | key             | value          |
      | id_customer     | 0              |
      | id_manufacturer | 0              |
      | id_supplier     | 0              |
      | id_warehouse    | 0              |
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
    And I request the Webservice with the method PUT on the endpoint "addresses"
      | key             | value          |
      | id              | 7              |
      | id_customer     | 0              |
      | id_manufacturer | 0              |
      | id_supplier     | 0              |
      | id_warehouse    | 0              |
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
    And I request the Webservice with the method GET on the endpoint "addresses/7"
    And I check the last webservice request has these values:
      | key             | value          |
      | id              | 7              |
      | id_customer     | 0              |
      | id_manufacturer | 0              |
      | id_supplier     | 0              |
      | id_warehouse    | 0              |
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
    And I request the Webservice with the method DELETE on the endpoint "addresses/7"
    ## Check if it is deleted
    When I request the Webservice with the method GET on the endpoint "addresses"
    Then I should get a number of 6 items of type "address"
