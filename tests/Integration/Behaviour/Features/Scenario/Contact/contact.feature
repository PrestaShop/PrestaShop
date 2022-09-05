@restore-all-tables-before-feature
Feature: Contact
  In order to create customizable contact us form for customers
  As a BO user
  I should be able to add and edit new contact

  Scenario: Add new contact
    When I add new contact "contact1" with the following details:
      | title                     | test1               |
      | email_address             | test@prestashop.com |
      | is_message_saving_enabled | true                |
      | description               | test123             |
      | shop_id_association       | 1                   |
    Then contact "contact1" should have the following details:
      | title                     | test1               |
      | email_address             | test@prestashop.com |
      | is_message_saving_enabled | true                |
      | description               | test123             |
      | shop_id_association       | 1                   |

  Scenario: Edit existing contact
    When I add new contact "contact2" with the following details:
      | title                     | test service 3      |
      | email_address             | test@prestashop.com |
      | is_message_saving_enabled | false               |
      | description               | test321             |
      | shop_id_association       | 1                   |
    And I update contact "contact2" with the following details:
      | title                     | test service 2      |
      | email_address             | test@prestashop.com |
      | is_message_saving_enabled | true                |
      | description               | test12345           |
      | shop_id_association       | 1                   |
    Then contact "contact2" should have the following details:
      | title                     | test service 2      |
      | email_address             | test@prestashop.com |
      | is_message_saving_enabled | true                |
      | description               | test12345           |
      | shop_id_association       | 1                   |

