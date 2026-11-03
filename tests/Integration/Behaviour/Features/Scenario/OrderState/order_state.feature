# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s order_state
@restore-order-states-after-feature
Feature: OrderState
  Background:
    Given shop "shop1" with name "test_shop" exists
    And language "en" with locale "en-US" exists
    And language "fr" with locale "fr-FR" exists
    And I add a new order state "order_state_1st" with the following details:
      | name[en-US]     | The 1st Order State       |
      | name[fr-FR]     | Le 1er statut de commande |
      | color           | #123456                   |
      | isLoggable      | 0                         |
      | isInvoice       | 0                         |
      | isHidden        | 0                         |
      | hasSendMail     | 0                         |
      | hasPdfInvoice   | 0                         |
      | hasPdfDelivery  | 0                         |
      | isShipped       | 0                         |
      | isPaid          | 0                         |
      | isDelivery      | 0                         |
    And the order state "order_state_1st" should exist
    And I add a new order state "order_state_2nd" with the following details:
      | name[en-US]     | The 2nd Order State       |
      | name[fr-FR]     | Le 2e statut de commande  |
      | color           | #7890AB                   |
      | isLoggable      | 0                         |
      | isInvoice       | 0                         |
      | isHidden        | 0                         |
      | hasSendMail     | 0                         |
      | hasPdfInvoice   | 0                         |
      | hasPdfDelivery  | 0                         |
      | isShipped       | 0                         |
      | isPaid          | 0                         |
      | isDelivery      | 0                         |
    And the order state "order_state_2nd" should exist

  Scenario: Add new order state
    When I add a new order state "order_state_3rd" with the following details:
      | name[en-US]     | The 3rd Order State       |
      | name[fr-FR]     | Le 3e statut de commande  |
      | color           | #CDEF12                   |
      | isLoggable      | 0                         |
      | isInvoice       | 0                         |
      | isHidden        | 0                         |
      | hasSendMail     | 0                         |
      | hasPdfInvoice   | 0                         |
      | hasPdfDelivery  | 0                         |
      | isShipped       | 0                         |
      | isPaid          | 0                         |
      | isDelivery      | 0                         |
    And the order state "order_state_3rd" should have the following details:
      | name[en-US]     | The 3rd Order State       |
      | name[fr-FR]     | Le 3e statut de commande  |
      | color           | #CDEF12                   |
      | hasSendMail     | 0                         |
      | template[en-US] |                           |
      | template[fr-FR] |                           |
    ## Reset
    When I delete the order state "order_state_3rd"
    And the order state "order_state_3rd" should be deleted

  Scenario: Edit order state
    When I update the order state "order_state_1st" with the following details:
      | color           | #345678                   |
    And the order state "order_state_1st" should have the following details:
      | name[en-US]     | The 1st Order State       |
      | name[fr-FR]     | Le 1er statut de commande |
      | color           | #345678                   |
      | hasSendMail     | 0                         |
      | template[en-US] |                           |
      | template[fr-FR] |                           |

  Scenario: Edit order state (with Send Email disabled)
    When I update the order state "order_state_1st" with the following details:
      | color           | #345678                   |
      | hasSendMail     | 0                         |
      | template[en-US] | account                   |
      | template[fr-FR] | account                   |
    And the order state "order_state_1st" should have the following details:
      | name[en-US]     | The 1st Order State       |
      | name[fr-FR]     | Le 1er statut de commande |
      | color           | #345678                   |
      | hasSendMail     | 0                         |
      | template[en-US] |                           |
      | template[fr-FR] |                           |

  Scenario: Edit order state (with Send Email enabled)
    When I update the order state "order_state_1st" with the following details:
      | color           | #345678                   |
      | hasSendMail     | 1                         |
      | template[en-US] | account                   |
      | template[fr-FR] | account                   |
    And the order state "order_state_1st" should have the following details:
      | name[en-US]     | The 1st Order State       |
      | name[fr-FR]     | Le 1er statut de commande |
      | color           | #345678                   |
      | hasSendMail     | 1                         |
      | template[en-US] | account                   |
      | template[fr-FR] | account                   |

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
    When I update the order state "order_state_1st" with the following details:
      | name[fr-FR]     | Le 1er statut modifié     |
    Then the order state "order_state_1st" should have the following details:
      | name[en-US]     | The 1st Order State       |
      | name[fr-FR]     | Le 1er statut modifié     |
