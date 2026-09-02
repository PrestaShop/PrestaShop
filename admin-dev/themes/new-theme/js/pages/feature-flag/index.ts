/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

import ConfirmModal from '@components/modal';
import FeatureFlagMap from '@pages/feature-flag/components-map';

const {$} = window;

$(() => {
  const $submitButton = $(FeatureFlagMap.betaSubmitButton);
  const $stableFormSubmitButton = $(FeatureFlagMap.stableSubmitButton);
  const $form = $(FeatureFlagMap.betaForm);
  const $betaFormInputs = $(FeatureFlagMap.betaFormInputFields);
  const $stableForm = $(FeatureFlagMap.stableForm);
  const $stableFormInputs = $(FeatureFlagMap.stableFormInputs);
  const $stableFormInitialState = $stableForm.serialize();
  const initialState = $form.serialize();
  const initialFormData = $form.serializeArray();

  $betaFormInputs.on('change', () => {
    $submitButton.prop('disabled', initialState === $form.serialize());
  });

  $stableFormInputs.on('change', () => {
    $stableFormSubmitButton.prop('disabled', $stableFormInitialState === $stableForm.serialize());
  });

  $submitButton.on('click', (event) => {
    event.preventDefault();

    const formData = $form.serializeArray();

    if (initialState === $form.serialize()) {
      return;
    }

    let oneFlagIsEnabled = false;

    for (let i = 0; i < formData.length; i += 1) {
      if ((formData[i].name !== 'form[_token]') && (formData[i].value !== '0') && (initialFormData[i].value === '0')) {
        oneFlagIsEnabled = true;
        break;
      }
    }

    if (oneFlagIsEnabled) {
      new ConfirmModal(
        {
          id: 'modal-confirm-submit-feature-flag',
          confirmTitle: $submitButton.data('modal-title'),
          confirmMessage: $submitButton.data('modal-message'),
          confirmButtonLabel: $submitButton.data('modal-apply'),
          closeButtonLabel: $submitButton.data('modal-cancel'),
        },
        () => {
          $form.submit();
        },
      );
    } else {
      $form.submit();
    }
  });
});
