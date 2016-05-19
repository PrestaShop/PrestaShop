import $ from 'jquery';
// import Bloodhound from 'typeahead.js';

export default function() {
  $(document).ready(function() {
    $('.js-attribute-checkbox').change( (event) => {
      if ($(event.target).is(':checked')) {
        if ($(`.token[data-value="${$(event.target).data('value')}"] .close`).length === 0) {
          $('#form_step3_attributes').tokenfield(
            'createToken',
            {value: $(event.target).data('value'), label: $(event.target).data('label')}
          );
        }
      } else {
        $(`.token[data-value="${$(event.target).data('value')}"] .close`).click();
      }
    });
  });

  $('#form_step3_attributes')
    .on('tokenfield:createdtoken', function (e) {
      if (!$(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).is(':checked')) {
        $(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).prop('checked', true);
      }
    })
    .on('tokenfield:removedtoken', function (e) {
      if ($(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).is(':checked')) {
        $(`.js-attribute-checkbox[data-value="${e.attrs.value}"]`).prop('checked', false);
      }
    });
}
