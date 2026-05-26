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

  Scenario: Update thread status to open
    When I update thread "thread1" status to open
    Then customer thread "thread1" should be open

  Scenario: Mark thread as pending status 1
    When I update thread "thread1" status to pending1
    Then customer thread "thread1" should be pending1

  Scenario: Mark thread as pending status 2
    When I update thread "thread1" status to pending2
    Then customer thread "thread1" should be pending2

  Scenario: I delete thread
    When I delete thread "thread1"
    Then thread "thread1" should be deleted

  Scenario: Bulk delete several threads
    When I add new customer thread "thread-bulk-1" with following properties:
      | message    | bulk one |
    And I add new customer thread "thread-bulk-2" with following properties:
      | message    | bulk two |
    And I bulk delete customer threads: "thread-bulk-1, thread-bulk-2"
    Then thread "thread-bulk-1" should be deleted
    And thread "thread-bulk-2" should be deleted

  Scenario: Deleting a non-existent thread raises a not-found error
    When I delete non-existent customer thread with id 999999
    Then I should get error that customer thread does not exist

  Scenario: Bulk deleting a non-existent thread raises a not-found error
    When I bulk delete non-existent customer threads with ids 999998, 999999
    Then I should get error that customer thread does not exist

  Scenario: Updating status of a non-existent thread raises an error
    When I update non-existent customer thread with id 999999 status to open
    Then I should get error that customer thread status update failed
