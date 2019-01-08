module.exports = {
  DashBoardPage: {
    dashboard_employee_information_link: '#header-employee-container div.person, #employee_infos', //@TODO
    dashboard_sign_out_option_link: '#header_logout',
    dashboard_view_my_shop_link: '#header_shopname',
    multistore_link: '#header_shop > li > a',
    link_shop: '#header_shop  li:nth-child(%D) > a.link-shop i',
    shopname_button:'#header_shop',
    shopname_option:'#header_shop li:nth-child(%ID) > a:nth-child(1)',
    symfony_toolbar_close_button: 'div[id*="sfToolbarMainContent"][style="display: block;"] a.hide-button',
    onbording_stop_button: '#nav-sidebar a.onboarding-button-stop',
    on_boarding_welcome_modal: 'div.onboarding-welcome i',
    multistore_shop_name: '#header_shop > li > a',
    click_all_shops: '#header_shop ul > li:nth-child(1) > a'
  }
};
