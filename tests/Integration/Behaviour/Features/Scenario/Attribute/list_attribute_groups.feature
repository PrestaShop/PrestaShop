# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attribute --tags list-attribute-group
@reset-database-before-feature
@list-attribute-group
Feature: Attribute Group
  PrestaShop allows BO users to list attribute groups
  As a BO user
  I should be able to list attribute groups

  # NOTE: we don't initialize specific data since we already have the core features
  Scenario: List attribute groups
    When I list all attribute groups I should get following results:
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position |
      | Size        | Size               | false          | select     | 0        |
      | Color       | Color              | true           | color      | 1        |
      | Dimension   | Dimension          | false          | select     | 2        |
      | Paper Type  | Paper Type         | false          | select     | 3        |
