# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order_state
@restore-order-states-after-feature
Feature: OrderState
  Background:
    Given shop "shop1" with name "test_shop" exists
    And I add a new order state with the following details:
      | name            | The 1st Order State |
      | color           | #123456             |
      | isLoggable      | 0                   |
      | isInvoice       | 0                   |
      | isHidden        | 0                   |
      | hasSendMail     | 0                   |
      | hasPdfInvoice   | 0                   |
      | hasPdfDelivery  | 0                   |
      | isShipped       | 0                   |
      | isPaid          | 0                   |
      | isDelivery      | 0                   |
    And the order state with name "The 1st Order State" should exist
    And I add a new order state with the following details:
      | name            | The 2nd Order State |
      | color           | #7890AB             |
      | isLoggable      | 0                   |
      | isInvoice       | 0                   |
      | isHidden        | 0                   |
      | hasSendMail     | 0                   |
      | hasPdfInvoice   | 0                   |
      | hasPdfDelivery  | 0                   |
      | isShipped       | 0                   |
      | isPaid          | 0                   |
      | isDelivery      | 0                   |
    And the order state with name "The 2nd Order State" should exist

  Scenario: Add new order state
    When I add a new order state with the following details:
      | name            | The 3rd Order State |
      | color           | #CDEF12             |
      | isLoggable      | 0                   |
      | isInvoice       | 0                   |
      | isHidden        | 0                   |
      | hasSendMail     | 0                   |
      | hasPdfInvoice   | 0                   |
      | hasPdfDelivery  | 0                   |
      | isShipped       | 0                   |
      | isPaid          | 0                   |
      | isDelivery      | 0                   |
    And the order state with name "The 3rd Order State" should have the following details:
      | name            | The 3rd Order State |
      | color           | #CDEF12             |
    ## Reset
    When I delete the order state with name "The 3rd Order State"

  Scenario: Edit order state
    When I update the order state with name "The 1st Order State" with the following details:
      | color           | #345678             |
    And the order state with name "The 1st Order State" should have the following details:
      | name            | The 1st Order State |
      | color           | #345678             |

  Scenario: Delete order state
    When I delete the order state with name "The 1st Order State"
    And the order state with name "The 1st Order State" shouldn't exist

  Scenario: Bulk Delete
    When I bulk delete order states with name "The 1st Order State,The 2nd Order State"
    And the order state with name "The 1st Order State" shouldn't exist
    And the order state with name "The 2nd Order State" shouldn't exist
