#./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s quick_access
@restore-all-tables-before-feature
Feature: Quick access management
  As an admin
  I want to manage quick access links
  So that I can configure the back-office quick access toolbar

  Background:
    Given language "english" with locale "en-US" exists
    And language "french" with locale "fr-FR" exists

  Scenario: Add a quick access with required fields
    When I add a quick access "qa_orders" with the following properties:
      | localizedNames[en-US] | Orders                     |
      | localizedNames[fr-FR] | Commandes                  |
      | link                  | index.php?controller=orders |
      | new_window            | true                        |
    Then quick access "qa_orders" should have the following properties:
      | localizedNames[en-US] | Orders                     |
      | localizedNames[fr-FR] | Commandes                  |
      | link                  | index.php?controller=orders |
      | new_window            | true                        |

  Scenario: Add a quick access with new_window set to false
    When I add a quick access "qa_catalog" with the following properties:
      | localizedNames[en-US] | Catalog                      |
      | localizedNames[fr-FR] | Catalogue                    |
      | link                  | index.php?controller=catalog |
      | new_window            | false                        |
    Then quick access "qa_catalog" should have the following properties:
      | localizedNames[en-US] | Catalog                      |
      | localizedNames[fr-FR] | Catalogue                    |
      | link                  | index.php?controller=catalog |
      | new_window            | false                        |

  Scenario: Add a quick access with only the default language name fills the other languages
    When I add a quick access "qa_autofill" with the following properties:
      | localizedNames[en-US] | Dashboard                      |
      | link                  | index.php?controller=dashboard |
      | new_window            | false                          |
    Then quick access "qa_autofill" should have the following properties:
      | localizedNames[en-US] | Dashboard                      |
      | localizedNames[fr-FR] | Dashboard                      |
      | link                  | index.php?controller=dashboard |

  Scenario: Editing only one language keeps the other languages untouched
    Given I add a quick access "qa_refill" with the following properties:
      | localizedNames[en-US] | Before                      |
      | localizedNames[fr-FR] | Untouched                   |
      | link                  | index.php?controller=refill |
      | new_window            | false                       |
    When I edit quick access "qa_refill" with the following properties:
      | localizedNames[en-US] | After |
    Then quick access "qa_refill" should have the following properties:
      | localizedNames[en-US] | After     |
      | localizedNames[fr-FR] | Untouched |

  Scenario: Edit a quick access - change name
    Given I add a quick access "qa_edit" with the following properties:
      | localizedNames[en-US] | Original name              |
      | localizedNames[fr-FR] | Nom original               |
      | link                  | index.php?controller=edit  |
      | new_window            | false                      |
    When I edit quick access "qa_edit" with the following properties:
      | localizedNames[en-US] | Updated name    |
      | localizedNames[fr-FR] | Nom mis à jour  |
    Then quick access "qa_edit" should have the following properties:
      | localizedNames[en-US] | Updated name              |
      | localizedNames[fr-FR] | Nom mis à jour            |
      | link                  | index.php?controller=edit |
      | new_window            | false                     |

  Scenario: Edit a quick access - change link
    When I edit quick access "qa_edit" with the following properties:
      | link | index.php?controller=edited-link |
    Then quick access "qa_edit" should have the following properties:
      | link       | index.php?controller=edited-link |
      | new_window | false                            |

  Scenario: Edit a quick access - change new_window
    When I edit quick access "qa_edit" with the following properties:
      | new_window | true |
    Then quick access "qa_edit" should have the following properties:
      | link       | index.php?controller=edited-link |
      | new_window | true                             |

  Scenario: Toggle new_window flag
    Given I add a quick access "qa_toggle" with the following properties:
      | localizedNames[en-US] | Toggle test                  |
      | localizedNames[fr-FR] | Test de bascule              |
      | link                  | index.php?controller=toggle  |
      | new_window            | false                        |
    When I toggle the new_window flag for quick access "qa_toggle"
    Then quick access "qa_toggle" should have the following properties:
      | new_window | true |
    When I toggle the new_window flag for quick access "qa_toggle"
    Then quick access "qa_toggle" should have the following properties:
      | new_window | false |

  Scenario: Delete a quick access
    Given I add a quick access "qa_delete" with the following properties:
      | localizedNames[en-US] | Delete me                    |
      | localizedNames[fr-FR] | Supprimer moi                |
      | link                  | index.php?controller=delete  |
      | new_window            | false                        |
    When I delete quick access "qa_delete"
    Then quick access "qa_delete" should be deleted

  Scenario: Bulk delete quick accesses
    Given I add a quick access "qa_bulk1" with the following properties:
      | localizedNames[en-US] | Bulk One                     |
      | localizedNames[fr-FR] | Lot Un                       |
      | link                  | index.php?controller=bulk1   |
      | new_window            | false                        |
    And I add a quick access "qa_bulk2" with the following properties:
      | localizedNames[en-US] | Bulk Two                     |
      | localizedNames[fr-FR] | Lot Deux                     |
      | link                  | index.php?controller=bulk2   |
      | new_window            | false                        |
    And I add a quick access "qa_bulk3" with the following properties:
      | localizedNames[en-US] | Bulk Three                   |
      | localizedNames[fr-FR] | Lot Trois                    |
      | link                  | index.php?controller=bulk3   |
      | new_window            | false                        |
    When I bulk delete quick accesses "qa_bulk1,qa_bulk2"
    Then quick accesses "qa_bulk1,qa_bulk2" should be deleted
    And quick access "qa_bulk3" should have the following properties:
      | link | index.php?controller=bulk3 |

  Scenario: Cannot add quick access with duplicate link
    Given I add a quick access "qa_dup" with the following properties:
      | localizedNames[en-US] | Duplicate test               |
      | localizedNames[fr-FR] | Test dupliqué                |
      | link                  | index.php?controller=dup     |
      | new_window            | false                        |
    When I add a quick access "qa_dup2" with the following properties:
      | localizedNames[en-US] | Duplicate test 2             |
      | localizedNames[fr-FR] | Test dupliqué 2              |
      | link                  | index.php?controller=dup     |
      | new_window            | false                        |
    Then I should get error that quick access link already exists

  Scenario: Cannot delete a non-existent quick access
    Given quick access "qa_ghost" does not exist
    When I delete quick access "qa_ghost"
    Then I should get error that quick access was not found

  Scenario: Cannot toggle a non-existent quick access
    Given quick access "qa_ghost2" does not exist
    When I toggle the new_window flag for quick access "qa_ghost2"
    Then I should get error that quick access was not found
