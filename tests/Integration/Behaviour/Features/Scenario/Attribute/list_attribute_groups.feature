# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attribute --tags list-attribute-group
@reset-database-before-feature
@list-attribute-group
Feature: Attribute Group
  PrestaShop allows BO users to list attribute groups
  As a BO user
  I should be able to list attribute groups

  Background:
    Given language with iso code "en" is the default one
    And attribute group "size" named "Size" in en language exists
    And attribute group "color" named "Color" in en language exists
    And attribute group "dimension" named "Dimension" in en language exists
    And attribute group "paper_type" named "Paper Type" in en language exists
    And attribute "s" named "S" in en language exists
    And attribute "m" named "M" in en language exists
    And attribute "l" named "L" in en language exists
    And attribute "xl" named "XL" in en language exists
    And attribute "ruled" named "Ruled" in en language exists
    And attribute "plain" named "Plain" in en language exists
    And attribute "squarred" named "Squarred" in en language exists
    And attribute "doted" named "Doted" in en language exists

  # NOTE: we don't initialize specific data since we already have the core features
  Scenario: List attribute groups
    When I list all attribute groups I should get following results:
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position | reference  |
      | Size        | Size               | false          | select     | 0        | size       |
      | Color       | Color              | true           | color      | 1        | color      |
      | Dimension   | Dimension          | false          | select     | 2        | dimension  |
      | Paper Type  | Paper Type         | false          | select     | 3        | paper_type |

  Scenario: List attribute groups with attributes
    When I list all attribute groups, the group "size" should have the following attributes:
      | name[en-US] | color | position | reference |
      | S           |       | 0        | s         |
      | M           |       | 1        | m         |
      | L           |       | 2        | l         |
      | XL          |       | 3        | xl        |

  Scenario: List attribute groups with attributes
    When I list all attribute groups, the group "paper_type" should have the following attributes:
      | name[en-US] | color | position | reference |
      | Ruled       |       | 0        | ruled     |
      | Plain       |       | 1        | plain     |
      | Squarred    |       | 2        | squarred  |
      | Doted       |       | 3        | doted     |
