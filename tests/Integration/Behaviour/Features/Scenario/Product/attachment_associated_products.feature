# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags attachment-associated-products
@restore-products-before-feature
@restore-languages-after-feature
@attachment-associated-products
@clear-cache-after-feature
@reset-downloads-after-feature
Feature: List the products an attachment is associated with from Back Office (BO).
  As an employee I want to see which products use a file before I change or delete it.

  Background:
    Given language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    And I add new attachment "att1" with following properties:
      | description[en-US] | puffin photo nr1 |
      | name[en-US]        | puffin           |
      | file_name          | app_icon.png     |
    And I add new attachment "att2" with following properties:
      | name[en-US]        | myShop       |
      | description[en-US] | my shop logo |
      | file_name          | logo.jpg     |
    And I add product "product1" with following information:
      | name[en-US] | mug with photo |
      | name[fr-FR] | tasse photo    |
      | type        | standard       |
    And I add product "product2" with following information:
      | name[en-US] | t-shirt  |
      | name[fr-FR] | tee-shirt |
      | type        | standard |
    And I update product "product1" with following values:
      | reference | MUG-01 |
      | active    | true   |
    And I update product "product2" with following values:
      | reference | TS-02 |

  Scenario: An attachment lists its products with their reference, name and status
    Then attachment "att1" should have no associated products
    When I associate product product1 with following attachments: "[att1]"
    And I associate product product2 with following attachments: "[att1,att2]"
    Then attachment "att1" should be associated with following products:
      | product  | reference | name           | active |
      | product1 | MUG-01    | mug with photo | true   |
      | product2 | TS-02     | t-shirt        | false  |
    And attachment "att2" should be associated with following products:
      | product  | reference | name    | active |
      | product2 | TS-02     | t-shirt | false  |
    When I remove product product1 attachments association
    Then attachment "att1" should be associated with following products:
      | product  | reference | name    | active |
      | product2 | TS-02     | t-shirt | false  |
    # Files can be found by their products' ID, or a part of their reference or name
    When I associate product product1 with following attachments: "[att1]"
    Then searching attachments by product "ts-0" should find:
      | product  | attachment |
      | product2 | att1       |
      | product2 | att2       |
    And searching attachments by product "0" should find:
      | product  | attachment |
      | product1 | att1       |
      | product2 | att1       |
      | product2 | att2       |
    And searching attachments by product "SHIRT" should find:
      | product  | attachment |
      | product2 | att1       |
      | product2 | att2       |
    And searching attachments by the ID of product "product1" should find:
      | product  | attachment |
      | product1 | att1       |
    # Names are searched in the employee's language only
    And searching attachments by product "tasse" should find nothing
    # LIKE wildcards are searched literally
    And searching attachments by product "%" should find nothing
    And searching attachments by product "MUG_01" should find nothing

  Scenario: The products of an attachment are listed page by page and can be searched
    When I add product "product3" with following information:
      | name[en-US] | poster   |
      | type        | standard |
    And I update product "product3" with following values:
      | reference | POS-03 |
    And I associate product product1 with following attachments: "[att1]"
    And I associate product product2 with following attachments: "[att1]"
    And I associate product product3 with following attachments: "[att1]"
    Then listing 2 products from offset 0 of attachment "att1" should give 3 in total and:
      | product  | reference | name           | active |
      | product1 | MUG-01    | mug with photo | true   |
      | product2 | TS-02     | t-shirt        | false  |
    And listing 2 products from offset 2 of attachment "att1" should give 3 in total and:
      | product  | reference | name   | active |
      | product3 | POS-03    | poster | false  |
    # Same search as by product: the ID exactly, or a part of the reference or name
    And the products of attachment "att1" matching "t-sh" should be:
      | product  | reference | name    | active |
      | product2 | TS-02     | t-shirt | false  |
    And the products of attachment "att1" matching "-0" should be:
      | product  | reference | name           | active |
      | product1 | MUG-01    | mug with photo | true   |
      | product2 | TS-02     | t-shirt        | false  |
      | product3 | POS-03    | poster         | false  |
    And no product of attachment "att2" should match "poster"
