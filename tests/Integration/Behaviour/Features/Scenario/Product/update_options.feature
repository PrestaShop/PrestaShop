# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-options
@reset-database-before-feature
@update-options
Feature: Update product options from Back Office (BO)
  As a BO user
  I need to be able to update product options from BO

  Scenario: I update product options
    Given I add product "product1" with following information:
      | name       | en-US:Presta camera |
      | is_virtual | false               |
    Then product "product1" should have following values:
      | visibility           | both               |
      | available_for_order  | true               |
      | online_only          | false              |
      | show_price           | true               |
      | condition            | new                |
      | isbn                 |                    |
      | upc                  |                    |
      | ean13                |                    |
      | mpn                  |                    |
      | reference            |                    |
    And product "product1" localized "tags" should be "en-US:"
    When I update product "product1" options with following values:
      | visibility           | catalog            |
      | available_for_order  | false              |
      | online_only          | true               |
      | show_price           | false              |
      | condition            | used               |
      | isbn                 | 978-3-16-148410-0  |
      | upc                  | 72527273070        |
      | ean13                | 978020137962       |
      | mpn                  | mpn1               |
      | reference            | ref1               |
    Then product "product1" should have following values:
      | visibility           | catalog            |
      | available_for_order  | false              |
      | online_only          | true               |
      | show_price           | false              |
      | condition            | used               |
      | isbn                 | 978-3-16-148410-0  |
      | upc                  | 72527273070        |
      | ean13                | 978020137962       |
      | mpn                  | mpn1               |
      | reference            | ref1               |

  Scenario: I only update product availability for order, leaving other properties unchanged
    Given product "product1" should have following values:
      | visibility           | catalog            |
      | available_for_order  | false              |
      | online_only          | true               |
      | show_price           | false              |
      | condition            | used               |
      | isbn                 | 978-3-16-148410-0  |
      | upc                  | 72527273070        |
      | ean13                | 978020137962       |
      | mpn                  | mpn1               |
      | reference            | ref1               |
    When I update product "product1" options with following values:
      | available_for_order  | true               |
    Then product "product1" should have following values:
      | visibility           | catalog            |
      | available_for_order  | true               |
      | online_only          | true               |
      | show_price           | false              |
      | condition            | used               |
      | isbn                 | 978-3-16-148410-0  |
      | upc                  | 72527273070        |
      | ean13                | 978020137962       |
      | mpn                  | mpn1               |
      | reference            | ref1               |

  Scenario: I update product options providing invalid values
    Given I add product "product2" with following information:
      | name       | en-US:'The truth is out there' wallpaper |
      | is_virtual | true                                     |
    When I update product "product2" options with following values:
      | visibility | show it to me plz  |
    Then I should get error that product visibility is invalid
    When I update product "product2" options with following values:
      | condition | very good condition |
    Then I should get error that product condition is invalid
    When I update product "product2" options with following values:
      | isbn  | isbn1                   |
    Then I should get error that product isbn is invalid
    When I update product "product2" options with following values:
      | upc   | upc1                    |
    Then I should get error that product upc is invalid
    When I update product "product2" options with following values:
      | ean13 | ean1                    |
    Then I should get error that product ean13 is invalid
    When I update product "product2" options with following values:
      | mpn   | this is more than forty characters long string |
    Then I should get error that product mpn is invalid
    When I update product "product2" options with following values:
      | reference   | invalid chars like ^;{ |
    Then I should get error that product reference is invalid

    Scenario: I update product tags in multiple languages
      Given language "language1" with locale "en-US" exists
      And language with iso code "en" is the default one
      And language "language2" with locale "fr-FR" exists
      And I add product "product3" with following information:
        | name       | en-US:Mechanical watch;fr-FR:montre mécanique |
        | is_virtual | false                                         |
      And product "product3" should have following values:
        | name       | en-US:Mechanical watch;fr-FR:montre mécanique |
      And product "product3" localized "tags" should be "en-US:;fr-FR:"
      When I update product "product3" options with following values:
        | tags       | en-US:mechanic,watch;fr-FR:montre,mécanique |
      And product "product3" localized "tags" should be "en-US:mechanic,watch;fr-FR:montre,mécanique"
      When I update product "product3" options with following values:
        | tags       | en-US:mechanic,watch;fr-FR: |
      And product "product3" localized "tags" should be "en-US:mechanic,watch;fr-FR:"
