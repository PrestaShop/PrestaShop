# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cms_page
@reset-database-before-feature
Feature: CmsPage Management
  PrestaShop allows BO users to manage cms pages
  As a BO user
  I must be able to create, edit and delete cms page in my shop

  Scenario: Adding new cms page
    When I add new cms page "cmspage-1" with following properties:
      | meta_title           | test.This is the title field          |
      | head_seo_title       | headseotitle(legacy) or metatitle(new)|
      | meta_description     |                                       |
      | meta_keywords        | test,keyword,key2,key3                |
      | link_rewrite         | friendlyurlexample                    |
      | content              | <div> hello world </div>              |
      | indexation           | 1                                     |
      | active               | 0                                     |
    Then cms page "cmspage-1" "meta_title" in default language should be "test.This is the title field"
    And cms page "cmspage-1" "head_seo_title" in default language should be "headseotitle(legacy) or metatitle(new)"
    And cms page "cmspage-1" "meta_description" field in default language should be empty
    And cms page "cmspage-1" "link_rewrite" in default language should be "friendlyurlexample"
    And cms page "cmspage-1" "content" in default language should be "<div> hello world </div>"
    And cms page "cmspage-1" indexation for search engines should be enabled
    And cms page "cmspage-1" should be not displayed

  Scenario: Attempting to create cms page with empty title
    Given cms page "cmspage-2" does not exist
    When I attempt to create cms page "cmspage-2" with empty title
    Then I should get error message 'Cms page contains invalid field values'
    And cms page "cmspage-2" does not exist

  Scenario: Attempting to create cms page for not existing category
    Given cms category with id "5000" does not exist
    When I attempt to create cms page "cmspage-3" with cms category id "5000"
    Then I should get error message '<string>'

  Scenario: Editing cms page
    When I edit cms page "cmspage-1" with following properties:
      | meta_title           | edited title                          |
      | meta_description     | edited meta description               |
      | meta_keywords        | key2,key3                             |
      | link_rewrite         | friendlyurl-edited                    |
      | content              |                                       |
      | indexation           | 0                                     |
      | active               | 1                                     |
    Then cms page "cmspage-1" "meta_title" in default language should be "edited title"
    And cms page "cmspage-1" "head_seo_title" in default language should be "headseotitle(legacy) or metatitle(new)"
    And cms page "cmspage-1" "meta_description" in default language should be "edited meta description"
    And cms page "cmspage-1" "meta_keywords" in default language should be "key2,key3"
    And cms page "cmspage-1" "link_rewrite" in default language should be "friendlyurl-edited"
    And cms page "cmspage-1" "content" field in default language should be empty
    And cms page "cmspage-1" indexation for search engines should be disabled
    And cms page "cmspage-1" should be displayed

  Scenario: Attempting to edit only content field of cms page
    When I edit cms page "cmspage-1" with following properties:
      | content              | <span> content edited </span> |
    Then cms page "cmspage-1" "content" in default language should be "<span> content edited </span>"

  Scenario: Attempting to add illegal script to cms page content
    Given cms page "cmspage-1" "content" in default language should be "<span> content edited </span>"
    When I attempt to edit cms page "cmspage-1" providing illegal content value "<form onsubmit='evil()'></form>"
    Then I should get error message 'Cms page contains invalid field values'
    And cms page "cmspage-1" "content" in default language should be "<span> content edited </span>"

  Scenario: Deleting cms page
    Given cms page with id "1" exists
    When I delete cms page with id "1"
    Then cms page with id "1" should not exist

