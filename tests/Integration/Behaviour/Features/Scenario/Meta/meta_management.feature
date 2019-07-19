# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s meta
@reset-database-before-feature
Feature: Meta management (Traffic & Seo)
  PrestaShop allows BO users to manage page metadata - title, description, keywords, url_rewrite etc...
  As a BO user I must be able to create, edit, delete and update meta data.

  Background:
    Given language with iso code "en" is the default one

  Scenario: Create new metadata
    Given I specify following properties for new meta "meta1":
      | page_name                        | pdf-order-return                      |
      | localized_page_title             | page title in default language        |
      | localized_meta_description       | meta description in default language  |
      | localized_meta_keywords          | meta keywords in default language     |
      | localized_rewrite_urls           | rewrite-url-default                   |
    When I add meta "meta1" with specified properties
    Then meta "meta1" page should be "pdf-order-return"
    And meta "meta1" field "title" for default language should be "page title in default language"
    And meta "meta1" field "description" for default language should be "meta description in default language"
    And meta "meta1" field "keywords" for default language should be "meta keywords in default language"
    And meta "meta1" field "url_rewrite" for default language should be "rewrite-url-default"

  Scenario: Creating new metadata without default language for url rewrite should not be allowed
    Given I specify following properties for new meta "meta2":
      | page_name                          | pdf-invoice                             |
      | localized_rewrite_urls             | rewrite-url                             |
    When I add meta "meta2" with specified properties without default language
    Then I should get error that url rewrite value is incorrect

  Scenario: Creating new metadata with invalid url rewrite should not be allowed when ascended chars setting is turned off
    Given I specify following properties for new meta "meta3":
      | page_name                          | order-return                            |
      | localized_rewrite_urls             | i-got-char-š                            |
    And shop configuration for "PS_ALLOW_ACCENTED_CHARS_URL" is set to 0
    When I add meta "meta3" with specified properties
    Then I should get error that url rewrite value is incorrect

  Scenario: Creating new metadata with invalid url rewrite should be allowed when ascended chars setting is turned on
    Given I specify following properties for new meta "meta4":
      | page_name                          | newproducts                            |
      | localized_rewrite_urls             | i-got-char-š                           |
    And shop configuration for "PS_ALLOW_ACCENTED_CHARS_URL" is set to 1
    When I add meta "meta4" with specified properties
    Then meta "meta4" page should be "newproducts"
    And meta "meta4" field "url_rewrite" for default language should be "i-got-char-š"

  Scenario: Creating new metadata with unknown page name
    Given I specify following properties for new meta "meta5":
      | page_name                          | test-unknown                                    |
      | localized_rewrite_urls             | test-page-unknown                               |
    When I add meta "meta5" with specified properties
    Then I should get error that page name value is incorrect

  Scenario: Creating new metadata with already created page name
    Given I specify following properties for new meta "meta6":
      | page_name                          | index                                           |
      | localized_rewrite_urls             | index                                           |
    When I add meta "meta6" with specified properties
    Then I should get error that page name value is incorrect

  Scenario: Update existing metadata
    Given I specify following properties for new meta "meta7":
      | meta_id                          | 4                                     |
      | page_name                        | index                                 |
      | localized_page_title             | page title in default language        |
      | localized_meta_description       | meta description in default language  |
      | localized_meta_keywords          | meta keywords in default language     |
      | localized_rewrite_urls           | rewrite-url-default                   |
    When I update meta "meta7" with specified properties
    Then meta "meta7" page should be "index"
    And meta "meta7" field "title" for default language should be "page title in default language"
    And meta "meta7" field "description" for default language should be "meta description in default language"
    And meta "meta7" field "keywords" for default language should be "meta keywords in default language"
    And meta "meta7" field "url_rewrite" for default language should be "rewrite-url-default"

  Scenario: Update index page allows to have empty rewrite url
    Given I specify following properties for new meta "meta8":
      | meta_id                          | 4                                     |
    When I update meta "meta8" with specified properties
    And meta "meta8" field "url_rewrite" for default language should be ""

  Scenario: Updating metadata without default language for url rewrite should not be allowed
    Given I specify following properties for new meta "meta9":
      | meta_id                            | 1                                        |
      | localized_rewrite_urls             | rewrite-url                              |
    When I update meta "meta9" with specified properties without default language
    Then I should get error that url rewrite value is incorrect

  Scenario: Updating metadata with invalid url rewrite should not be allowed when ascended chars setting is turned off
    Given I specify following properties for new meta "meta10":
      | meta_id                            | 4                                |
      | localized_rewrite_urls             | i-got-char-š                     |
    And shop configuration for "PS_ALLOW_ACCENTED_CHARS_URL" is set to 0
    When I update meta "meta10" with specified properties
    Then I should get error that url rewrite value is incorrect

  Scenario: Updating metadata with invalid url rewrite should be allowed when ascended chars setting is turned on
    Given I specify following properties for new meta "meta11":
      | meta_id                            | 4                                |
      | localized_rewrite_urls             | i-got-char-š                     |
    And shop configuration for "PS_ALLOW_ACCENTED_CHARS_URL" is set to 1
    When I update meta "meta11" with specified properties
    Then meta "meta11" page should be "index"
    And meta "meta11" field "url_rewrite" for default language should be "i-got-char-š"

  Scenario: Updating metadata with non existing page name
    Given I specify following properties for new meta "meta11":
      | meta_id                            | 4                                |
      | page_name                          | i-dont-exist                     |
    When I update meta "meta11" with specified properties
    Then I should get error that page name value is incorrect

  Scenario: Updating metadata with already existing page name
    Given I specify following properties for new meta "meta12":
      | meta_id                            | 4                                |
      | page_name                          |  	pagenotfound                  |
    When I update meta "meta11" with specified properties
    Then I should get error that page name value is incorrect
