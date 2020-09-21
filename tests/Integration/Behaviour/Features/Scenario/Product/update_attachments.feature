# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-attachments
@reset-database-before-feature
@update-attachments
Feature: Update product attachments from Back Office (BO).
  As an employee I want to be able to assign/remove existing attachments to product and add new ones.

  Scenario: I associate attachment with product
    When I add product "product1" with following information:
      | name       | en-US:mug with photo |
      | is_virtual | false                |
    Then product "product1" should have following values:
      | active           | false          |
    And product "product1" type should be standard
    Given I add new attachment "att1" with following properties:
      | description | en-US:puffin photo nr1 |
      | name        | en-US:puffin           |
      | file_name   | app_icon.png           |
    Then attachment "att1" should have following properties:
      | description | en-US:puffin photo nr1 |
      | name        | en-US:puffin           |
      | file_name   | app_icon.png           |
      | mime        | image/png              |
      | size        | 19187                  |
    When I associate attachment "att1" with product product1
