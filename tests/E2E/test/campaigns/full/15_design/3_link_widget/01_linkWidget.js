const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../../selectors/FO/access_page');
const {LinkWidget} = require('../../../../selectors/BO/design/link_widget');
const {productPage} = require('../../../../selectors/FO/product_page');
const commonLinkWidget = require('../../../common_scenarios/linkwidget');
const {CheckoutOrderPage} = require('../../../../selectors/FO/order_page');
const {Menu} = require('../../../../selectors/BO/menu');
let promise = Promise.resolve();

scenario('Create, edit, delete LinkWidget with different HOOK ', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('Create a link Widget with "displayFooter" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('first', 'displayFooter');
    commonLinkWidget.createWidget('second', 'displayFooter');
    commonLinkWidget.createWidget('displayFooter', 'displayFooter');

    scenario('Check the created Link widget - "displayFooter" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.footer_block_link_widget.replace('%FOOTERBLOCKNAME', 'displayFooter' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayFooter');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.footer_block_second_link_widget, 'DISPLAYFOOTER' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');
  }, 'common_client');

  scenario('Create a link Widget with "DisplayAfterCarrier" hook and check it in the Front Office', client => {
    commonLinkWidget.createWidget('First', 'displayAfterCarrier');
    commonLinkWidget.createWidget('Second', 'displayAfterCarrier');
    commonLinkWidget.createWidget('displayAfterCarrier', 'displayAfterCarrier');

    scenario('Check the created Link widget - "displayAfterCarrier" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should sign in Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageFO.sign_in_button))
          .then(() => client.waitAndSetValue(AccessPageFO.login_input, 'pub@prestashop.com'))
          .then(() => client.waitAndSetValue(AccessPageFO.password_inputFO, '123456789'))
          .then(() => client.waitForExistAndClick(AccessPageFO.login_button))
          .then(() => client.waitForExistAndClick(AccessPageFO.logo_home_page));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product));
      test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(productPage.quick_view_add_to_cart));
      test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
      test('should click on proceed to checkout button 2', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_button));
      test('should click on confirm address button', () => client.waitForExistAndClick(CheckoutOrderPage.checkout_step2_continue_button));
      test('should check in the FO if the block is displayed', () => client.waitForVisible(CheckoutOrderPage.display_after_carrier_link_widget.replace('%NAME', 'displayAfterCarrier' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayAfterCarrier');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(CheckoutOrderPage.display_after_carrier_second_link_widget, 'DISPLAYAFTERCARRIER' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');
  }, 'common_client');

  scenario('Create a link Widget with "DisplayAfterProductThumbs" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayAfterProductThumbs');
    commonLinkWidget.createWidget('Second', 'displayAfterProductThumbs');
    commonLinkWidget.createWidget('displayAfterProductThumbs', 'displayAfterProductThumbs');

    scenario('Check the created Link widget - "DisplayAfterProductThumbs" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should sign in Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageFO.sign_in_button))
          .then(() => client.waitAndSetValue(AccessPageFO.login_input, 'pub@prestashop.com'))
          .then(() => client.waitAndSetValue(AccessPageFO.password_inputFO, '123456789'))
          .then(() => client.waitForExistAndClick(AccessPageFO.login_button))
          .then(() => client.waitForExistAndClick(AccessPageFO.logo_home_page));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product));
      test('should check in the FO if the block is displayed', () => client.waitForVisible(productPage.widget_after_product_thumbs.replace('%NAME', 'displayAfterProductThumbs' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayAfterProductThumbs');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(productPage.second_widget_after_product_thumbs, 'DISPLAYAFTERPRODUCTTHUMBS' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');
  }, 'common_client');

  scenario('Create a link Widget with "displayFooterBefore" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayFooterBefore');
    commonLinkWidget.createWidget('Second', 'displayFooterBefore');
    commonLinkWidget.createWidget('displayFooterBefore', 'displayFooterBefore');

    scenario('Check the created Link widget - "displayFooterBefore" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.display_before_footer_linkwidget.replace('%NAME', 'displayFooterBefore' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayFooterBefore');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.display_before_footer_second_linkwidget, 'DISPLAYFOOTERBEFORE' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');
  }, 'common_client');

  scenario('Create a link Widget with "displayFooterProduct" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayFooterProduct');
    commonLinkWidget.createWidget('Second', 'displayFooterProduct');
    commonLinkWidget.createWidget('displayFooterProduct', 'displayFooterProduct');

    scenario('Check the created Link widget - "displayFooterProduct" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product));
      test('should check in the FO if the block is displayed', () => client.waitForVisible(productPage.display_footer_product_linkwidget.replace('%DISPLAYFOOTERPRODUCT', 'displayFooterProduct' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayFooterBefore');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(productPage.display_second_footer_product_linkwidget, 'DISPLAYFOOTERPRODUCT' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');
  }, 'common_client');

  scenario('Create a link Widget with "displayHome" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayHome');
    commonLinkWidget.createWidget('Second', 'displayHome');
    commonLinkWidget.createWidget('displayHome', 'displayHome');

    scenario('Check the created Link widget - "displayHome" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.home_link_widget.replace('%HOMELINKWIDGET', 'displayHome' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayHome');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_home_link_widget, 'DISPLAYHOME' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');

  }, 'common_client');

  scenario('Create a link Widget with "DisplayNav1" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayNav1');
    commonLinkWidget.createWidget('Second', 'displayNav1');
    commonLinkWidget.createWidget('displayNav1', 'displayNav1');

    scenario('Check the created Link widget - "displayNav1" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => {
        return promise
          .then(() => client.scrollTo(AccessPageFO.sign_in_button))
          .then(() => client.changeLanguage());
      });
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.display_nav1_link_widget.replace('%NAVLINKWIDGET', 'displayNav1' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayNav1');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_display_nav1_link_widget, 'DISPLAYNAV1' + " " + date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');

  }, 'common_client');

  scenario('Create a link Widget with "DisplayNav2" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayNav2');
    commonLinkWidget.createWidget('Second', 'displayNav2');
    commonLinkWidget.createWidget('displayNav2', 'displayNav2');

    scenario('Check the created Link widget - "displayNav2" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.display_nav2_link_widget.replace('%NAVLINKWIDGET', 'displayNav2' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayNav2');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_display_nav2_link_widget, 'DISPLAYNAV2' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');

  }, 'common_client');

  scenario('Create a link Widget with "DisplayNavFullWidth" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayNavFullWidth');
    commonLinkWidget.createWidget('Second', 'displayNavFullWidth');
    commonLinkWidget.createWidget('displayNavFullWidth', 'displayNavFullWidth');

    scenario('Check the created Link widget - "displayNavFullWidth" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.nav_full_width_link_widget.replace('%NAVFULLWIDTHLINKWIDGET', 'displayNavFullWidth' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayNavFullWidth');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_nav_full_width_link_widget, 'DISPLAYNAVFULLWIDTH' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');

  }, 'common_client');

  scenario('Create a link Widget with "DisplayLeftColumn" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayLeftColumn');
    commonLinkWidget.createWidget('Second', 'displayLeftColumn');
    commonLinkWidget.createWidget('displayLeftColumn', 'displayLeftColumn');

    scenario('Check the created Link widget - "displayLeftColumn" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should click on "All products" button', () => client.scrollWaitForExistAndClick(productPage.see_all_products));
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.nav_left_column_link_widget.replace('%NAVLEFTCOLUMNLINKWIDGET', 'displayLeftColumn' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayLeftColumn');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_nav_left_column_link_widget, 'DISPLAYLEFTCOLUMN' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');

  }, 'common_client');

  scenario('Create a link Widget with "DisplayShoppingCart" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayShoppingCart');
    commonLinkWidget.createWidget('Second', 'displayShoppingCart');
    commonLinkWidget.createWidget('displayShoppingCart', 'displayShoppingCart');

    scenario('Check the created Link widget - "displayShoppingCart" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product));
      test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(productPage.quick_view_add_to_cart));
      test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.nav_shopping_cart_link_widget.replace('%NAVSHOPPINGCARTLINKWIDGET', 'displayShoppingCart' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayShoppingCart');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_shopping_cart_link_widget, 'DISPLAYSHOPPINGCART' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');

  }, 'common_client');

  scenario('Create a link Widget with "DisplayShoppingCartFooter" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayShoppingCartFooter');
    commonLinkWidget.createWidget('Second', 'displayShoppingCartFooter');
    commonLinkWidget.createWidget('displayShoppingCartFooter', 'displayShoppingCartFooter');

    scenario('Check the created Link widget - "displayShoppingCartFooter" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should go to the first product page', () => client.waitForExistAndClick(productPage.first_product));
      test('should click on "ADD TO CART" button', () => client.waitForExistAndClick(productPage.quick_view_add_to_cart));
      test('should click on proceed to checkout button 1', () => client.waitForVisibleAndClick(CheckoutOrderPage.proceed_to_checkout_modal_button));
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.nav_shopping_cart_footer_link_widget.replace('%NAVSHOPPINGCARTFOOTERLINKWIDGET', 'displayShoppingCartFooter' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayShoppingCartFooter');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_nav_shopping_cart_footer_link_widget, 'DISPLAYSHOPPINGCARTFOOTER' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');
  }, 'common_client');

  scenario('Create a link Widget with "DisplayTop" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayTop');
    commonLinkWidget.createWidget('Second', 'displayTop');
    commonLinkWidget.createWidget('displayTop', 'displayTop');

    scenario('Check the created Link widget - "displayTop" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.display_top_link_widget.replace('%DISPLAYTOP', 'displayTop' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayTop');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_display_top_link_widget, 'DISPLAYTOP' + " " + +date_time));
      test('should go to the Back Office', () => client.closeWindow(0));
    }, 'common_client');
  }, 'common_client');


  commonLinkWidget.createWidget('EditHook', 'displayFooter');

  scenario('Edit the created linkwidget hook from "displayFooter" to "displayleftcolumn"', client => {
    test('should go to "Design - Link Widget" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.link_widget_submenu));
    test('should click on Display Footer "EditHook" linkwidget edit button', () => client.waitForExistAndClick(LinkWidget.edit_display_footer_created_hook));
    test('should choose the hook "displayLeftColumn"', () => client.waitAndSelectByVisibleText(LinkWidget.hook_select, "displayLeftColumn"));
    test('should select All the "content pages"', () => client.waitForExistAndClick(LinkWidget.select_all_content_page));
    test('should deactivate all product pages"', () => {
      return promise
        .then(() => client.waitForExistAndClick(LinkWidget.select_all_product_page))
        .then(() => client.waitForExistAndClick(LinkWidget.select_all_product_page));
    });
    test('should deactivate all "static content"', () => {
      return promise
        .then(() => client.waitForExistAndClick(LinkWidget.select_all_static_content))
        .then(() => client.waitForExistAndClick(LinkWidget.select_all_static_content));
    });
    test('should click on "save" button', () => client.waitForExistAndClick(LinkWidget.save_button));
    test('should verify the redirection to the link widget page', () => client.checkTextValue(LinkWidget.link_widget_configuration_bloc, 'LINK BLOCK CONFIGURATION', 'contain'));
    test('should refresh the page', () => client.refresh());
    test('should verify if the added block is displayed', () => client.checkTextValue(LinkWidget.last_widget_name_block.replace('%HOOK', "displayLeftColumn"), "EditHook" + " " + +date_time));

    scenario('Check the modification in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should Check in the FO if the block isn\'t anymore in the footer', () => client.isNotExisting(AccessPageFO.footer_block_link_widget.replace('%FOOTERBLOCKNAME', "EditHook" + " " + +date_time)));
      test('should click on "All products" button', () => client.scrollWaitForExistAndClick(productPage.see_all_products));
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.nav_left_column_link_widget.replace('%NAVLEFTCOLUMNLINKWIDGET', "EditHook" + " " + +date_time)));
      test('should go to the back office the page', () => client.closeWindow(0));
    }, 'common_client');
  }, 'common_client');

  scenario('Delete the created linkwidget hook from "displayFooter" to "displayleftcolumn"', client => {
    test('should click on delete button of the created hook', () => {
      return promise
        .then(() => client.waitForExistAndClick(LinkWidget.delete_display_footer_created_hook))
        .then(() => client.waitForExistAndClick(LinkWidget.delete_button));
    });
    test('should verify if the added block is displayed', () => client.isNotExisting(LinkWidget.last_widget_name_block.replace('%HOOK', "displayleftcolumn"), "EditHook" + " " + date_time));
    scenario('Check that the hook is well deleted in the front office in the Front Office', client => {
      test('should go to the Front Office the page', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      test('should check in the FO if the block is displayed', () => client.isNotExisting(AccessPageFO.nav_left_column_link_widget.replace('%NAVLEFTCOLUMNLINKWIDGET', "EditHook" + " " + +date_time)));
    }, 'common_client');
  }, 'common_client');

}, 'common_client', true);


