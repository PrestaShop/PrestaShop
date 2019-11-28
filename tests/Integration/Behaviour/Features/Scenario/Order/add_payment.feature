@reset-database-before-feature
Feature: Order payment from Back Office
  PrestaShop allows to add payment for order
  As a BO user
  I need to be able to add payment for the chosen order

  Background:
    Given email sending is disabled
    Given the current currency is "USD"
    Given there is existing order with id 1
#   todo: invoice is needed to be created and id_invoice should be not just 0

  Scenario: add order payment with negative amount to get exception Property Order->total_paid_real is not valid
    When I add payment to order id 1 exception is thrown with the following properties:
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
      | 2019-11-26 13:56:22 | Payments by check | test!@#$%%^^&* OR 1=1 _     | 100.00 |            |
