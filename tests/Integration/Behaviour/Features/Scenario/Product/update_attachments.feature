# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-attachments
@reset-database-before-feature
@update-attachments
Feature: Update product attachments from Back Office (BO).
  As an employee I want to be able to assign/remove existing attachments to product and add new ones.
  
  Scenario: I add new product attachment
    When I add new attachment ""
