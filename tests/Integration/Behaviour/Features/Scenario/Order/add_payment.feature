@reset-database-before-feature
Feature: Order payment from Back Office
  In order to track received customer payments
  As a BO user
  I need to be able to pay for the chosen order

  Background:
    Given email sending is disabled
    And the current currency is "USD"

  Scenario: pay order with negative amount and see it is not valid
    When order 1 has 0 payments
    And I pay order 1 with the invalid following details:
      | date                | payment_method    | transaction_id              | id_currency | amount | id_invoice |
      | 2019-11-26 13:56:22 | Payments by check | test!@#$%%^^&* OR 1=1 _     | 1           | -5.548 | 0          |
    Then order 1 has 0 payments

  Scenario: pay for order
    When I pay order 1 with the following details:
      | date                | payment_method    | transaction_id              | id_currency | amount | id_invoice |
      | 2019-11-26 13:56:23 | Payments by check | test123                     | 1           | 6.00   | 0          |
    Then order 1 payments should have the following details:
      | date                | payment_method    | transaction_id              | amount | id_invoice |
      | 2019-11-26 13:56:23 | Payments by check | test123                     | $6.00  |            |
    When I pay order 1 with the following details:
      | date                | payment_method    | transaction_id              | id_currency | amount | id_invoice |
      | 2019-11-26 13:56:24 | Payments by check | test!@#$%%^^&* OR 1=1 _     | 1           | 100.00 | 0          |
    Then order 1 payments should have the following details:
      | date                | payment_method    | transaction_id              | amount | id_invoice |
      | 2019-11-26 13:56:23 | Payments by check | test123                     | $6.00  |            |
      | 2019-11-26 13:56:22 | Payments by check | test!@#$%%^^&* OR 1=1 _     | $10.00 |            |

  Scenario: change order state to Delivered to be able to add valid invoice to new Payment
    When order 2 has 0 payments
    And I update order 2 status to "Delivered"
    Then order 2 payments should have invoice "#IN000001"



