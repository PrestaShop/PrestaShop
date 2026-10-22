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

  Scenario: Delete a single contact
    When I add new contact "contact_to_delete" with the following details:
      | title                     | Contact to delete   |
      | email_address             | test@prestashop.com |
      | is_message_saving_enabled | true                |
      | description               | test delete         |
      | shop_id_association       | 1                   |
    And I delete contact "contact_to_delete"
    Then contact "contact_to_delete" should not exist

  Scenario: Bulk delete multiple contacts
    When I add new contact "bulk_contact_1" with the following details:
      | title                     | Bulk 1              |
      | email_address             | test@prestashop.com |
      | is_message_saving_enabled | true                |
      | description               | delete bulk 1       |
      | shop_id_association       | 1                   |
    And I add new contact "bulk_contact_2" with the following details:
      | title                     | Bulk 2              |
      | email_address             | test@prestashop.com |
      | is_message_saving_enabled | true                |
      | description               | delete bulk 2       |
      | shop_id_association       | 1                   |
    And I bulk delete contacts "bulk_contact_1, bulk_contact_2"
    Then contact "bulk_contact_1" should not exist
    And contact "bulk_contact_2" should not exist