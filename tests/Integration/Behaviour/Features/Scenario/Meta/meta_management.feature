# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s meta
@reset-database-before-feature
Feature: Meta management (Traffic & Seo)
  PrestaShop allows BO users to manage page metadata - title, description, keywords, url_rewrite etc...
  As a BO user I must be able to create, edit, delete and update meta data.

  Background:
    Given shop "shop1" with name "test_shop" exists
    And language with iso code "en" is the default one

  Scenario: Create new metadata
    Given I specify following properties for new meta "meta1":
      | page_name                        | pdf-order-return                      |
      | localized_page_title             | page title in default language        |
      | localized_meta_description       | meta description in default language  |
      | localized_meta_keywords          | meta keywords in default language     |
      | localized_rewrite_urls           | rewrite-url-default                   |
    When I add meta "meta1" with specified properties
    Then meta "meta1" page should be "pdf-order-return"
