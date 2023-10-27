# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s api-access --tags api-access-management
@restore-api-access-before-feature
@api-access-management
Feature: Api Access Management
  PrestaShop provides an API to manage and export BO data
  As a API user
  I must be able to create, save and edit api access

  Scenario: Create a simple api access and edit it
    When I create an api access "AA-1" with following properties:
      | clientName  | Thomas               |
      | apiClientId | test-id              |
      | enabled     | true                 |
      | description | a simple description |
    Then api access "AA-1" should have the following properties:
      | clientName  | Thomas               |
      | apiClientId | test-id              |
      | enabled     | true                 |
      | description | a simple description |
      | scopes      |                      |
    When I edit api access "AA-1" with the following values:
      | clientName  | Toto                |
      | apiClientId | test-id-toto        |
      | enabled     | false               |
      | description | another description |
    Then api access "AA-1" should have the following properties:
      | clientName  | Toto                |
      | apiClientId | test-id-toto        |
      | enabled     | false               |
      | description | another description |
      | scopes      |                     |
    When I edit api access "AA-1" with the following values:
    # Just a quick edition to show partial update is possible
      | apiClientId | test-id-toto-2 |
    Then api access "AA-1" should have the following properties:
      | apiClientId | test-id-toto-2 |

  Scenario: Create an api access with non unique properties:
    When I create an api access "AA-2" with following properties:
      | clientName  | Thomas2              |
      | apiClientId | test-id-2            |
      | enabled     | true                 |
      | description | a simple description |
    When I create an api access "AA-3" with following properties:
      | clientName  | Thomas3              |
      | apiClientId | test-id-2            |
      | enabled     | true                 |
      | description | a simple description |
    Then I should get an error that clientId is not unique
    When I create an api access "AA-4" with following properties:
      | clientName  | Thomas2   |
      | apiClientId | test-id-3 |
      | enabled     | true      |
      | description |           |
    Then I should get an error that clientName is not unique

  Scenario: Create an api access with invalid properties:
    When I create an api access "AA-1" with following properties:
      | clientName  |                      |
      | apiClientId | test-id-1            |
      | enabled     | true                 |
      | description | a simple description |
    Then I should get an error that clientName is invalid
    When I create an api access "AA-2" with following properties:
      | clientName  | Thomas-1             |
      | apiClientId |                      |
      | enabled     | true                 |
      | description | a simple description |
    Then I should get an error that apiClientId is invalid

  Scenario: Create api access with values over max length:
    When I create an api access "AA-6" with a large value in apiClientId:
      | clientName  | Thomas-6           |
      | apiClientId | valueToBeGenerated |
      | enabled     | true               |
      | description | test description   |
    Then I should get an error that apiClientId is too large
    When I create an api access "AA-7" with a large value in clientName:
      | clientName  | valueToBeGenerated |
      | apiClientId | test-client-id     |
      | enabled     | true               |
      | description | test description   |
    Then I should get an error that clientName is too large
    When I create an api access "AA-8" with a large value in description:
      | clientName  | Thomas-7           |
      | apiClientId | test-client-id-2   |
      | enabled     | true               |
      | description | valueToBeGenerated |
    Then I should get an error that description is too large

  Scenario: Edit api access with values over max length:
    When I create an api access "AA-9" with following properties:
      | clientName  | Thomas-8         |
      | apiClientId | test-client-id-3 |
      | enabled     | true             |
      | description | description      |
    When I edit api access "AA-9" with a large value in apiClientId:
    Then I should get an error that apiClientId is too large
    When I create an api access "AA-10" with following properties:
      | clientName  | Thomas-9         |
      | apiClientId | test-client-id-4 |
      | enabled     | true             |
      | description | description      |
    When I edit api access "AA-10" with a large value in clientName:
    Then I should get an error that clientName is too large
    When I create an api access "AA-11" with following properties:
      | clientName  | Thomas-10        |
      | apiClientId | test-client-id-5 |
      | enabled     | true             |
      | description | description      |
    When I edit api access "AA-11" with a large value in description:
    Then I should get an error that description is too large

  Scenario: Create a simple api access and delete it
    When I create an api access "AA-12" with following properties:
      | clientName  | Jojo       |
      | apiClientId | test-id-jo |
      | enabled     | true       |
      | description |            |
    Then api access "AA-12" should have the following properties:
      | clientName  | Jojo       |
      | apiClientId | test-id-jo |
      | enabled     | true       |
      | description |            |
    When I delete api access "AA-12"
    Then api access "AA-12" should not exist

  Scenario: Create a simple api access with scopes and edit its scopes
    When I create an api access "AA-13" with following properties:
      | clientName  | Obiwan                     |
      | apiClientId | jedi                       |
      | enabled     | true                       |
      | description | may the force              |
      | scopes      | api_access_read, hook_read |
    Then api access "AA-13" should have the following properties:
      | clientName  | Obiwan                     |
      | apiClientId | jedi                       |
      | enabled     | true                       |
      | description | may the force              |
      | scopes      | api_access_read, hook_read |
    When I edit api access "AA-13" with the following values:
      | scopes | api_access_read, hook_read, hook_write |
    Then api access "AA-13" should have the following properties:
      | clientName  | Obiwan                                 |
      | apiClientId | jedi                                   |
      | enabled     | true                                   |
      | description | may the force                          |
      | scopes      | api_access_read, hook_read, hook_write |
    When I edit api access "AA-13" with the following values:
      | scopes |  |
    Then api access "AA-13" should have the following properties:
      | clientName  | Obiwan        |
      | apiClientId | jedi          |
      | enabled     | true          |
      | description | may the force |
      | scopes      |               |

  Scenario: Create or edit an api access with invalid scopes
    When I create an api access "AA-14" with following properties:
      | clientName  | Palpatine          |
      | apiClientId | sith               |
      | enabled     | true               |
      | description | may the force      |
      | scopes      | unknown_api_access |
    Then I should get an error that scopes is invalid
    When I create an api access "AA-14" with following properties:
      | clientName  | Palpatine       |
      | apiClientId | sith            |
      | enabled     | true            |
      | description | may the force   |
      | scopes      | api_access_read |
    Then api access "AA-14" should have the following properties:
      | clientName  | Palpatine       |
      | apiClientId | sith            |
      | enabled     | true            |
      | description | may the force   |
      | scopes      | api_access_read |
    When I edit api access "AA-14" with the following values:
      | clientName | Emperor                             |
      | scopes     | api_access_read, unknown_api_access |
    Then I should get an error that scopes is invalid
    # Api Access data do not change because of invalid scope values
    And api access "AA-14" should have the following properties:
      | clientName  | Palpatine       |
      | apiClientId | sith            |
      | enabled     | true            |
      | description | may the force   |
      | scopes      | api_access_read |

  Scenario: Check secret validity and generate ne secret
    When I create an api access "AA-13" with generated secret "AA-13-secret" using following properties:
      | clientName  | Jojo-2         |
      | apiClientId | test-id-jojo-2 |
      | enabled     | true           |
      | description | description    |
    Then api access "AA-13" should have the following properties:
      | clientName  | Jojo-2         |
      | apiClientId | test-id-jojo-2 |
      | enabled     | true           |
      | description | description    |
    Then secret "AA-13-secret" is valid for api access "AA-13"
    When I generate new secret "AA-13-new-secret" for api access "AA-13"
    Then secret "AA-13-secret" is invalid for api access "AA-13"
    And secret "AA-13-new-secret" is valid for api access "AA-13"
