# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s cms_page
@reset-database-before-feature
Feature: CmsPage Management
  PrestaShop allows BO users to manage cms pages
  As a BO user
  I must be able to create, edit and delete CMS page in my shop

  Scenario: Adding new cms page
    When I add new CMS page "cmspage-1" with following properties:
      | id_cms_category      | 1                                        |
      | meta_title           | Special delivery options                 |
      | head_seo_title       | delivery options                         |
      | meta_description     | Our special delivery options             |
      | meta_keywords        | delivery,configure,special               |
      | link_rewrite         | delivery-options                         |
      | content              | <div> <h5> Delivery <img src="../delivery/options.jpg" alt="" /></h5> </div>|
      | indexation           | 1                                        |
      | active               | 1                                        |
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
    Then I should get error message 'Cms page contains invalid field values'

  Scenario: Editing cms page
    When I edit CMS page "cmspage-1" with following properties:
      | meta_title           | Unusual delivery options              |
      | meta_description     | Our unusual delivery options          |
      | meta_keywords        | delivery,configure,special,unusual    |
      | content              |                                       |
      | active               | 0                                     |
    Then CMS page "cmspage-1" "meta_title" in default language should be 'Unusual delivery options'
    And CMS page "cmspage-1" "head_seo_title" in default language should be 'delivery options'
    And CMS page "cmspage-1" "meta_description" in default language should be 'Our unusual delivery options'
    And CMS page "cmspage-1" "meta_keywords" in default language should be 'delivery,configure,special,unusual'
    And CMS page "cmspage-1" "link_rewrite" in default language should be 'delivery-options'
    And CMS page "cmspage-1" "content" field in default language should be empty
    And CMS page "cmspage-1" indexation for search engines should be enabled
    And CMS page "cmspage-1" should be not displayed

  Scenario: Editing CMS page single field should be allowed
    When I edit CMS page "cmspage-1" with following properties:
      | content              | <span style="color:#0000FF;"> <a href="www.special.test">Check options</a></span> |
    Then CMS page "cmspage-1" "content" in default language should be '<span style="color:#0000FF;"> <a href="www.special.test">Check options</a></span>'

  Scenario: Enabling CMS page status
    Given CMS page "cmspage-1" should be not displayed
    When I toggle CMS page "cmspage-1" display status
    Then CMS page "cmspage-1" should be displayed

  Scenario: Disabling CMS page status
    Given CMS page "cmspage-1" should be displayed
    When I toggle CMS page "cmspage-1" display status
    Then CMS page "cmspage-1" should be not displayed

  Scenario: Disabling cms pages status in bulk action
    Given CMS pages: "cmspage-2,cmspage-3,cms-page-4" exists
    And CMS pages: "cmspage-2,cmspage-3,cms-page-4" should be displayed
    When I disable CMS pages: "cmspage-2,cmspage-3,cms-page-4" in bulk action
    Then CMS pages: "cmspage-2,cmspage-3,cms-page-4" should be not displayed

  Scenario: Deleting cms page
    Given CMS page with id "1" exists
    When I delete CMS page with id "1"
    Then CMS page with id "1" should not exist

