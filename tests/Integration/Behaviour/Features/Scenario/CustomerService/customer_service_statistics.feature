# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_service --tags customer-service-statistics
@restore-all-tables-before-feature
@customer-service-statistics
Feature: Customer service listing statistics

  Background:
    Given there are no customer threads

  Scenario: An empty installation reports zero counters
    Then customer service listing statistics should be:
      | total_threads     | 0 |
      | open_threads      | 0 |
      | pending_threads   | 0 |
      | closed_threads    | 0 |
      | customer_messages | 0 |
      | employee_messages | 0 |

  Scenario: Statistics aggregate threads grouped by status
    When I add new customer thread "stats-open" with status "open" and message "open thread"
    And I add new customer thread "stats-pending1" with status "pending1" and message "pending1 thread"
    And I add new customer thread "stats-pending2" with status "pending2" and message "pending2 thread"
    And I add new customer thread "stats-closed" with status "closed" and message "closed thread"
    Then customer service listing statistics should be:
      | total_threads     | 4 |
      | open_threads      | 1 |
      | pending_threads   | 2 |
      | closed_threads    | 1 |
      | customer_messages | 4 |
      | employee_messages | 0 |

  Scenario: Employee messages are counted separately from customer messages
    When I add new customer thread "stats-employee-msg" with status "open" and message "customer message"
    And employee replies to customer thread "stats-employee-msg" with message "employee reply"
    Then customer service listing statistics should be:
      | total_threads     | 1 |
      | open_threads      | 1 |
      | pending_threads   | 0 |
      | closed_threads    | 0 |
      | customer_messages | 1 |
      | employee_messages | 1 |

  Scenario: Statistics are scoped by shop context
    Given customer service statistics shops are available
    When I add new customer thread "stats-shop1" in shop "stats_shop_1" with status "open" and message "shop 1 thread"
    And I add new customer thread "stats-shop2" in shop "stats_shop_2" with status "closed" and message "shop 2 thread"
    And customer service statistics are scoped to shop "stats_shop_1"
    Then customer service listing statistics should be:
      | total_threads     | 1 |
      | open_threads      | 1 |
      | pending_threads   | 0 |
      | closed_threads    | 0 |
      | customer_messages | 1 |
      | employee_messages | 0 |
    When customer service statistics are scoped to all shops
    Then customer service listing statistics should be:
      | total_threads     | 2 |
      | open_threads      | 1 |
      | pending_threads   | 0 |
      | closed_threads    | 1 |
      | customer_messages | 2 |
      | employee_messages | 0 |
