import $ from 'jquery';

export default class NavBar {
  constructor() {
    $(() => {
      $(".nav-bar").find(".link-levelone").hover(function() {
        $(this).addClass("-hover");
      }, function() {
        $(this).removeClass("-hover");
      });

      $('.nav-bar').on('click', '.menu-collapse', function() {
        $('body').toggleClass('page-sidebar-closed');
        $.ajax({
          url: "index.php",
          cache: false,
          data: {
            token: employee_token,
            ajax: 1,
            action: 'toggleMenu',
            tab: 'AdminEmployees',
            collapse: Number($('body').hasClass('page-sidebar-closed'))
          },
        });
      });
    });
  }
}
