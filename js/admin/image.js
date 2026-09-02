/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(function(){
  const formId = 'image_type_form';

  // on submit, re-enable disabled checkbox so that it is properly sent to backend
  const form = document.getElementById(formId);
  form.onsubmit = () => {
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');

    checkboxes.forEach(checkbox => {
      checkbox.disabled = false;
    });
  };
});
