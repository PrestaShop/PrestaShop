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
