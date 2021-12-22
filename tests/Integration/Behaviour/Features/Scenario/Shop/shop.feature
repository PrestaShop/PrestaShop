# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s shop
@restore-all-tables-before-feature
@reset-img-after-feature
@shop
Feature: Shop Management
  In order to personalize the shop
  As a BO user
  I must be able to upload logos

  Scenario: Uploading a new Header logo should update the associated configuration keys
    When I upload "/tests/Resources/assets/new_logo.jpg" as new Header Logo
    Then the logo size configuration should be 510 x 170
