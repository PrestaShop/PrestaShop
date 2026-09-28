# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s import --tags import-product-associations
@restore-all-tables-before-feature
@reset-all-tables-before-scenario
@reboot-kernel-before-scenario
@clear-cache-before-scenario
@import-product-associations
Feature: Import products that reference each other
  In order to import a catalogue whose rows point at one another
  As a BO user
  I should get the links resolved once every row exists

  # Accessories are the reason the engine has a phase after the database one: a row may point at
  # a product a later row creates, so the links cannot be made while the rows are being written.
  Scenario: Products referencing each other are linked once every row has been imported
    When I start an import job "job1" for entity type "product" from file "product_accessories_mutual.csv"
    And I continue the import job "job1" until it stops
    Then import job "job1" should have the following properties:
      | status          | finished |
      | dataRecordCount | 3        |
      | progressPercent | 100      |

    Given there is a product "accA" with reference "ACC-A"
    And there is a product "accB" with reference "ACC-B"
    And there is a product "accC" with reference "ACC-C"
    # A points at B and B points at A, each resolved after both existed
    Then product "accA" should have following related products:
      | product | name             | reference | image url                                             |
      | accB    | Accessory Prod B | ACC-B     | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    And product "accB" should have following related products:
      | product | name             | reference | image url                                             |
      | accA    | Accessory Prod A | ACC-A     | http://myshop.com/img/p/{no_picture}-home_default.jpg |
    # C points at a reference no row ever created: the product is still imported, the link is not
    And product "accC" should have no related products
