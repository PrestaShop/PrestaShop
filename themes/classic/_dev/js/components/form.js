import $ from 'jquery';

export default class Form {
  init(){
    this.parentFocus();
    this.togglePasswordVisibility();
  }

  parentFocus() {
    $('.js-child-focus').focus(function () {
      $(this).closest('.js-parent-focus').addClass('focus');
    });
    $('.js-child-focus').focusout(function () {
      $(this).closest('.js-parent-focus').removeClass('focus');
    });
  }

  togglePasswordVisibility() {
    $('button[data-action="show-password"]').on('click', function () {
      var elm = $(this).closest('.input-group').children('input.js-visible-password');
      if (elm.attr('type') === 'password') {
        elm.attr('type', 'text');
        $(this).text($(this).data('textHide'));
      } else {
        elm.attr('type', 'password');
        $(this).text($(this).data('textShow'));
      }

    });
  }
}
