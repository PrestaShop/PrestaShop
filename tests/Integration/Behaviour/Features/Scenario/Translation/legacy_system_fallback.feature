# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml --name "Legacy Translation System"
Feature: Legacy Translation System in the Modules
  All module developers should be able to use trans() regardless of the underlying translation system.
  trans() should fall back to the legacy translation system if:
    * The translation domain belongs to a module (ie. it starts with Modules.)
    * The domain doesn't exist in the translation catalogue, or the wording is untranslated.


  Scenario: Access to trans() function in a Module
    Given I install the module "translations"
    And I have a legacy translation for "Hello World" in locale "fr" in the module "translations"
    Then the Module translated version of "Hello World" using domain "Modules.Translations" should be "Bonjour le monde" using locale "fr-FR"

  Scenario: Access to trans() function in a Twig template
    Given I install the module "translations"
    And I have a legacy translation for "Hello World" in locale "fr" in the module "translations"
    Then the Twig translated version of "Hello World" using domain "Modules.Translations" should be "Bonjour le monde" using locale "fr-FR"
