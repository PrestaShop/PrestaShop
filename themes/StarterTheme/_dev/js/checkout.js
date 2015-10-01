import $ from 'jquery';

$(document).ready(function listenForTermsAndConditionsApprovalChange () {
  var $conditionsForm = $('#conditions-to-approve');

  if (!$conditionsForm) {
    return;
  }

  $conditionsForm.find('input[type="submit"]').hide();
  $conditionsForm.find('input[type="checkbox"]').each((_, checkbox) => {
    $(checkbox).on('change', () => {
      $conditionsForm.submit();
    });
  });
});
