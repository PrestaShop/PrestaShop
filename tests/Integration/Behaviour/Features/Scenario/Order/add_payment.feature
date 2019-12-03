@reset-database-before-feature
Feature: Order payment from Back Office
  PrestaShop allows to add payment for order
  As a BO user
  I need to be able to add payment for the chosen order

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And there is existing order with id 1

  Scenario: add order payment with negative amount to get exception Property Order->total_paid_real is not valid
    When if I query order id 1 payments I should get 0 payments
    And I add payment to order id 1 exception is thrown with the following properties:
      | date                | payment_method    | transaction_id              | id_currency | amount | id_invoice |
      | 2019-11-26 13:56:22 | Payments by check | test!@#$%%^^&* OR 1=1 _     | 1           | -5.548 | 0          |
    Then if I query order id 1 payments I should get 0 payments

  Scenario: add order payment
    When I add payment to order id 1 with the following properties:
      | date                | payment_method    | transaction_id              | id_currency | amount | id_invoice |
      | 2019-11-26 13:56:23 | Payments by check | test123                     | 1           | 6      | 0          |
    Then if I query order id 1 payments I should get an Order with properties:
      | date                | payment_method    | transaction_id              | amount | id_invoice |
      | 2019-11-26 13:56:23 | Payments by check | test123                     | $6     |            |
    When I add payment to order id 1 with the following properties:
      | date                | payment_method    | transaction_id              | id_currency | amount | id_invoice |
      | 2019-11-26 13:56:24 | Payments by check | test!@#$%%^^&* OR 1=1 _     | 1           | 100.00 | 0          |
    Then if I query order id 1 payments I should get an Order with properties:
      | date                | payment_method    | transaction_id              | amount | id_invoice |
      | 2019-11-26 13:56:23 | Payments by check | test123                     | $6     |            |
      | 2019-11-26 13:56:22 | Payments by check | test!@#$%%^^&* OR 1=1 _     | $10.00 |            |

  Scenario: change order state to Delivered to be able to add valid invoice to new Payment
    When if I query order id 2 payments I should get 0 payments
    And I update order 2 to status "Delivered"
    Then if I query order id 2 payments I should get an Order with properties:
    | payment_method    | amount | id_invoice |
    | Payments by check |        | #IN000001  |
    When I add payment to order id 2 with the following properties:
    | date                | payment_method    | transaction_id              | id_currency | amount | id_invoice |
    | 2019-11-26 13:56:24 | Payments by check | test!@#$%^&*()_+~/*-<{}     | 1           | 100.00 | 1          |
    Then if I query order id 2 payments I should get an Order with properties:
    | date                | payment_method    | transaction_id              | amount  | id_invoice |
    |                     | Payments by check |                             |         | #IN000001  |
    | 2019-11-26 13:56:24 | Payments by check | test!@#$%^&*()_+~/*-<{}     | $100.00 | #IN000001  |



