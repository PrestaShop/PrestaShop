# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-description
@reset-database-before-feature
Feature: Update product descriptions from Back Office
  As a BO user I need to be able to update product description and short description

  @update-description
  Scenario: Update product descriptions
# todo: blocked. first need to create a product. #18672
