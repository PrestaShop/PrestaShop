# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s alias --tags search_configuration
@restore-search-configuration-before-feature
@restore-shops-after-feature
@clear-cache-before-feature
@search_configuration
Feature: Configure the search from Back Office (BO)
  As a BO user
  I need to be able to configure the indexation, the search options and the weights of the search

  Scenario: Save the indexation setting
    When I save the search "indexation" settings in all shops context with the following values:
      | indexing | false |
    Then the search configuration "PS_SEARCH_INDEXATION" should be disabled in all shops context
    When I save the search "indexation" settings in all shops context with the following values:
      | indexing | true |
    Then the search configuration "PS_SEARCH_INDEXATION" should be enabled in all shops context

  Scenario: Save the search options
    When I save the search "search options" settings in all shops context with the following values:
      | search_within_word       | true        |
      | search_exact_end_match   | true        |
      | fuzzy_search             | false       |
      | fuzzy_max_words          | 6           |
      | fuzzy_max_difference     | 4           |
      | max_word_length          | 20          |
      | min_word_length          | 2           |
      | blacklisted_words[en-US] | the\|of\|at |
    Then the search configuration "PS_SEARCH_START" should be enabled in all shops context
    And the search configuration "PS_SEARCH_END" should be enabled in all shops context
    And the search configuration "PS_SEARCH_FUZZY" should be disabled in all shops context
    And the search configuration "PS_SEARCH_FUZZY_MAX_LOOP" should be "6" in all shops context
    And the search configuration "PS_SEARCH_FUZZY_MAX_DIFFERENCE" should be "4" in all shops context
    And the search configuration "PS_SEARCH_MAX_WORD_LENGTH" should be "20" in all shops context
    And the search configuration "PS_SEARCH_MINWORDLEN" should be "2" in all shops context
    And the search blacklist in language "en-US" should be "the|of|at" in all shops context

  Scenario: Empty optional numbers of the search options are saved as zero
    When I save the search "search options" settings in all shops context with the following values:
      | fuzzy_max_words      |  |
      | fuzzy_max_difference |  |
      | min_word_length      |  |
    Then the search configuration "PS_SEARCH_FUZZY_MAX_LOOP" should be "0" in all shops context
    And the search configuration "PS_SEARCH_FUZZY_MAX_DIFFERENCE" should be "0" in all shops context
    And the search configuration "PS_SEARCH_MINWORDLEN" should be "0" in all shops context

  Scenario: Invalid search options are rejected
    Then saving the search "search options" settings in all shops context with the following values should be rejected because of "max_word_length":
      | max_word_length | -1 |
    And saving the search "search options" settings in all shops context with the following values should be rejected because of "1":
      | blacklisted_words[en-US] | the\|<script> |

  Scenario: Save the weights
    When I save the search "weight" settings in all shops context with the following values:
      | product_name_weight      | 9 |
      | reference_weight         | 8 |
      | short_description_weight | 7 |
      | description_weight       | 6 |
      | category_weight          | 5 |
      | brand_weight             | 4 |
      | tags_weight              | 3 |
      | attributes_weight        | 2 |
      | features_weight          | 1 |
    Then the search configuration "PS_SEARCH_WEIGHT_PNAME" should be "9" in all shops context
    And the search configuration "PS_SEARCH_WEIGHT_REF" should be "8" in all shops context
    And the search configuration "PS_SEARCH_WEIGHT_SHORTDESC" should be "7" in all shops context
    And the search configuration "PS_SEARCH_WEIGHT_DESC" should be "6" in all shops context
    And the search configuration "PS_SEARCH_WEIGHT_CNAME" should be "5" in all shops context
    And the search configuration "PS_SEARCH_WEIGHT_MNAME" should be "4" in all shops context
    And the search configuration "PS_SEARCH_WEIGHT_TAG" should be "3" in all shops context
    And the search configuration "PS_SEARCH_WEIGHT_ATTRIBUTE" should be "2" in all shops context
    And the search configuration "PS_SEARCH_WEIGHT_FEATURE" should be "1" in all shops context

  Scenario: Invalid weights are rejected
    Then saving the search "weight" settings in all shops context with the following values should be rejected because of "product_name_weight":
      | product_name_weight | -3 |

  Scenario: A setting overridden for a shop does not change the other shops
    Given I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "test_shop2" and color "blue" for the group "default_shop_group"
    And I save the search "weight" settings in all shops context with the following values:
      | product_name_weight | 6 |
      | reference_weight    | 10 |
    When I save the search "weight" settings in shop "shop2" context overriding the following values:
      | product_name_weight | 12 |
    Then the search configuration "PS_SEARCH_WEIGHT_PNAME" should be "12" for shop "shop2"
    And shop "shop2" should override the search configuration "PS_SEARCH_WEIGHT_PNAME"
    And the search configuration "PS_SEARCH_WEIGHT_PNAME" should be "6" for shop "shop1"
    And the search configuration "PS_SEARCH_WEIGHT_PNAME" should be "6" in all shops context
    # The fields which are not overridden keep following the all shops value
    And shop "shop2" should not override the search configuration "PS_SEARCH_WEIGHT_REF"
    And the search configuration "PS_SEARCH_WEIGHT_REF" should be "10" for shop "shop2"

  Scenario: Saving for a shop without overriding keeps the all shops value and removes the override
    Given shop "shop2" should override the search configuration "PS_SEARCH_WEIGHT_PNAME"
    When I save the search "weight" settings in shop "shop2" context without overriding the following values:
      | product_name_weight | 15 |
    Then shop "shop2" should not override the search configuration "PS_SEARCH_WEIGHT_PNAME"
    And the search configuration "PS_SEARCH_WEIGHT_PNAME" should be "6" for shop "shop2"
    And the search configuration "PS_SEARCH_WEIGHT_PNAME" should be "6" in all shops context

  Scenario: Saving for all shops keeps the settings overridden for a shop
    Given I save the search "search options" settings in shop "shop2" context overriding the following values:
      | fuzzy_search    | false |
      | min_word_length | 5     |
    When I save the search "search options" settings in all shops context with the following values:
      | fuzzy_search    | true |
      | min_word_length | 3    |
    Then the search configuration "PS_SEARCH_FUZZY" should be enabled in all shops context
    And the search configuration "PS_SEARCH_FUZZY" should be enabled for shop "shop1"
    And the search configuration "PS_SEARCH_FUZZY" should be disabled for shop "shop2"
    And the search configuration "PS_SEARCH_MINWORDLEN" should be "3" for shop "shop1"
    And the search configuration "PS_SEARCH_MINWORDLEN" should be "5" for shop "shop2"

  Scenario: The indexation setting can be overridden for a shop
    Given I save the search "indexation" settings in all shops context with the following values:
      | indexing | true |
    When I save the search "indexation" settings in shop "shop2" context overriding the following values:
      | indexing | false |
    Then the search configuration "PS_SEARCH_INDEXATION" should be disabled for shop "shop2"
    And the search configuration "PS_SEARCH_INDEXATION" should be enabled for shop "shop1"
