const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../../selectors/FO/access_page');
const {LinkWidget} = require('../../../../selectors/BO/design/link_widget');
const {productPage} = require('../../../../selectors/FO/product_page');
const commonLinkWidget = require('../../../common_scenarios/linkwidget');
const {CheckoutOrderPage} = require('../../../../selectors/FO/order_page');
let promise = Promise.resolve();
scenario('Create, edit, delete and delete with bulk actions page category', () => {

  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

     scenario('Create a link Widget with "displayFooter" hook and check it in the Front Office', client => {

       test('Should check that the table contains at least 2 rows', () => {
         return promise
           .then(() => client.isVisible(LinkWidget.second_element_hook_in_table.name_filter))
           .then(() =>{ if(global.isVisible){

         }})

       });


        commonLinkWidget.createWidget('displayFooter', 'displayFooter');

        scenario('Check the created Link widget - "displayFooter" in the Front Office', client => {
          test('should go to the Front Office', () => {
            return promise
              .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
              .then(() => client.switchWindow(1));
          });
          test('should change the Front Office language to "English"', () => client.changeLanguage());
          test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.footer_block.replace('%FOOTERBLOCKNAME', 'displayFooter' + " " + date_time)));
          test('should go to the back office the page', () => client.switchWindow(0));
        }, 'common_client');

        commonLinkWidget.dragAndDropHookBO('displayFooter');

        scenario('Check that the position of the created widget is changed', client => {
          test('should go to the Front Office', () => client.switchWindow(1));
          test('should refresh the page', () => client.refresh());
          test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_footer_block, 'DISPLAYFOOTER' + " " + +date_time));
          test('should go to the Back Office', () => client.switchWindow(0));
        }, 'common_client');
      }, 'common_client');

  /*
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
            test('should go to the Back Office', () => client.switchWindow(0));
          }, 'common_client');
        }, 'common_client');
  */

  /*  scenario('Create a link Widget with "DisplayAfterProductThumbs" hook and check it in the Front Office', client => {

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
        test('should go to the Back Office', () => client.switchWindow(0));
      }, 'common_client');
    }, 'common_client');*/

  /*  scenario('Create a link Widget with "displayFooterBefore" hook and check it in the Front Office', client => {

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
        test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.footer_block.replace('%FOOTERBLOCKNAME', 'displayFooterBefore' + " " + date_time)));
        test('should go to the back office the page', () => client.switchWindow(0));
      }, 'common_client');

      commonLinkWidget.dragAndDropHookBO('displayFooterBefore');

      scenario('Check that the position of the created widget is changed', client => {
        test('should go to the Front Office', () => client.switchWindow(1));
        test('should refresh the page', () => client.refresh());
        test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(productPage.second_widget_after_product_thumbs, 'DISPLAYAFTERPRODUCTTHUMBS' + " " + +date_time));
        test('should go to the Back Office', () => client.switchWindow(0));
      }, 'common_client');
    }, 'common_client');*/

  /*   scenario('Create a link Widget with "displayFooterProduct" hook and check it in the Front Office', client => {

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
         test('should check in the FO if the block is displayed', () => client.waitForVisible(productPage.product_footer_linkwidget.replace('%DISPLAYFOOTERPRODUCT', 'displayFooterProduct' + " " + date_time)));
         test('should go to the back office the page', () => client.switchWindow(0));
       }, 'common_client');

       commonLinkWidget.dragAndDropHookBO('displayFooterBefore');

       scenario('Check that the position of the created widget is changed', client => {
         test('should go to the Front Office', () => client.switchWindow(1));
         test('should refresh the page', () => client.refresh());
         test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(productPage.second_product_footer_linkwidget, 'DISPLAYFOOTERPRODUCT' + " " + +date_time));
         test('should go to the Back Office', () => client.switchWindow(0));
       }, 'common_client');
     }, 'common_client');
 */
  /*   scenario('Create a link Widget with "displayHome" hook and check it in the Front Office', client => {

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
         test('should go to the Back Office', () => client.switchWindow(0));
       }, 'common_client');

     }, 'common_client');*/


/* scenario('Create a link Widget with "DisplayNav1" hook and check it in the Front Office', client => {

    commonLinkWidget.createWidget('First', 'displayNav1');
    commonLinkWidget.createWidget('Second', 'displayNav1');
    commonLinkWidget.createWidget('displayNav1', 'displayNav1');

    scenario('Check the created Link widget - "displayNav1" in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
          .then(() => client.switchWindow(1));
      });
      test('should change the Front Office language to "English"', () => client.changeLanguage());
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.nav_link_widget.replace('%NAVLINKWIDGET', 'displayNav1' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayNav1');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
     // test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_nav_link_widget, 'DISPLAYNAV1' + " " + +date_time));
      test('should go to the Back Office', () => client.switchWindow(0));
    }, 'common_client');

  }, 'common_client');*/



/*    scenario('Create a link Widget with "DisplayNav2" hook and check it in the Front Office', client => {

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
        test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.nav_link_widget.replace('%NAVLINKWIDGET', 'displayNav2' + " " + date_time)));
        test('should go to the back office the page', () => client.switchWindow(0));
      }, 'common_client');

      commonLinkWidget.dragAndDropHookBO('displayNav2');

      scenario('Check that the position of the created widget is changed', client => {
        test('should go to the Front Office', () => client.switchWindow(1));
        test('should refresh the page', () => client.refresh());
       // test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_nav_link_widget, 'DISPLAYNAV2' + " " + +date_time));
        test('should go to the Back Office', () => client.switchWindow(0));
      }, 'common_client');

    }, 'common_client');*/




/*    scenario('Create a link Widget with "DisplayNavFullWidth" hook and check it in the Front Office', client => {

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
        //test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_nav_link_widget, 'displayNavFullWidth' + " " + +date_time));
        test('should go to the Back Office', () => client.switchWindow(0));
      }, 'common_client');

    }, 'common_client');*/



/*  scenario('Create a link Widget with "DisplayLeftColumn" hook and check it in the Front Office', client => {

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
      test('should go to the product page "', () => client.scrollWaitForExistAndClick(productPage.see_all_products));
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.nav_left_column_link_widget.replace('%NAVLEFTCOLUMNLINKWIDGET', 'displayLeftColumn' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayLeftColumn');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      //test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_nav_link_widget, 'displayNavFullWidth' + " " + +date_time));
      test('should go to the Back Office', () => client.switchWindow(0));
    }, 'common_client');

  }, 'common_client');*/



/*    scenario('Create a link Widget with "DisplayShoppingCart" hook and check it in the Front Office', client => {

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
        // test('should click on proceed to checkout button 2', () => client.waitForExistAndClick(CheckoutOrderPage.proceed_to_checkout_button));
        test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.nav_shopping_cart_link_widget.replace('%NAVSHOPPINGCARTLINKWIDGET', 'displayShoppingCart' + " " + date_time)));
        test('should go to the back office the page', () => client.switchWindow(0));
      }, 'common_client');

      commonLinkWidget.dragAndDropHookBO('displayShoppingCart');

      scenario('Check that the position of the created widget is changed', client => {
        test('should go to the Front Office', () => client.switchWindow(1));
        test('should refresh the page', () => client.refresh());
        //test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_nav_link_widget, 'displayNavFullWidth' + " " + +date_time));
        test('should go to the Back Office', () => client.switchWindow(0));
      }, 'common_client');

    }, 'common_client');*/



/*  scenario('Create a link Widget with "DisplayShoppingCartFooter" hook and check it in the Front Office', client => {

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
      //test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_nav_link_widget, 'displayNavFullWidth' + " " + +date_time));
      test('should go to the Back Office', () => client.switchWindow(0));
    }, 'common_client');
  }, 'common_client');*/


/*  scenario('Create a link Widget with "DisplayTop" hook and check it in the Front Office', client => {

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
      test('should check in the FO if the block is displayed', () => client.waitForVisible(AccessPageFO.nav_link_widget.replace('%NAVLINKWIDGET', 'displayTop' + " " + date_time)));
      test('should go to the back office the page', () => client.switchWindow(0));
    }, 'common_client');

    commonLinkWidget.dragAndDropHookBO('displayTop');

    scenario('Check that the position of the created widget is changed', client => {
      test('should go to the Front Office', () => client.switchWindow(1));
      test('should refresh the page', () => client.refresh());
      //test('should check in the FO if the positions of the blocks have changed', () => client.checkTextValue(AccessPageFO.second_nav_link_widget, 'displayNavFullWidth' + " " + +date_time));
      test('should go to the Back Office', () => client.switchWindow(0));
    }, 'common_client');
  }, 'common_client');*/


}, 'common_client');

