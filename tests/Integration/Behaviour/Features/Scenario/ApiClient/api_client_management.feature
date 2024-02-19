# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s api-client --tags api-client-management
@restore-api-client-before-feature
@api-client-management
Feature: Api Client Management
  PrestaShop provides an API to manage and export BO data
  As a API user
  I must be able to create, save and edit api client

  Scenario: Create a simple api client and edit it
    When I create an api client "AC-1" with following properties:
      | clientName  | Thomas               |
      | apiClientId | test-id              |
      | enabled     | true                 |
      | description | a simple description |
      | lifetime    | 3600                 |
    Then api client "AC-1" should have the following properties:
      | clientName  | Thomas               |
      | apiClientId | test-id              |
      | enabled     | true                 |
      | description | a simple description |
      | scopes      |                      |
      | lifetime    | 3600                 |
    When I edit api client "AC-1" with the following values:
      | clientName  | Toto                |
      | apiClientId | test-id-toto        |
      | enabled     | false               |
      | description | another description |
      | lifetime    | 3000                |
    Then api client "AC-1" should have the following properties:
      | clientName  | Toto                |
      | apiClientId | test-id-toto        |
      | enabled     | false               |
      | description | another description |
      | scopes      |                     |
      | lifetime    | 3000                |
    When I edit api client "AC-1" with the following values:
    # Just a quick edition to show partial update is possible
      | apiClientId | test-id-toto-2 |
    Then api client "AC-1" should have the following properties:
      | apiClientId | test-id-toto-2 |

  Scenario: Create an api client with non unique properties:
    When I create an api client "AC-2" with following properties:
      | clientName  | Thomas2              |
      | apiClientId | test-id-2            |
      | enabled     | true                 |
      | description | a simple description |
      | lifetime    | 3600                 |
    When I create an api client "AC-3" with following properties:
      | clientName  | Thomas3              |
      | apiClientId | test-id-2            |
      | enabled     | true                 |
      | description | a simple description |
      | lifetime    | 3600                 |
    Then I should get an error that clientId is not unique
    When I create an api client "AC-4" with following properties:
      | clientName  | Thomas2   |
      | apiClientId | test-id-3 |
      | enabled     | true      |
      | description |           |
      | lifetime    | 3600      |
    Then I should get an error that clientName is not unique

  Scenario: Create an api client with invalid properties:
    When I create an api client "AC-1" with following properties:
      | clientName  |                      |
      | apiClientId | test-id-1            |
      | enabled     | true                 |
      | description | a simple description |
      | lifetime    | 3600                 |
    Then I should get an error that clientName is invalid
    When I create an api client "AC-2" with following properties:
      | clientName  | Thomas-1             |
      | apiClientId |                      |
      | enabled     | true                 |
      | description | a simple description |
      | lifetime    | 3600                 |
    Then I should get an error that apiClientId is invalid

  Scenario: Create api client with values over max length:
    When I create an api client "AC-6" with a large value in apiClientId:
      | clientName  | Thomas-6           |
      | apiClientId | valueToBeGenerated |
      | enabled     | true               |
      | description | test description   |
      | lifetime    | 2000               |
    Then I should get an error that apiClientId is too large
    When I create an api client "AC-7" with a large value in clientName:
      | clientName  | valueToBeGenerated |
      | apiClientId | test-client-id     |
      | enabled     | true               |
      | description | test description   |
      | lifetime    | 3600               |
    Then I should get an error that clientName is too large
    When I create an api client "AC-8" with a large value in description:
      | clientName  | Thomas-7           |
      | apiClientId | test-client-id-2   |
      | enabled     | true               |
      | description | valueToBeGenerated |
      | lifetime    | 3600               |
    Then I should get an error that description is too large

  Scenario: Edit api client with values over max length:
    When I create an api client "AC-9" with following properties:
      | clientName  | Thomas-8         |
      | apiClientId | test-client-id-3 |
      | enabled     | true             |
      | description | description      |
      | lifetime    | 3600             |
    When I edit api client "AC-9" with a large value in apiClientId:
    Then I should get an error that apiClientId is too large
    When I create an api client "AC-10" with following properties:
      | clientName  | Thomas-9         |
      | apiClientId | test-client-id-4 |
      | enabled     | true             |
      | description | description      |
      | lifetime    | 3600             |
    When I edit api client "AC-10" with a large value in clientName:
    Then I should get an error that clientName is too large
    When I create an api client "AC-11" with following properties:
      | clientName  | Thomas-10        |
      | apiClientId | test-client-id-5 |
      | enabled     | true             |
      | description | description      |
      | lifetime    | 3600             |
    When I edit api client "AC-11" with a large value in description:
    Then I should get an error that description is too large

  Scenario: Create a simple api client and delete it
    When I create an api client "AC-12" with following properties:
      | clientName  | Jojo       |
      | apiClientId | test-id-jo |
      | enabled     | true       |
      | description |            |
      | lifetime    | 3600       |
    Then api client "AC-12" should have the following properties:
      | clientName  | Jojo       |
      | apiClientId | test-id-jo |
      | enabled     | true       |
      | description |            |
      | lifetime    | 3600       |
    When I delete api client "AC-12"
    Then api client "AC-12" should not exist

  Scenario: Create a simple api client with scopes and edit its scopes
    When I create an api client "AC-13" with following properties:
      | clientName  | Obiwan                  |
      | apiClientId | jedi                    |
      | enabled     | true                    |
      | description | may the force           |
      | scopes      | product_read, hook_read |
      | lifetime    | 3600                    |
    Then api client "AC-13" should have the following properties:
      | clientName  | Obiwan                  |
      | apiClientId | jedi                    |
      | enabled     | true                    |
      | description | may the force           |
      | scopes      | product_read, hook_read |
      | lifetime    | 3600                    |
    When I edit api client "AC-13" with the following values:
      | scopes | product_read, hook_read, hook_write |
    Then api client "AC-13" should have the following properties:
      | clientName  | Obiwan                              |
      | apiClientId | jedi                                |
      | enabled     | true                                |
      | description | may the force                       |
      | scopes      | product_read, hook_read, hook_write |
      | lifetime    | 4000                                |
    When I edit api client "AC-13" with the following values:
      | scopes |  |
    Then api client "AC-13" should have the following properties:
      | clientName  | Obiwan        |
      | apiClientId | jedi          |
      | enabled     | true          |
      | description | may the force |
      | scopes      |               |
      | lifetime    | 4000          |

  Scenario: Create or edit an api client with invalid scopes
    When I create an api client "AC-14" with following properties:
      | clientName  | Palpatine          |
      | apiClientId | sith               |
      | enabled     | true               |
      | description | may the force      |
      | scopes      | unknown_api_client |
      | lifetime    | 3600               |
    Then I should get an error that scopes is invalid
    When I create an api client "AC-14" with following properties:
      | clientName  | Palpatine     |
      | apiClientId | sith          |
      | enabled     | true          |
      | description | may the force |
      | scopes      | product_read  |
      | lifetime    | 3600          |
    Then api client "AC-14" should have the following properties:
      | clientName  | Palpatine     |
      | apiClientId | sith          |
      | enabled     | true          |
      | description | may the force |
      | scopes      | product_read  |
      | lifetime    | 3600          |
    When I edit api client "AC-14" with the following values:
      | clientName | Emperor                          |
      | scopes     | product_read, unknown_api_client |
    Then I should get an error that scopes is invalid
    # Api Client data do not change because of invalid scope values
    And api client "AC-14" should have the following properties:
      | clientName  | Palpatine     |
      | apiClientId | sith          |
      | enabled     | true          |
      | description | may the force |
      | scopes      | product_read  |
      | lifetime    | 3600          |

  Scenario: Create or edit an api client with invalid lifetime
    When I create an api client "AC-15" with following properties:
      | clientName  | Chewie        |
      | apiClientId | wookie        |
      | enabled     | true          |
      | description | Rhrhhrhrhrrhr |
      | scopes      | product_read  |
      | lifetime    | -10           |
    Then I should get an error that lifetime is invalid
    When I create an api client "AC-16" with following properties:
      | clientName  | Chewie        |
      | apiClientId | wookie        |
      | enabled     | true          |
      | description | Rhrhhrhrhrrhr |
      | scopes      | product_read  |
      | lifetime    | 3600          |
    Then api client "AC-16" should have the following properties:
      | clientName  | Chewie        |
      | apiClientId | wookie        |
      | enabled     | true          |
      | description | Rhrhhrhrhrrhr |
      | scopes      | product_read  |
      | lifetime    | 3600          |
    When I edit api client "AC-16" with the following values:
      | lifetime | 0 |
    Then I should get an error that lifetime is invalid

  Scenario: Check secret validity and generate ne secret
    When I create an api client "AC-13" with generated secret "AC-13-secret" using following properties:
      | clientName  | Jojo-2         |
      | apiClientId | test-id-jojo-2 |
      | enabled     | true           |
      | description | description    |
      | lifetime    | 3600           |
    Then api client "AC-13" should have the following properties:
      | clientName  | Jojo-2         |
      | apiClientId | test-id-jojo-2 |
      | enabled     | true           |
      | description | description    |
      | lifetime    | 3600           |
    Then secret "AC-13-secret" is valid for api client "AC-13"
    When I generate new secret "AC-13-new-secret" for api client "AC-13"
    Then secret "AC-13-secret" is invalid for api client "AC-13"
    And secret "AC-13-new-secret" is valid for api client "AC-13"
