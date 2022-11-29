# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-product
@restore-products-before-feature
@update-product
# @todo: this feature could be used to update more properties together, but I think its not worth putting extracting all the existing features into this one,
#         because it will be too hard to manage, there are too many of them
Feature: Update product properties from Back Office (BO) in a single shop context
  As a BO user
  I need to be able to update product properties from BO

  Background:
    Given I disable multishop feature
    And shop "shop1" with name "test_shop" exists
    And language "language1" with locale "en-US" exists

  Scenario: I update product basic information
    Given I add product "product1" with following information:
      | name[en-US] | funny mug |
      | type        | standard  |
    And product "product1" type should be standard
    And product "product1" localized "name" should be:
      | locale | value     |
      | en-US  | funny mug |
    When I update product "product1" basic information with following values:
      | name[en-US]              | photo of funny mug |
      | description[en-US]       | nice mug           |
      | description_short[en-US] | Just a nice mug    |
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
    When I update product "product1" basic information with following values:
      | name[en-US] | #hashtagmug |
    Then I should get error that product name is invalid
    And product "product1" localized "name" should be:
      | locale | value              |
      | en-US  | photo of funny mug |
    When I update product "product1" basic information with following values:
      | description_short[en-US] | <div onmousedown=hack()> |
    Then I should get error that product "description_short" is invalid
    When I update product "product1" basic information with following values:
      | description[en-US] | <script> |
    Then I should get error that product description is invalid
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
    When I update product "product1" basic information with following values:
      | description[en-US] | it's mug & it's nice |
    Then product "product1" localized "description" should be:
      | locale | value                |
      | en-US  | it's mug & it's nice |

  Scenario: When product has empty names in some languages the default language is used to prefill them all
    Given language "fr" with locale "fr-FR" exists
    And I add product "empty_product" with following information:
      | type | standard |
    Then product "empty_product" localized "name" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    When I update product "empty_product" basic information with following values:
      | name[en-US] | english name |
    Then product "empty_product" localized "name" should be:
      | locale | value        |
      | en-US  | english name |
      | fr-FR  | english name |
    When I update product "empty_product" basic information with following values:
      | name[en-US] | english name |
      | name[fr-FR] |              |
    Then product "empty_product" localized "name" should be:
      | locale | value        |
      | en-US  | english name |
      | fr-FR  | english name |

  Scenario: When product name is updated, empty friendly-urls are auto-filled
    Given language "fr" with locale "fr-FR" exists
    And I add product "empty_product2" with following information:
      | type | standard |
    Then product "empty_product2" localized "name" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    And product "empty_product2" localized "link_rewrite" should be:
      | locale | value |
      | en-US  |       |
      | fr-FR  |       |
    When I update product "empty_product2" basic information with following values:
      | name[en-US] | english name |
      | name[fr-FR] | french name  |
    Then product "empty_product2" localized "name" should be:
      | locale | value        |
      | en-US  | english name |
      | fr-FR  | french name  |
    And product "empty_product2" localized "link_rewrite" should be:
      | locale | value        |
      | en-US  | english-name |
      | fr-FR  | french-name  |
    When I update product "empty_product2" basic information with following values:
      | name[en-US] | english name v2 |
      | name[fr-FR] | french name v2  |
    Then product "empty_product2" localized "name" should be:
      | locale | value           |
      | en-US  | english name v2 |
      | fr-FR  | french name v2  |
    And product "empty_product2" localized "link_rewrite" should be:
      | locale | value        |
      | en-US  | english-name |
      | fr-FR  | french-name  |
