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

  Scenario: Render the forwarded copy of the latest message
    Then the forwarded copy of customer thread "thread1" for an employee should contain "test message2"
    And the forwarded copy of customer thread "thread1" for an employee should contain "Sent by:"
    And the forwarded copy of customer thread "thread1" for an employee should contain "test@gmail.com"
    And the forwarded copy of customer thread "thread1" for an employee should contain "/sell/customer-service/customer-threads/"
    And the forwarded copy of customer thread "thread1" for an employee should not contain "_token="
    And the forwarded copy of customer thread "thread1" for an employee should not contain "/http"
    And the forwarded copy of customer thread "thread1" for an employee should contain "View this thread"
    And the forwarded copy of customer thread "thread1" for someone else should not contain "View this thread"

  Scenario: The forwarded copy escapes the message content
    Given I add new customer thread "thread2" with following properties:
      | message | <b>bold</b> |
    Then the forwarded copy of customer thread "thread2" for someone else should contain "&lt;b&gt;bold&lt;/b&gt;"

  Scenario: Forward thread to an email address
    When I forward customer thread "thread1" to "someone@example.com" with comment "please check"
    Then the forwarded email should be sent to "someone@example.com"
    And the forwarded email should contain "test message2"
    And the forwarded email should not contain "View this thread"
    And customer thread "thread1" latest message should contain "Message forwarded to someone@example.com"
    And customer thread "thread1" latest message should contain "Comment: please check"
    And customer thread "thread1" latest message should be public

  Scenario: Forward thread to an employee
    When I forward customer thread "thread1" to employee "test@prestashop.com" with comment "for you"
    Then the forwarded email should be sent to "test@prestashop.com"
    And the forwarded email should contain "View this thread"
    And the forwarded email should contain "/sell/customer-service/customer-threads/"
    And the forwarded email should not contain "_token="
    And customer thread "thread1" latest message should contain "Message forwarded to Puffin Mummy"
    And customer thread "thread1" latest message should contain "Comment: for you"
    And customer thread "thread1" latest message should be private

  Scenario: Forward a thread that does not exist
    When I forward customer thread with id 999999 to "someone@example.com" with comment "please check"
    Then I should get an error that the customer thread was not found

  Scenario: I delete thread
    When I delete thread "thread1"
    Then thread "thread1" should be deleted
