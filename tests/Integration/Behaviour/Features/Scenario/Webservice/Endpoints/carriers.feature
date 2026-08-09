# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s webservice --tags webservice-endpoints-carriers
@restore-all-tables-before-feature
@webservice-endpoints-carriers
Feature: Webservice carriers endpoint
  PrestaShop allows API users to manage carriers through the Webservice
  As an API user
  I must be able to update a carrier without destroying its reference

  Background:
    Given shop "shop1" with name "test_shop" exists
    And I restore tables "webservice_account,webservice_account_shop,webservice_permission"
    And shop configuration for "PS_WEBSERVICE" is set to 1
    And I add a new webservice key with specified properties:
      | key              | CARRIERSCARRIERSCARRIERSCARRIERS |
      | description      | Carriers key                     |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |
      | permission_GET   | carriers                         |
      | permission_POST  | carriers                         |
      | permission_PUT   | carriers                         |

  Scenario: The carrier reference survives an update that omits it
    When I use Webservice with key "CARRIERSCARRIERSCARRIERSCARRIERS" to add "carriers" for reference "carrier1"
      | key    | value      | language |
      | name   | WS carrier |          |
      | active | 1          |          |
      | delay  | 3 days     | 1        |
    Then using Webservice with key "CARRIERSCARRIERSCARRIERSCARRIERS" to view "carriers" for reference "carrier1", I should get following non empty properties:
      | key          |
      | id_reference |

    # id_reference is an internal versioning key, set by Carrier::add() and read by getCarrierByReference().
    # It is not writable through the Webservice, so omitting it must preserve it rather than reset it to 0.
    When I use Webservice with key "CARRIERSCARRIERSCARRIERSCARRIERS" to update "carriers" for reference "carrier1"
      | key    | value              | language |
      | name   | WS carrier renamed |          |
      | active | 1                  |          |
      | delay  | 3 days             | 1        |
    Then using Webservice with key "CARRIERSCARRIERSCARRIERSCARRIERS" to view "carriers" for reference "carrier1", I should get following properties:
      | key  | value              |
      | name | WS carrier renamed |
    And using Webservice with key "CARRIERSCARRIERSCARRIERSCARRIERS" to view "carriers" for reference "carrier1", I should get following non empty properties:
      | key          |
      | id_reference |
