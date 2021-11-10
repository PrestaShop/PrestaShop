# ./vendor/bin/behat -c tests/Integration/Behaviour/behat.yml -s profile --tags profile-permissions
@profile-permissions
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
