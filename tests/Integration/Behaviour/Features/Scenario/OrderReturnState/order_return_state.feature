# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order_return_state
@restore-order-return-states-before-scenario
@restore-order-return-states-after-feature
Feature: OrderState
  Background:
    Given shop "shop1" with name "test_shop" exists
    And I add a new order return state "order_return_state_1st" with the following details:
      | name               | The 1st Order Return State |
      | color              | #123456                  |
      | is_cancelling_return | 0                          |
    And the order return state "order_return_state_1st" should exist
    And I add a new order return state "order_return_state_2nd" with the following details:
      | name               | The 2nd Order Return State |
      | color              | #7890AB                  |
      | is_cancelling_return | 1                          |
    And the order return state "order_return_state_2nd" should exist

  Scenario: Add new order return state
    When I add a new order return state "order_return_state_3rd" with the following details:
      | name               | The 3rd Order Return State |
      | color              | #CDEF12                  |
      | is_cancelling_return | 0                          |
    And the order return state "order_return_state_3rd" should have the following details:
      | name               | The 3rd Order Return State |
      | color              | #CDEF12                  |
      | is_cancelling_return | 0                          |
    ## Reset
    When I delete the order return state "order_return_state_3rd"

  Scenario: Edit order return state
    When I update the order return state "order_return_state_1st" with the following details:
      | color           | #345678                    |
    And the order return state "order_return_state_1st" should have the following details:
      | name               | The 1st Order Return State |
      | color              | #345678                  |
      | is_cancelling_return | 0                          |
    When I update the order return state "order_return_state_1st" with the following details:
      | is_cancelling_return | 1                          |
    And the order return state "order_return_state_1st" should have the following details:
      | name               | The 1st Order Return State |
      | color              | #345678                  |
      | is_cancelling_return | 1                          |

  Scenario: Delete order return state
    When I delete the order return state "order_return_state_1st"
    And the order return state "order_return_state_1st" shouldn't exist

  Scenario: Bulk Delete
    When I bulk delete order return states "order_return_state_1st,order_return_state_2nd"
    And the order return state "order_return_state_1st" shouldn't exist
    And the order return state "order_return_state_2nd" shouldn't exist

  Scenario: Adding an order return state that reuses an existing name is refused
    When I add a new order return state "order_return_state_duplicate" with the following details:
      | name               | The 1st Order Return State |
      | color              | #CDEF12                  |
      | is_cancelling_return | 0                          |
    Then I should get an error that the order return state name is already used

  Scenario: Renaming an order return state to the name of another one is refused
    When I update the order return state "order_return_state_2nd" with the following details:
      | name               | The 1st Order Return State |
    Then I should get an error that the order return state name is already used
    And the order return state "order_return_state_2nd" should have the following details:
      | name               | The 2nd Order Return State |
      | color              | #7890AB                  |
      | is_cancelling_return | 1                          |

  Scenario: Saving an order return state while keeping its own name is accepted
    When I update the order return state "order_return_state_1st" with the following details:
      | name               | The 1st Order Return State |
      | color              | #ABCDEF                  |
    And the order return state "order_return_state_1st" should have the following details:
      | name               | The 1st Order Return State |
      | color              | #ABCDEF                  |
      | is_cancelling_return | 0                          |
