/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

$(function(){
  const checkboxName = 'PS_IMAGE_FORMAT[]';
  const formId = 'image_type_form';
  const checkedCheckboxes = `input[name="${checkboxName}"]:checked`
  const avifFieldId = '#PS_IMAGE_FORMATavif_on';

  const avifCheckbox = document.querySelector(avifFieldId);
  const isAvifSupported = !(avifCheckbox.disabled && !avifCheckbox.checked);

  // on page load, disable checkbox if there is only one checked
  if (document.querySelectorAll(checkedCheckboxes).length === 1) {
    document.querySelector(checkedCheckboxes).disabled = true;
  }

  // on change, if only one checkbox is checked, disable it
  const checkboxes = document.querySelectorAll(`input[name="${checkboxName}"]`);
  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', function(event){
      const checkedCount = document.querySelectorAll(checkedCheckboxes).length;

      if (checkedCount > 1) {
        let disabledCheckbox;
        if (isAvifSupported) {
          disabledCheckbox = document.querySelector(`input[name="${checkboxName}"]:disabled`);
        } else {
          disabledCheckbox = document.querySelector(`input[name="${checkboxName}"]:disabled:not(${avifFieldId})`);
        }
        disabledCheckbox.disabled = false;
      } else {
        const checkedCheckbox = document.querySelector(checkedCheckboxes);
        checkedCheckbox.disabled = true;
      }
    });
  });

  // on submit, re-enable disabled checkbox so that it is properly sent to backend
  const form = document.getElementById(formId);
  form.onsubmit = () => {
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');

    checkboxes.forEach(checkbox => {
      checkbox.disabled = false;
    });
  };
});
