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

  Scenario: Update thread status to handled
    When I update thread "thread1" status to handled
    Then customer thread "thread1" should be closed

  Scenario: I delete thread
    When I delete thread "thread1"
    Then thread "thread1" should be deleted
