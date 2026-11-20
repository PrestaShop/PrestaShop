# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s withdrawal_request
@restore-all-tables-before-feature
@clear-cache-before-feature
Feature: Withdrawal request management
  As a consumer I must be able to withdraw from an order online during the withdrawal period
  As a merchant I must be able to follow the withdrawal requests up
  (Directive 2011/83/EU, article 11a, added by Directive (EU) 2023/2673)

  Background:
    Given email sending is disabled
    And the current currency is "USD"
    And country "US" is enabled
    And the module "dummy_payment" is installed
    And I am logged in as "test@prestashop.com" employee
    And there is customer "testCustomer" with email "pub@prestashop.com"
    And customer "testCustomer" has address in "US" country
    And shop configuration for "PS_WITHDRAWAL_FUNCTION" is set to 1
    And shop configuration for "PS_WITHDRAWAL_PERIOD" is set to 14
    And I create an empty cart "withdrawal_cart" for customer "testCustomer"
    And I select "US" address as delivery and invoice address for customer "testCustomer" in cart "withdrawal_cart"
    And I add 1 products "Mug The best is yet to come" to the cart "withdrawal_cart"
    And I add order "withdrawal_order" with the following details:
      | cart                | withdrawal_cart            |
      | message             | test                       |
      | payment module name | dummy_payment              |
      | status              | Awaiting bank wire payment |

  Scenario: An order that is not delivered yet can be withdrawn from at any time
    Then the withdrawal eligibility of order "withdrawal_order" on "2030-01-01 12:00:00" should be:
      | eligible | true |
      | reason   |      |
      | deadline |      |

  Scenario: The withdrawal period starts the day after delivery and ends at the end of its last day
    Given order "withdrawal_order" was delivered on "2026-11-02 08:15:00"
    Then the withdrawal eligibility of order "withdrawal_order" on "2026-11-16 23:59:59" should be:
      | eligible | true                |
      | reason   |                     |
      | deadline | 2026-11-16 23:59:59 |
    And the withdrawal eligibility of order "withdrawal_order" on "2026-11-17 00:00:00" should be:
      | eligible | false               |
      | reason   | period_expired      |
      | deadline | 2026-11-16 23:59:59 |

  Scenario: A withdrawal period ending on a weekend is extended to the next Monday
    Given order "withdrawal_order" was delivered on "2026-11-07 10:00:00"
    Then the withdrawal eligibility of order "withdrawal_order" on "2026-11-23 18:00:00" should be:
      | eligible | true                |
      | deadline | 2026-11-23 23:59:59 |

  Scenario: The merchant can grant a longer withdrawal period
    Given shop configuration for "PS_WITHDRAWAL_PERIOD" is set to 30
    And order "withdrawal_order" was delivered on "2026-11-02 08:15:00"
    Then the withdrawal eligibility of order "withdrawal_order" on "2026-11-20 10:00:00" should be:
      | eligible | true                |
      | deadline | 2026-12-02 23:59:59 |

  Scenario: Withdraw from an order
    Given order "withdrawal_order" was delivered on "2026-11-02 08:15:00"
    When I withdraw from order "withdrawal_order" on "2026-11-10 14:32:10" with request "withdrawal_1" and the following statement:
      | firstname | John                |
      | lastname  | DOE                 |
      | email     | john.doe@example.com |
      | message   | Too small           |
    Then withdrawal request "withdrawal_1" should have the following properties:
      | order        | withdrawal_order     |
      | firstname    | John                 |
      | lastname     | DOE                  |
      | email        | john.doe@example.com |
      | message      | Too small            |
      | status       | pending              |
      | submitted at | 2026-11-10 14:32:10  |
    And the withdrawal eligibility of order "withdrawal_order" on "2026-11-11 10:00:00" should be:
      | eligible | false             |
      | reason   | already_requested |

  Scenario: A statement submitted on the last day of the period is on time
    Given order "withdrawal_order" was delivered on "2026-11-02 08:15:00"
    When I withdraw from order "withdrawal_order" on "2026-11-16 23:59:59" with request "withdrawal_last_day" and the following statement:
      | firstname | John                 |
      | lastname  | DOE                  |
      | email     | pub@prestashop.com   |
    Then withdrawal request "withdrawal_last_day" should have the following properties:
      | status       | pending             |
      | message      |                     |
      | submitted at | 2026-11-16 23:59:59 |

  Scenario: Cannot withdraw twice from the same order
    When I withdraw from order "withdrawal_order" on "2026-11-10 14:32:10" with request "withdrawal_first" and the following statement:
      | firstname | John               |
      | lastname  | DOE                |
      | email     | pub@prestashop.com |
    And I try to withdraw from order "withdrawal_order" on "2026-11-10 14:35:00" with the following statement:
      | firstname | John               |
      | lastname  | DOE                |
      | email     | pub@prestashop.com |
    Then I should get an error that the order is not eligible for withdrawal because "already_requested"

  Scenario: Cannot withdraw after the end of the withdrawal period
    Given order "withdrawal_order" was delivered on "2026-11-02 08:15:00"
    When I try to withdraw from order "withdrawal_order" on "2026-11-17 00:00:01" with the following statement:
      | firstname | John               |
      | lastname  | DOE                |
      | email     | pub@prestashop.com |
    Then I should get an error that the order is not eligible for withdrawal because "period_expired"

  Scenario: Cannot withdraw from a cancelled order
    Given I update order "withdrawal_order" status to "Canceled"
    Then the withdrawal eligibility of order "withdrawal_order" on "2026-11-10 10:00:00" should be:
      | eligible | false           |
      | reason   | order_cancelled |
    When I try to withdraw from order "withdrawal_order" on "2026-11-10 10:00:00" with the following statement:
      | firstname | John               |
      | lastname  | DOE                |
      | email     | pub@prestashop.com |
    Then I should get an error that the order is not eligible for withdrawal because "order_cancelled"

  Scenario: The statement must hold the name and the email of the consumer
    When I try to withdraw from order "withdrawal_order" on "2026-11-10 10:00:00" with the following statement:
      | firstname |                    |
      | lastname  | DOE                |
      | email     | pub@prestashop.com |
    Then I should get an error that the withdrawal request "firstname" is invalid
    When I try to withdraw from order "withdrawal_order" on "2026-11-10 10:00:00" with the following statement:
      | firstname | John               |
      | lastname  |                    |
      | email     | pub@prestashop.com |
    Then I should get an error that the withdrawal request "lastname" is invalid
    When I try to withdraw from order "withdrawal_order" on "2026-11-10 10:00:00" with the following statement:
      | firstname | John |
      | lastname  | DOE  |
      | email     |      |
    Then I should get an error that the withdrawal request "email" is invalid

  Scenario: The merchant follows the withdrawal request up
    When I withdraw from order "withdrawal_order" on "2026-11-10 14:32:10" with request "withdrawal_followed" and the following statement:
      | firstname | John               |
      | lastname  | DOE                |
      | email     | pub@prestashop.com |
    And I mark withdrawal request "withdrawal_followed" as "processed"
    Then withdrawal request "withdrawal_followed" should have the following properties:
      | status | processed |
    When I mark withdrawal request "withdrawal_followed" as "refused"
    Then withdrawal request "withdrawal_followed" should have the following properties:
      | status | refused |
    When I try to mark withdrawal request "withdrawal_followed" as "archived"
    Then I should get an error that the withdrawal request "status" is invalid
    When I try to mark the unknown withdrawal request 999999 as "processed"
    Then I should get an error that the withdrawal request was not found

  Scenario: The withdrawal function can be disabled
    Given shop configuration for "PS_WITHDRAWAL_FUNCTION" is set to 0
    Then the withdrawal eligibility of order "withdrawal_order" on "2026-11-10 10:00:00" should be:
      | eligible | false            |
      | reason   | feature_disabled |
    When I try to withdraw from order "withdrawal_order" on "2026-11-10 10:00:00" with the following statement:
      | firstname | John               |
      | lastname  | DOE                |
      | email     | pub@prestashop.com |
    Then I should get an error that the order is not eligible for withdrawal because "feature_disabled"
