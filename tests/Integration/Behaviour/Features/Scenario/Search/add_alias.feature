# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags add
@restore-aliases-before-feature
@clear-cache-before-feature
@clear-cache-after-feature
@add
Feature: Add basic alias from Back Office (BO)
  As a BO user
  I need to be able to add new alias with basic information from the BO

  Scenario: I add an alias with basic information
    Given following aliases should exist:
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |
    When I add alias with following information:
      | alias   | large |
      | search  | big   |
    Then following aliases should exist:
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |
      | alias3       | large  | big    |

  Scenario: I add a multiple aliases with basic information
    Given following aliases should exist:
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |
      | alias3       | large  | big    |
    When I add alias with following information:
      | alias   | bliu,blu |
      | search  | blue     |
    Then following aliases should exist:
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |
      | alias3       | large  | big    |
      | alias5       | bliu   | blue   |
      | alias6       | blu    | blue   |

  Scenario: I add a multiple aliases with duplicate search field
    Given following aliases should exist:
      | id reference | alias  | search |
      | alias1       | bloose | blouse |
      | alias2       | blues  | blouse |
      | alias3       | large  | big    |
      | alias5       | bliu   | blue   |
      | alias6       | blu    | blue   |
    When I add alias with following information:
      | alias   | bliu,sapphire |
      | search  | blue          |
    Then following aliases should exist:
      | id reference | alias    | search |
      | alias1       | bloose   | blouse |
      | alias2       | blues    | blouse |
      | alias3       | large    | big    |
      | alias5       | bliu     | blue   |
      | alias6       | blu      | blue   |
      | alias7       | sapphire | blue   |

    When I add alias with following information:
      | alias   | blah |
      | search  | blue |
    Then following aliases should exist:
      | id reference | alias    | search |
      | alias1       | bloose   | blouse |
      | alias2       | blues    | blouse |
      | alias3       | large    | big    |
      | alias5       | bliu     | blue   |
      | alias6       | blu      | blue   |
      | alias7       | sapphire | blue   |
      | alias8       | blah     | blue   |

  Scenario: I add alias with empty alias field
    When I add alias with following information:
      | alias   |      |
      | search  | blue |
    Then I should get error that alias cannot be empty
    Then following aliases should exist:
      | id reference | alias    | search |
      | alias1       | bloose   | blouse |
      | alias2       | blues    | blouse |
      | alias3       | large    | big    |
      | alias5       | bliu     | blue   |
      | alias6       | blu      | blue   |
      | alias7       | sapphire | blue   |
      | alias8       | blah     | blue   |

  Scenario: I add alias with empty search field
    When I add alias with following information:
      | alias   | blu |
      | search  |     |
    Then I should get error that search term cannot be empty
    And following aliases should exist:
      | id reference | alias    | search |
      | alias1       | bloose   | blouse |
      | alias2       | blues    | blouse |
      | alias3       | large    | big    |
      | alias5       | bliu     | blue   |
      | alias6       | blu      | blue   |
      | alias7       | sapphire | blue   |
      | alias8       | blah     | blue   |
