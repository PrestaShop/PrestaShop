# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_service --tags customer-management
@restore-all-tables-before-feature
@customer-management
Feature: Customer service summary

  Scenario: Get default customer service summary
    Given that default contacts exists
    Then customer service should fine two services

  Scenario: Add customer thread
    When I add new customer thread "thread1" with following properties:
      | message   | test message |
      | contactId | 2            |
    Then contact 1 should have 0 threads
    And contact 2 should have 1 threads
