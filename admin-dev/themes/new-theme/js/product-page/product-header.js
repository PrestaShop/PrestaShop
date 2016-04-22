import $ from 'jquery';

export default function() {
  if (!window.location.hash.length) {
    $('.js-edit').val('');
  }
}
