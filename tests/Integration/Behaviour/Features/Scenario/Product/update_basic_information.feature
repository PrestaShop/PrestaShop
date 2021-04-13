# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-basic-information
@reset-database-before-feature
@update-basic-information
Feature: Update product basic information from Back Office (BO)
  As a BO user
  I need to be able to update product basic information from BO

  Scenario: I update product basic information
    Given I add product "product1" with following information:
      | name[en-US] | funny mug |
      | type        | standard  |
    And product "product1" type should be standard
    And product "product1" localized "name" should be:
      | locale     | value     |
      | en-US      | funny mug |
    When I update product "product1" basic information with following values:
      | name[en-US]              | photo of funny mug |
      | description[en-US]       | nice mug           |
      | description_short[en-US] | Just a nice mug    |
    Then product "product1" type should be standard
    And product "product1" localized "name" should be:
      | locale     | value              |
      | en-US      | photo of funny mug |
    And product "product1" localized "description" should be:
      | locale | value    |
      | en-US  | nice mug |
    And product "product1" localized "description_short" should be:
      | locale | value           |
      | en-US  | Just a nice mug |

  Scenario: I update product basic information providing invalid product name
    Given product "product1" localized "name" is:
      | locale | value              |
      | en-US  | photo of funny mug |
    When I update product "product1" basic information with following values:
      | name[en-US] | #hashtagmug |
    Then I should get error that product name is invalid
    And product "product1" localized "name" should be:
      | locale | value              |
      | en-US  | photo of funny mug |

  Scenario: Partially update product basic information
    Given product "product1" localized "name" is:
      | locale | value              |
      | en-US  | photo of funny mug |
    And product "product1" localized "description" is:
      | locale | value    |
      | en-US  | nice mug |
    And product "product1" localized "description_short" is:
      | locale | value           |
      | en-US  | Just a nice mug |
    When I update product "product1" basic information with following values:
      | is_virtual | false |
    Then product "product1" type should be standard
    And product "product1" localized "name" should be:
      | locale | value              |
      | en-US  | photo of funny mug |
    And product "product1" localized "description" should be:
      | locale | value    |
      | en-US  | nice mug |
    And product "product1" localized "description_short" should be:
      | locale | value           |
      | en-US  | Just a nice mug |

  Scenario: Update product basic information providing invalid characters in description
    Given product "product1" localized "description" is:
      | locale | value    |
      | en-US  | nice mug |
    And product "product1" localized "description_short" is:
      | locale | value           |
      | en-US  | Just a nice mug |
    When I update product "product1" basic information with following values:
      | description[en-US] | <script> |
    Then I should get error that product description is invalid
    And product "product1" localized "description" should be:
      | locale | value    |
      | en-US  | nice mug |
    When I update product "product1" basic information with following values:
      | description_short[en-US] | <div onmousedown=hack()> |
    Then I should get error that product "description_short" is invalid
    And product "product1" localized "description_short" should be:
      | locale | value           |
      | en-US  | Just a nice mug |

  Scenario: Update product basic information providing allowed symbols in description
    Given product "product1" localized "description" is:
      | locale | value    |
      | en-US  | nice mug |
    When I update product "product1" basic information with following values:
      | description[en-US] | it's mug & it's nice |
    Then product "product1" localized "description" should be:
      | locale | value                |
      | en-US  | it's mug & it's nice |
