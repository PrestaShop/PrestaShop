# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s customer_service --tags customer-management
@restore-all-tables-before-feature
@customer-management
Feature: Customer service

  Scenario: Add customer thread
    When I add new customer thread "thread1" with following properties:
      | message    | test message |
    Then customer thread "thread1" should have the latest message "test message"

  Scenario: Response to thread
    When I respond to customer thread "thread1" with following properties:
      | reply_message    | test message2 |
    Then customer thread "thread1" should have the latest message "test message2"
    And customer thread "thread1" should be closed

  Scenario: Response to thread attributed to a given employee without an employee in the context
    Given the legacy context has no employee
    When I try to respond to customer thread "thread1" as employee 1 with following properties:
      | reply_message    | reply from the Admin API |
    Then customer thread "thread1" should have the latest message "reply from the Admin API"
    And the latest message of customer thread "thread1" should be from employee 1

  Scenario: Response to thread without any employee is refused
    Given the legacy context has no employee
    When I try to respond to customer thread "thread1" with following properties:
      | reply_message    | reply from nobody |
    Then I should get an error that the reply needs an employee
    And customer thread "thread1" should have the latest message "reply from the Admin API"

  Scenario: Response to an unknown thread is refused
    When I try to respond to customer thread "unknown_thread" with following properties:
      | reply_message    | lost reply |
    Then I should get an error that the customer thread was not found

  Scenario: Update thread status to open
    When I update thread "thread1" status to open
    Then customer thread "thread1" should be open

  Scenario: I delete thread
    When I delete thread "thread1"
    Then thread "thread1" should be deleted
