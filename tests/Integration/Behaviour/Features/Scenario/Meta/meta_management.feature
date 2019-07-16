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
#    todo: in update check index page that it should have empty value ( exception)
#  todo: non existing page name

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
#  todo: make sure above two tests not work when scalar value is used in IsUrlRewriteValidator
