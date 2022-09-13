# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order_return_state
@restore-order-return-states-after-feature
Feature: OrderState
  Background:
    Given shop "shop1" with name "test_shop" exists
    And I add a new order return state "order_return_state_1st" with the following details:
      | name            | The 1st Order Return State |
      | color           | #123456                    |
    And the order return state "order_return_state_1st" should exist
    And I add a new order return state "order_return_state_2nd" with the following details:
      | name            | The 2nd Order Return State |
      | color           | #7890AB                    |
    And the order return state "order_return_state_2nd" should exist

  Scenario: Add new order return state
    When I add a new order return state "order_return_state_3rd" with the following details:
      | name            | The 3rd Order Return State |
      | color           | #CDEF12                    |
    And the order return state "order_return_state_3rd" should have the following details:
      | name            | The 3rd Order Return State |
      | color           | #CDEF12                    |
    ## Reset
    When I delete the order return state "order_return_state_3rd"

  Scenario: Edit order return state
    When I update the order return state "order_return_state_1st" with the following details:
      | color           | #345678                    |
    And the order return state "order_return_state_1st" should have the following details:
      | name            | The 1st Order Return State |
      | color           | #345678                    |

  Scenario: Delete order return state
    When I delete the order return state "order_return_state_1st"
    And the order return state "order_return_state_1st" shouldn't exist

  Scenario: Bulk Delete
    When I bulk delete order return states "order_return_state_1st,order_return_state_2nd"
    And the order return state "order_return_state_1st" shouldn't exist
    And the order return state "order_return_state_2nd" shouldn't exist
