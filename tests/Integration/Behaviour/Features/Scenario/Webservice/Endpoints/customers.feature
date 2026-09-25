# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s webservice --tags webservice-endpoints-customers
@restore-all-tables-before-feature
@webservice-endpoints-customers
Feature: Webservice customers endpoint
  PrestaShop allows API users to manage customers through the Webservice
  As an API user
  I must be able to update a customer without destroying the fields I am not allowed to write

  Background:
    Given shop "shop1" with name "test_shop" exists
    And I restore tables "webservice_account,webservice_account_shop,webservice_permission"
    And shop configuration for "PS_WEBSERVICE" is set to 1
    And I add a new webservice key with specified properties:
      | key              | CUSTOMERSCUSTOMERSCUSTOMERSCUSTO |
      | description      | Customers key                    |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |
      | permission_GET   | customers                        |
      | permission_POST  | customers                        |
      | permission_PUT   | customers                        |

  Scenario: A non writable field survives an update that omits it
    When I use Webservice with key "CUSTOMERSCUSTOMERSCUSTOMERSCUSTO" to add "customers" for reference "customer1"
      | key       | value                   |
      | firstname | Web                     |
      | lastname  | Service                 |
      | email     | ws.customer@example.com |
      | passwd    | Pr3st4Sh0P              |
    Then using Webservice with key "CUSTOMERSCUSTOMERSCUSTOMERSCUSTO" to view "customers" for reference "customer1", I should get following non empty properties:
      | key        |
      | secure_key |

    # secure_key is declared with "setter => false", so it cannot be part of the payload: sending it is
    # rejected with error 93. Omitting it must therefore preserve it rather than wipe it.
    When I use Webservice with key "CUSTOMERSCUSTOMERSCUSTOMERSCUSTO" to update "customers" for reference "customer1"
      | key       | value                   |
      | firstname | Updated                 |
      | lastname  | Service                 |
      | email     | ws.customer@example.com |
      | passwd    | Pr3st4Sh0P              |
    Then using Webservice with key "CUSTOMERSCUSTOMERSCUSTOMERSCUSTO" to view "customers" for reference "customer1", I should get following properties:
      | key       | value   |
      | firstname | Updated |
    And using Webservice with key "CUSTOMERSCUSTOMERSCUSTOMERSCUSTO" to view "customers" for reference "customer1", I should get following non empty properties:
      | key        |
      | secure_key |
