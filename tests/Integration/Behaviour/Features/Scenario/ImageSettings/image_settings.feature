# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s image-settings --tags image-settings
@restore-all-tables-before-feature
@image-settings
Feature: Image Settings
  PrestaShop allows BO users to manage images types and settings for regeneration
  As a BO user
  I must be able to create, save, edit image types and edit image settings.

  # Scenarios for image types
  Scenario: Create an image type
    When I create an image type "test-default" with following properties:
      | width         | 123  |
      | height        | 456  |
      | products      | true |
      | categories    | true |
      | manufacturers | true |
      | suppliers     | true |
      | stores        | true |
    Then image type "test-default" should have the following properties:
      | width         | 123  |
      | height        | 456  |
      | products      | true |
      | categories    | true |
      | manufacturers | true |
      | suppliers     | true |
      | stores        | true |

  Scenario: Edit an image type
    When I edit image type "test-default" with following properties:
      | width         | 456  |
      | height        | 789  |
      | products      | false |
      | categories    | false |
      | manufacturers | false |
      | suppliers     | false |
      | stores        | false |
    Then image type "test-default" should have the following properties:
      | width         | 456   |
      | height        | 789   |
      | products      | false |
      | categories    | false |
      | manufacturers | false |
      | suppliers     | false |
      | stores        | false |

  Scenario: Partial edit an image type
    When I edit image type "test-default" with following properties:
      | width         | 999 |
    Then image type "test-default" should have the following properties:
      | width         | 999   |
      | height        | 789   |
      | products      | false |
      | categories    | false |
      | manufacturers | false |
      | suppliers     | false |
      | stores        | false |
    When I edit image type "test-default" with following properties:
      | height        | 123 |
    Then image type "test-default" should have the following properties:
      | width         | 999   |
      | height        | 123   |
      | products      | false |
      | categories    | false |
      | manufacturers | false |
      | suppliers     | false |
      | stores        | false |
    When I edit image type "test-default" with following properties:
      | products      | true |
    Then image type "test-default" should have the following properties:
      | width         | 999   |
      | height        | 123   |
      | products      | true  |
      | categories    | false |
      | manufacturers | false |
      | suppliers     | false |
      | stores        | false |

  Scenario: Delete an image type
    When I delete image type "test-default".
    Then image type "test-default" should not exist.

  Scenario: Bulk delete some image types
    When I create an image type "test1-default" with following properties:
      | width         | 123  |
      | height        | 456  |
      | products      | true |
      | categories    | true |
      | manufacturers | true |
      | suppliers     | true |
      | stores        | true |
    Then image type "test1-default" should have the following properties:
      | width         | 123  |
      | height        | 456  |
      | products      | true |
      | categories    | true |
      | manufacturers | true |
      | suppliers     | true |
      | stores        | true |
    Then I create an image type "test2-default" with following properties:
      | width         | 456  |
      | height        | 123  |
      | products      | true |
      | categories    | true |
      | manufacturers | true |
      | suppliers     | true |
      | stores        | true |
    Then image type "test2-default" should have the following properties:
      | width         | 456  |
      | height        | 123  |
      | products      | true |
      | categories    | true |
      | manufacturers | true |
      | suppliers     | true |
      | stores        | true |
    Then I bulk delete image types "test1-default,test2-default".
    Then image type "test1-default" should not exist.
    Then image type "test2-default" should not exist.

  # Scenarios for image settings
  Scenario: Edit images settings for regeneration
    When I edit images settings with following properties:
      | formats            | jpg,avif,webp |
      | base-format        | jpg           |
      | avif-quality       | 50            |
      | jpeg-quality       | 60            |
      | png-quality        | 5             |
      | webp-quality       | 70            |
      | generation-method  | 1             |
      | picture-max-size   | 5000          |
      | picture-max-width  | 123           |
      | picture-max-height | 345           |
    Then images settings should have the following properties:
      | formats            | jpg,avif,webp |
      | base-format        | jpg           |
      | avif-quality       | 50            |
      | jpeg-quality       | 60            |
      | png-quality        | 5             |
      | webp-quality       | 70            |
      | generation-method  | 1             |
      | picture-max-size   | 5000          |
      | picture-max-width  | 123           |
      | picture-max-height | 345           |