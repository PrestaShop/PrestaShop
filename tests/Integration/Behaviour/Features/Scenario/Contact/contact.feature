@reset-database-before-feature
Feature: Contact
  In order to create customizable contact us form for customers
  As a BO user
  I should be able to add and edit new contact

  Scenario: Add new contact
    When I add new contact with the following details:
      | title               | email_address       | is_message_saving_enabled  | description | shop_id_association |
      | test service 1      | test@prestashop.com | true                       | test123     | 1                   |
    Then contact 3 should have the following details:
      | title               | email_address       | is_message_saving_enabled  | description | shop_id_association |
      | test service 1      | test@prestashop.com | true                       | test123     | 1                   |

  Scenario: Edit existing contact
    When I add new contact with the following details:
      | title               | email_address       | is_message_saving_enabled  | description | shop_id_association |
      | test service 2      | test@prestashop.com | true                       | test123     | 1                   |
    And I update contact 4 with the following details:
      | title               | email_address       | is_message_saving_enabled  | description | shop_id_association |
      | test service 3      | test@prestashop.com | false                      | test321     | 1                   |
    Then contact 4 should have the following details:
      | title               | email_address       | is_message_saving_enabled  | description | shop_id_association |
      | test service 3      | test@prestashop.com | false                      | test321     | 1                   |

