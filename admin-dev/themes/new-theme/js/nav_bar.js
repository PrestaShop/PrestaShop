import $ from 'jquery';

export default class NavBar {
  constructor() {
    $(() => {
      $(".nav-bar").find(".link-levelone").hover(function() {
        $(this).addClass("-hover");
      }, function() {
        $(this).removeClass("-hover");
      });
    });
  }
}
