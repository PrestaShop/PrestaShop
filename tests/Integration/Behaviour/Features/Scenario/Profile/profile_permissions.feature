# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s profile --tags profile-permissions
@profile-permissions
# Reset was needed at first, but since we reset the permissions as they were as long as the whole suite is a success we
# shouldn't need to reset the DB
# @reset-database-before-feature
Feature: Manage profile permissions from BO
  As a BO user
  I need to be able to edit profilepermissions

  Background:
    Given profile super_admin with default name "SuperAdmin" exists
    Given profile logistician with default name "Logistician" exists
    Given profile translator with default name "Translator" exists
    Given profile salesman with default name "Salesman" exists

  Scenario: I can get profile tab permissions for profiles
    Then profile super_admin should have the following permissions for tabs:
      | AdminProducts     | view,add,edit,delete |
      | AdminCategories   | view,add,edit,delete |
      | AdminOrders       | view,add,edit,delete |
      | AdminDeliverySlip | view,add,edit,delete |
      | AdminTranslations | view,add,edit,delete |
      | AdminCarts        | view,add,edit,delete |
    And profile logistician should have the following permissions for tabs:
      | AdminProducts     | view,add,edit,delete |
      | AdminCategories   | view,add,edit,delete |
      | AdminOrders       | view,add,edit,delete |
      | AdminDeliverySlip | view,add,edit,delete |
      | AdminTranslations |                      |
      | AdminCarts        |                      |
    And profile translator should have the following permissions for tabs:
      | AdminProducts     | view,add,edit,delete |
      | AdminCategories   | view,add,edit,delete |
      | AdminOrders       |                      |
      | AdminDeliverySlip |                      |
      | AdminTranslations | view,add,edit,delete |
      | AdminCarts        |                      |
    And profile salesman should have the following permissions for tabs:
      | AdminProducts     | view,add,edit,delete |
      | AdminCategories   | view,add,edit,delete |
      | AdminOrders       | view,add,edit,delete |
      | AdminDeliverySlip |                      |
      | AdminTranslations |                      |
      | AdminCarts        | view,add,edit,delete |

  Scenario: I can edit tab permission for a profile
    When I disable view permission for tab AdminProducts for profile translator
    Then profile translator should have the following permissions for tabs:
      | AdminProducts     | add,edit,delete      |
      | AdminCategories   | view,add,edit,delete |
      | AdminOrders       |                      |
      | AdminDeliverySlip |                      |
      | AdminTranslations | view,add,edit,delete |
      | AdminCarts        |                      |
    When I disable add permission for tab AdminProducts for profile translator
    And I disable edit permission for tab AdminProducts for profile translator
    And I disable delete permission for tab AdminProducts for profile translator
    Then profile translator should have the following permissions for tabs:
      | AdminProducts     |                      |
      | AdminCategories   | view,add,edit,delete |
      | AdminOrders       |                      |
      | AdminDeliverySlip |                      |
      | AdminTranslations | view,add,edit,delete |
      | AdminCarts        |                      |
    When I enable view permission for tab AdminProducts for profile translator
    And I enable add permission for tab AdminProducts for profile translator
    And I enable edit permission for tab AdminProducts for profile translator
    And I enable delete permission for tab AdminProducts for profile translator
    Then profile translator should have the following permissions for tabs:
      | AdminProducts     | view,add,edit,delete |
      | AdminCategories   | view,add,edit,delete |
      | AdminOrders       |                      |
      | AdminDeliverySlip |                      |
      | AdminTranslations | view,add,edit,delete |
      | AdminCarts        |                      |
    When I disable all permission for tab AdminOrders for profile logistician
    Then profile logistician should have the following permissions for tabs:
      | AdminProducts     | view,add,edit,delete |
      | AdminCategories   | view,add,edit,delete |
      | AdminOrders       |                      |
      | AdminDeliverySlip | view,add,edit,delete |
      | AdminTranslations |                      |
      | AdminCarts        |                      |
    When I enable all permission for tab AdminOrders for profile logistician
    Then profile logistician should have the following permissions for tabs:
      | AdminProducts     | view,add,edit,delete |
      | AdminCategories   | view,add,edit,delete |
      | AdminOrders       | view,add,edit,delete |
      | AdminDeliverySlip | view,add,edit,delete |
      | AdminTranslations |                      |
      | AdminCarts        |                      |
    # SuperAdmin role has all permissions hard coded even if you try to disable them
    When I disable view permission for tab AdminProducts for profile super_admin
    Then profile super_admin should have the following permissions for tabs:
      | AdminProducts     | view,add,edit,delete |
      | AdminCategories   | view,add,edit,delete |
      | AdminOrders       | view,add,edit,delete |
      | AdminDeliverySlip | view,add,edit,delete |
      | AdminTranslations | view,add,edit,delete |
      | AdminCarts        | view,add,edit,delete |
    When I disable all permission for tab AdminProducts for profile super_admin
    Then profile super_admin should have the following permissions for tabs:
      | AdminProducts     | view,add,edit,delete |
      | AdminCategories   | view,add,edit,delete |
      | AdminOrders       | view,add,edit,delete |
      | AdminDeliverySlip | view,add,edit,delete |
      | AdminTranslations | view,add,edit,delete |
      | AdminCarts        | view,add,edit,delete |

  Scenario: I can get profile module permissions for profiles
    Then profile super_admin should have the following permissions for modules:
      | ps_banner      | view,configure,uninstall |
      | ps_wirepayment | view,configure,uninstall |
    Then profile logistician should have the following permissions for modules:
      | ps_banner      |                          |
      | ps_wirepayment |                          |
    Then profile translator should have the following permissions for modules:
      | ps_banner      |                          |
      | ps_wirepayment |                          |
    Then profile salesman should have the following permissions for modules:
      | ps_banner      |                          |
      | ps_wirepayment |                          |

  Scenario: I can edit module permission for a profile
    When I enable view permission for module ps_banner for profile logistician
    And I enable configure permission for module ps_banner for profile logistician
    And I enable uninstall permission for module ps_banner for profile logistician
    Then profile logistician should have the following permissions for modules:
      | ps_banner      | view,configure,uninstall |
      | ps_wirepayment |                          |
    When I disable view permission for module ps_banner for profile logistician
    And I disable configure permission for module ps_banner for profile logistician
    And I disable uninstall permission for module ps_banner for profile logistician
    Then profile logistician should have the following permissions for modules:
      | ps_banner      |                          |
      | ps_wirepayment |                          |
    # SuperAdmin role has all permissions hard coded even if you try to disable them
    When I disable view permission for module ps_banner for profile super_admin
    And I disable configure permission for module ps_banner for profile super_admin
    And I disable uninstall permission for module ps_banner for profile super_admin
    Then profile super_admin should have the following permissions for modules:
      | ps_banner      | view,configure,uninstall |
      | ps_wirepayment | view,configure,uninstall |
