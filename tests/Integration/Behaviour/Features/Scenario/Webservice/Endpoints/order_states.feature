# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s webservice --tags webservice-endpoints-order-states
@restore-all-tables-before-feature
@webservice-endpoints-order-states
Feature: Webservice order states endpoint
  PrestaShop allows API users to list order states
  As an API user
  I must be able to sort a list on a translatable and on a non translatable field at once

  Background:
    Given shop "shop1" with name "test_shop" exists
    And I restore tables "webservice_account,webservice_account_shop,webservice_permission"
    And shop configuration for "PS_WEBSERVICE" is set to 1
    And I add a new webservice key with specified properties:
      | key              | ENABLEDENABLEDENABLEDENABLEDENAB |
      | description      | Enabled key                      |
      | is_enabled       | 1                                |
      | shop_association | shop1                            |
    And I edit webservice key "ENABLEDENABLEDENABLEDENABLEDENAB" with specified properties:
      | description    | Enabled key with Permissions |
      | permission_GET | order_states                 |

  Scenario: Sort on a non translatable field listed before a translatable one
    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to sort "order_states" by "[id_ASC,name_ASC]"
    Then I should get 0 errors
    And I should get 13 items of type "order_state"

  Scenario: Sort on a non translatable field listed after a translatable one
    When I use Webservice with key "ENABLEDENABLEDENABLEDENABLEDENAB" to sort "order_states" by "[name_ASC,id_ASC]"
    Then I should get 0 errors
    And I should get 13 items of type "order_state"
