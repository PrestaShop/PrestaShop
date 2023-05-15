# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags update_alias_status_feature
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@toggle
Feature: Add basic alias from Back Office (BO)
  As a BO user
  I need to be able to add new alias with basic information from the BO

  Scenario: I add an alias with basic information
    Given following aliases should exist:
      | id reference | alias  | search | active |
      | alias1       | bloose | blouse | 1      |
      | alias2       | blues  | blouse | 1      |
    When I toggle alias with following information:
      | id reference |
      | alias1       |
      | alias2       |
    Then following aliases should exist:
      | id reference | alias  | search | active |
      | alias1       | bloose | blouse | 0      |
      | alias2       | blues  | blouse | 0      |
