@reset-database-before-feature
Feature: Contact
  In order to create customizable contact us form for customers
  As a BO user
  I should be able to add and edit new contact

  Scenario: Add new contact
    Given there is no contact with id 3
    And there is contact with id 2
    When I add new contact with the following properties:
      | title               | email_address       | is_message_saving_enabled  | description | shop_id_association |
      | Customer service    | test@prestashop.com | true                       | test123     | 1                   |
    And contact with id 3 should have the following properties:
      | title               | email_address       | is_message_saving_enabled  | description | shop_id_association |
      | Customer service    | test@prestashop.com | true                       | test123     | 1                   |

  Scenario: Edit existing contact
    When I add new contact with the following properties:
      | title               | email_address       | is_message_saving_enabled  | description | shop_id_association |
      | Customer service    | test@prestashop.com | true                       | test123     | 1                   |
    And I update contact with id 4 with the following properties:
      | title               | email_address       | is_message_saving_enabled  | description | shop_id_association |
      | Webmaster           | test@prestashop.com | false                      | test321     | 1                   |
    Then contact with id 4 should have the following properties:
      | title               | email_address       | is_message_saving_enabled  | description | shop_id_association |
      | Webmaster           | test@prestashop.com | false                      | test321     | 1                   |

