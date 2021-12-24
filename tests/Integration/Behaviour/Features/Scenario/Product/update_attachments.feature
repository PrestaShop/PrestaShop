# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s product --tags update-attachments
@restore-products-before-feature
@restore-languages-after-feature
@update-attachments
@clear-cache-after-feature
@reset-downloads-after-feature
Feature: Update product attachments from Back Office (BO).
  As an employee I want to be able to assign/remove existing attachments to product and add new ones.

  Scenario: I set new association of product attachments
    Given language "language1" with locale "en-US" exists
    And language with iso code "en" is the default one
    And language "language2" with locale "fr-FR" exists
    When I add product "product1" with following information:
      | name[en-US] | mug with photo |
      | type        | standard       |
    Then product "product1" should be disabled
    And product "product1" type should be standard
    Given I add new attachment "att1" with following properties:
      | description[en-US] | puffin photo nr1 |
      | name[en-US]        | puffin           |
      | name[fr-FR]        | frpuffin         |
      | file_name          | app_icon.png     |
    And attachment "att1" should have following properties:
      | description[en-US] | puffin photo nr1 |
      | description[fr-FR] |                  |
      | name[en-US]        | puffin           |
      | name[fr-FR]        | frpuffin         |
      | file_name          | app_icon.png     |
      | mime               | image/png        |
      | size               | 19187            |
    And I add new attachment "att2" with following properties:
      | name[en-US]        | myShop       |
      | description[en-US] | my shop logo |
      | file_name          | logo.jpg     |
    And attachment "att2" should have following properties:
      | name[en-US]        | myShop       |
      | name[fr-FR]        | myShop       |
      | description[en-US] | my shop logo |
      | description[fr-FR] |              |
      | mime               | image/jpeg   |
      | file_name          | logo.jpg     |
      | size               | 2758         |
    And product product1 should have no attachments associated
    When I associate product product1 with following attachments: "[att1]"
    Then product product1 should have following attachments associated:
      | attachment reference | title                       | description                   | file name    | type      | size  |
      | att1                 | en-US:puffin;fr-Fr:frpuffin | en-US:puffin photo nr1;fr-Fr: | app_icon.png | image/png | 19187 |
    When I associate product product1 with following attachments: "[att2]"
    Then product product1 should have following attachments associated:
      | attachment reference | title                     | description               | file name    | type       | size |
      | att2                 | en-US:myShop;fr-Fr:myShop | en-US:my shop logo;fr-Fr: | logo.jpg     | image/jpeg | 2758 |
    When I associate product product1 with following attachments: "[att1,att2]"
    Then product product1 should have following attachments associated:
      | attachment reference | title                       | description                   | file name    | type       | size  |
      | att1                 | en-US:puffin;fr-Fr:frpuffin | en-US:puffin photo nr1;fr-Fr: | app_icon.png | image/png  | 19187 |
      | att2                 | en-US:myShop;fr-Fr:myShop   | en-US:my shop logo;fr-Fr:     | logo.jpg     | image/jpeg | 2758 |

  Scenario: Remove association of product attachments
    Given product product1 should have following attachments associated:
      | attachment reference | title                       | description                   | file name    | type       | size  |
      | att1                 | en-US:puffin;fr-Fr:frpuffin | en-US:puffin photo nr1;fr-Fr: | app_icon.png | image/png  | 19187 |
      | att2                 | en-US:myShop;fr-Fr:myShop   | en-US:my shop logo;fr-Fr:     | logo.jpg     | image/jpeg | 2758  |
    When I remove product product1 attachments association
    Then product product1 should have no attachments associated
