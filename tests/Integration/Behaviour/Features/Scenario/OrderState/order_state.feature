# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order_state
@restore-order-states-after-feature
Feature: OrderState
  Background:
    Given shop "shop1" with name "test_shop" exists
    And I add a new order state "order_state_1st" with the following details:
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
    And the order state "order_state_1st" should exist
    And I add a new order state "order_state_2nd" with the following details:
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
    And the order state "order_state_2nd" should exist

  Scenario: Add new order state
    When I add a new order state "order_state_3rd" with the following details:
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
    And the order state "order_state_3rd" should have the following details:
      | name            | The 3rd Order State |
      | color           | #CDEF12             |
      | hasSendMail     | 0                   |
      | template        |                     |
    ## Reset
    When I delete the order state "order_state_3rd"
    And the order state "order_state_3rd" should be deleted

  Scenario: Edit order state
    When I update the order state "order_state_1st" with the following details:
      | color           | #345678             |
    And the order state "order_state_1st" should have the following details:
      | name            | The 1st Order State |
      | color           | #345678             |
      | hasSendMail     | 0                   |
      | template        |                     |

  Scenario: Edit order state (with Send Email disabled)
    When I update the order state "order_state_1st" with the following details:
      | color           | #345678             |
      | hasSendMail     | 0                   |
      | template        | account             |
    And the order state "order_state_1st" should have the following details:
      | name            | The 1st Order State |
      | color           | #345678             |
      | hasSendMail     | 0                   |
      | template        |                     |

  Scenario: Edit order state (with Send Email enabled)
    When I update the order state "order_state_1st" with the following details:
      | color           | #345678             |
      | hasSendMail     | 1                   |
      | template        | account             |
    And the order state "order_state_1st" should have the following details:
      | name            | The 1st Order State |
      | color           | #345678             |
      | hasSendMail     | 1                   |
      | template        | account             |

  Scenario: Delete order state
    When I delete the order state "order_state_1st"
    And the order state "order_state_1st" should be deleted
    And the order state "order_state_1st" should exist

  Scenario: Bulk Delete
    When I bulk delete order states "order_state_1st,order_state_2nd"
    And the order state "order_state_1st" should be deleted
    And the order state "order_state_1st" should exist
    And the order state "order_state_2nd" should be deleted
    And the order state "order_state_2nd" should exist

  Scenario: Bulk delete keeps the unremovable order states
    Given the order state "order_state_2nd" is unremovable
    When I bulk delete order states "order_state_1st,order_state_2nd"
    Then I should get an error that some order states could not be deleted
    And the order state "order_state_1st" should be deleted
    And the order state "order_state_2nd" shouldn't be deleted

  Scenario: Updating the name in one language keeps the other languages
    Given language "french" with locale "fr-FR" exists
    When I update the order state "order_state_1st" name in language "fr-FR" to "Le 1er statut"
    Then the order state "order_state_1st" should be named "Le 1er statut" in language "fr-FR"
    And the order state "order_state_1st" should be named "The 1st Order State" in language "en-US"
