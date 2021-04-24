# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cms_page
@reset-database-before-feature
Feature: CmsPage Management
  PrestaShop allows BO users to manage CMS pages
  As a BO user
  I must be able to create, edit and delete CMS page in my shop

  Scenario: Adding new CMS page
    When I add new CMS page "cmspage-1" with following properties:
      | id_cms_category      | 1                                        |
      | meta_title           | Special delivery options                 |
      | head_seo_title       | delivery options                         |
      | meta_description     | Our special delivery options             |
      | meta_keywords        | delivery,configure,special               |
      | link_rewrite         | delivery-options                         |
      | content              | <div> <h5> Delivery <img src="../delivery/options.jpg" alt="" /></h5> </div>|
      | indexation           | true                                     |
      | active               | true                                     |
    Then CMS page "cmspage-1" "meta_title" in default language should be 'Special delivery options'
    And CMS page "cmspage-1" "head_seo_title" in default language should be 'delivery options'
    And CMS page "cmspage-1" "meta_description" in default language should be 'Our special delivery options'
    And CMS page "cmspage-1" "meta_keywords" in default language should be 'delivery,configure,special'
    And CMS page "cmspage-1" "link_rewrite" in default language should be 'delivery-options'
    And CMS page "cmspage-1" "content" in default language should be '<div> <h5> Delivery <img src="../delivery/options.jpg" alt="" /></h5> </div>'
    And CMS page "cmspage-1" indexation for search engines should be enabled
    And CMS page "cmspage-1" should be displayed

  Scenario: Adding new CMS page for non existing category should not be allowed
    Given cms category with id "5000" does not exist
    When I create CMS page "cmspage-3" with cms category id "5000"
    Then I should get error message 'Cms page category with id "5000" not found'

  Scenario: Editing CMS page
    When I edit CMS page "cmspage-1" with following properties:
      | meta_title           | Unusual delivery options              |
      | meta_description     | Our unusual delivery options          |
      | meta_keywords        | delivery,configure,special,unusual    |
      | content              |                                       |
      | active               | false                                 |
    Then CMS page "cmspage-1" "meta_title" in default language should be 'Unusual delivery options'
    And CMS page "cmspage-1" "head_seo_title" in default language should be 'delivery options'
    And CMS page "cmspage-1" "meta_description" in default language should be 'Our unusual delivery options'
    And CMS page "cmspage-1" "meta_keywords" in default language should be 'delivery,configure,special,unusual'
    And CMS page "cmspage-1" "link_rewrite" in default language should be 'delivery-options'
    And CMS page "cmspage-1" "content" field in default language should be empty
    And CMS page "cmspage-1" indexation for search engines should be enabled
    And CMS page "cmspage-1" should be not displayed

  Scenario: Editing CMS page with wrong CMS category id
    Given cms category with id "60274513" does not exist
    When I edit CMS page "cmspage-1" with following properties:
      | id_cms_category | 60274513   |
    Then I should get error message 'Cms page category with id "60274513" not found'

  Scenario: Editing CMS page single field should be allowed
    When I edit CMS page "cmspage-1" with following properties:
      | content              | <span style="color:#0000FF;"> <a href="www.special.test">Check options</a></span> |
    Then CMS page "cmspage-1" "content" in default language should be '<span style="color:#0000FF;"> <a href="www.special.test">Check options</a></span>'

  Scenario: Toggling CMS page display status
    Given CMS page "cmspage-1" should be not displayed
    When I toggle CMS page "cmspage-1" display status
    Then CMS page "cmspage-1" should be displayed
    When I toggle CMS page "cmspage-1" display status
    Then CMS page "cmspage-1" should be not displayed

  Scenario: Enabling and disabling CMS pages in bulk action
    Given CMS pages: "cmspage-2,cmspage-3,cms-page-4" exists
    Then CMS pages: "cmspage-2,cmspage-3,cms-page-4" should be not displayed
    When I enable CMS pages: "cmspage-2,cmspage-3,cms-page-4" in bulk action
    Then CMS pages: "cmspage-2,cmspage-3,cms-page-4" should be displayed
    And CMS pages: "cmspage-2,cmspage-3,cms-page-4" should be displayed
    When I disable CMS pages: "cmspage-2,cmspage-3,cms-page-4" in bulk action

  Scenario: Deleting CMS pages
    When I delete CMS page "cmspage-1"
    Then CMS page "cmspage-1" should be deleted
    When I delete CMS pages: "cmspage-2,cmspage-3,cms-page-4" using bulk action
    Then CMS pages: "cmspage-2,cmspage-3,cms-page-4" should be deleted
