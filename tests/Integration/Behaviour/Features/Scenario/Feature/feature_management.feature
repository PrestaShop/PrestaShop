# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s feature --tags feature-management
@restore-all-tables-before-feature
@feature-management
Feature: Product feature management
  PrestaShop allows BO users to manage product features
  As a BO user
  I must be able to create, edit and delete product features

  Background:
    Given shop "shop1" with name "test_shop" exists
    And single shop "shop1" context is loaded
    And language "en" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists

  Scenario: I should not be able to create feature without providing associated shops
    When I create product feature "feature1" with specified properties:
      | name[en-US] | My feature en |
      | name[fr-FR] | My feature fr |
    Then I should get an error that feature shop association is invalid

  Scenario: I should not be able to update feature when providing empty shop association
    Given I create product feature "feature1" with specified properties:
      | name[en-US]      | My feature en |
      | name[fr-FR]      | My feature fr |
      | associated shops | shop1         |
    # when empty shop ids provided, it should throw exception
    When I update product feature feature1 with following details:
      | associated shops |  |
    Then I should get an error that feature shop association is invalid
    # but when shop ids are not provided at all, they should stay unchaged
    When I update product feature feature1 with following details:
      | name[en-US] | My feature en updated |
      | name[fr-FR] | My feature fr updated |
    Then product feature feature1 should have following details:
      | name[en-US]      | My feature en updated |
      | name[fr-FR]      | My feature fr updated |
      | associated shops | shop1                 |

  Scenario: Create product feature in single shop
    When I create product feature "feature1" with specified properties:
      | name[en-US]      | My feature en |
      | name[fr-FR]      | My feature fr |
      | associated shops | shop1         |
    Then product feature feature1 should have following details:
      | name[en-US]      | My feature en |
      | name[fr-FR]      | My feature fr |
      | associated shops | shop1         |

  Scenario: Update product feature
    Given I create product feature "feature2" with specified properties:
      | name[en-US]      | My feature en |
      | name[fr-FR]      | My feature fr |
      | associated shops | shop1         |
    And product feature feature2 should have following details:
      | name[en-US]      | My feature en |
      | name[fr-FR]      | My feature fr |
      | associated shops | shop1         |
    When I update product feature feature2 with following details:
      | name[en-US] | My feature en updated1 |
      | name[fr-FR] | My feature fr updated1 |
    Then product feature feature2 should have following details:
      | name[en-US] | My feature en updated1 |
      | name[fr-FR] | My feature fr updated1 |

  Scenario: Creating and updating product feature with empty name in default language should not be allowed
    When I create product feature "feature4" with specified properties:
      | name[en-US]      |               |
      | name[fr-FR]      | My feature fr |
      | associated shops | shop1         |
    Then I should get an error that feature name is invalid
    Given I create product feature "feature4" with specified properties:
      | name[en-US]      | My feature en |
      | name[fr-FR]      | My feature fr |
      | associated shops | shop1         |
    And product feature feature4 should have following details:
      | name[en-US] | My feature en |
      | name[fr-FR] | My feature fr |
    When I update product feature feature4 with following details:
      | name[en-US] |               |
      | name[fr-FR] | My feature fr |
    Then I should get an error that feature name is invalid
    And product feature feature4 should have following details:
      | name[en-US] | My feature en |
      | name[fr-FR] | My feature fr |

  Scenario: Delete feature which has no values
    Given I create product feature feature6 with specified properties:
      | name[en-US]      | My feature en |
      | name[fr-FR]      |               |
      | associated shops | shop1         |
    # this creation also checks that non-default lang name is filled with default one when its empty
    And product feature feature6 should have following details:
      | name[en-US] | My feature en |
      | name[fr-FR] | My feature en |
    When I delete product feature feature6
    Then product feature feature6 should not exist

  Scenario: Deleting feature should also remove related feature values
    Given I create product feature feature7 with specified properties:
      | name[en-US]      | My feature 7 |
      | associated shops | shop1        |
    And product feature feature7 should exist
    And I create feature value "featureValue7" for feature "feature7" with following properties:
      | value[en-US]     | Earth |
      | associated shops | shop1 |
    And feature value featureValue7 should exist
    And I create feature value "featureValue72" for feature "feature7" with following properties:
      | value[en-US]     | value2 |
      | associated shops | shop1  |
    And feature value featureValue72 should exist
    When I delete product feature feature7
    Then product feature feature7 should not exist
    And feature value featureValue7 should not exist
    And feature value featureValue72 should not exist

  Scenario: Bulk delete features should also remove related feature values
    Given I create product feature feature8 with specified properties:
      | name[en-US]      | My feature 8 |
      | associated shops | shop1        |
    And I create product feature feature9 with specified properties:
      | name[en-US]      | My feature 9 |
      | associated shops | shop1        |
    And I create product feature feature10 with specified properties:
      | name[en-US]      | My feature 10 |
      | associated shops | shop1         |
    And I create feature value "featureValue8" for feature "feature8" with following properties:
      | value[en-US] | value8 |
    And I create feature value "featureValue9" for feature "feature9" with following properties:
      | value[en-US] | value9 |
    And product feature feature8 should exist
    And feature value featureValue8 should exist
    And product feature feature9 should exist
    And feature value featureValue9 should exist
    And product feature feature10 should exist
    When I bulk delete product features "feature9,feature10"
    Then product feature feature9 should not exist
    And feature value featureValue9 should not exist
    And product feature feature10 should not exist
    But product feature feature8 should exist
    And feature value featureValue8 should exist
