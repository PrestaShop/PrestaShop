# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s attribute --tags list-multi-shop-attribute-group
@restore-all-tables-before-feature
@list-multi-shop-attribute-group
Feature: Attribute Group
  PrestaShop allows BO users to list attribute groups when multistore feature is enabled
  As a BO user
  I should be able to list attribute groups in different shops

  Background:
    Given attribute group "size" named "Size" in en language exists
    And attribute group "color" named "Color" in en language exists
    And attribute group "dimension" named "Dimension" in en language exists
    And attribute group "paper_type" named "Paper Type" in en language exists
    And attribute "grey" named "Grey" in en language exists
    And attribute "taupe" named "Taupe" in en language exists
    And attribute "beige" named "Beige" in en language exists
    And attribute "white" named "White" in en language exists
    And attribute "off_white" named "Off White" in en language exists
    And attribute "red" named "Red" in en language exists
    And attribute "black" named "Black" in en language exists
    And attribute "camel" named "Camel" in en language exists
    And attribute "orange" named "Orange" in en language exists
    And attribute "blue" named "Blue" in en language exists
    And attribute "green" named "Green" in en language exists
    And attribute "yellow" named "Yellow" in en language exists
    And attribute "brown" named "Brown" in en language exists
    And attribute "pink" named "Pink" in en language exists
    And attribute "s" named "S" in en language exists
    And attribute "m" named "M" in en language exists
    And attribute "l" named "L" in en language exists
    And attribute "xl" named "XL" in en language exists
    And language with iso code "en" is the default one
    And I enable multishop feature
    And shop "shop1" with name "test_shop" exists
    And shop group "default_shop_group" with name "Default" exists
    And I add a shop "shop2" with name "default_shop_group" and color "red" for the group "default_shop_group"
    And single shop context is loaded

  Scenario: List attribute groups with attributes
    And attribute group "size" is not associated to shops "shop2"
    And attribute group "color" is not associated to shops "shop2"
    And attribute group "dimension" is not associated to shops "shop2"
    And attribute group "paper_type" is not associated to shops "shop2"
    And attribute "grey" is not associated to shops "shop2"
    And attribute "taupe" is not associated to shops "shop2"
    And attribute "beige" is not associated to shops "shop2"
    And attribute "white" is not associated to shops "shop2"
    And attribute "off_white" is not associated to shops "shop2"
    And attribute "red" is not associated to shops "shop2"
    And attribute "black" is not associated to shops "shop2"
    And attribute "camel" is not associated to shops "shop2"
    And attribute "orange" is not associated to shops "shop2"
    And attribute "blue" is not associated to shops "shop2"
    And attribute "green" is not associated to shops "shop2"
    And attribute "yellow" is not associated to shops "shop2"
    And attribute "brown" is not associated to shops "shop2"
    And attribute "pink" is not associated to shops "shop2"
    And attribute "s" is not associated to shops "shop2"
    And attribute "m" is not associated to shops "shop2"
    And attribute "l" is not associated to shops "shop2"
    And attribute "xl" is not associated to shops "shop2"
    And there is a list of following attribute groups for shops "shop1":
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position | reference  |
      | Size        | Size               | false          | select     | 0        | size       |
      | Color       | Color              | true           | color      | 1        | color      |
      | Dimension   | Dimension          | false          | select     | 2        | dimension  |
      | Paper Type  | Paper Type         | false          | select     | 3        | paper_type |
    And there is a list of following attribute groups for all shops:
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position | reference  |
      | Size        | Size               | false          | select     | 0        | size       |
      | Color       | Color              | true           | color      | 1        | color      |
      | Dimension   | Dimension          | false          | select     | 2        | dimension  |
      | Paper Type  | Paper Type         | false          | select     | 3        | paper_type |
    And the attribute group "size" should have the following attributes for shops "shop1":
      | name[en-US] | color | position | reference |
      | S           |       | 0        | s         |
      | M           |       | 1        | m         |
      | L           |       | 2        | l         |
      | XL          |       | 3        | xl        |
    And the attribute group "size" should have the following attributes for all shops:
      | name[en-US] | color | position | reference |
      | S           |       | 0        | s         |
      | M           |       | 1        | m         |
      | L           |       | 2        | l         |
      | XL          |       | 3        | xl        |
    And the attribute group "color" should have the following attributes for shops "shop1":
      | name[en-US] | color   | position | reference |
      | Grey        | #AAB2BD | 0        | grey      |
      | Taupe       | #CFC4A6 | 1        | taupe     |
      | Beige       | #f5f5dc | 2        | beige     |
      | White       | #ffffff | 3        | white     |
      | Off White   | #faebd7 | 4        | off_white |
      | Red         | #E84C3D | 5        | red       |
      | Black       | #434A54 | 6        | black     |
      | Camel       | #C19A6B | 7        | camel     |
      | Orange      | #F39C11 | 8        | orange    |
      | Blue        | #5D9CEC | 9        | blue      |
      | Green       | #A0D468 | 10       | green     |
      | Yellow      | #F1C40F | 11       | yellow    |
      | Brown       | #964B00 | 12       | brown     |
      | Pink        | #FCCACD | 13       | pink      |
    And there should be no attribute groups for shops "shop2"
    When I associate attribute group "size" with shops "shop1,shop2"
    And there is a list of following attribute groups for shops "shop2":
      | name[en-US] | public_name[en-US] | is_color_group | group_type | position | reference  |
      | Size        | Size               | false          | select     | 0        | size       |
    But the attribute group "size" should have no attributes for shops "shop2"
    When I associate attribute "s" with shops "shop1,shop2"
    When I associate attribute "m" with shops "shop1,shop2"
    Then the attribute group "size" should have the following attributes for shops "shop2":
      | name[en-US] | color | position | reference |
      | S           |       | 0        | s         |
      | M           |       | 1        | m         |
