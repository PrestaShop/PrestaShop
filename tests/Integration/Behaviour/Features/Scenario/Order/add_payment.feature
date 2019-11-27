@reset-database-before-feature
Feature: Order payment from Back Office
  PrestaShop allows to add payment for order
  As a BO user
  I need to be able to add payment for the chosen order

  Background:
    Given email sending is disabled
    Given the current currency is "USD"
    Given there is existing order with id 1

  Scenario: add order payment
    When I add payment to order with id 1 with the following properties:
      | date                | paymentMethod     | transactionId                                  | amount | invoice   |
      | 2019-11-26 13:56:22 | Payments by check | test123                                        | 5.54   | #IN000001 |
    Then if I query order with id 1 payments I should get an Order with properties:
      | date                | paymentMethod     | transactionId                                  | amount | invoice   |
      | 2019-11-26 13:56:22 | Payments by check | test123                                        | 5.54   | #IN000001 |
    When I add payment to order with id 1 with the following properties:
      | date                | paymentMethod     | transactionId                                  | amount | invoice   |
      | 2019-11-26 13:56:23 | Payments by check | test!@#$%%^^&* OR 1=1 _                        | -5.548 | #IN000002 |
    Then if I query order with id 1 payments I should get an Order with properties:
      | date                | paymentMethod     | transactionId                                  | amount | invoice   |
      | 2019-11-26 13:56:22 | Payments by check | test123                                        | 5.54   | #IN000001 |
      | 2019-11-26 13:56:23 | Payments by check | test!@#$%%^^&*_ OR 1=1                         | -5.548 | #IN000002 |
    When I add payment to order with id 1 with the following properties:
      | date                | paymentMethod     | transactionId                                  | amount | invoice   |
      | 2019-11-26 13:56:24 | Bank transfer     | SELECT id, login FROM users WHERE login = ';'  | 0.00   | #IN000003 |
    Then if I query order with id 1 payments I should get an Order with properties:
      | 2019-11-26 13:56:22 | Payments by check | test123                                        | 5.54   | #IN000001 |
      | 2019-11-26 13:56:23 | Payments by check | test!@#$%%^^&* OR 1=1 _                        | -5.548 | #IN000002 |
      | 2019-11-26 13:56:24 | Bank transfer     | SELECT id, login FROM users WHERE login = ';'  | 0.00   | #IN000003 |
