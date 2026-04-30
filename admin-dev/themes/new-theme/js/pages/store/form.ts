/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

$(() => {
  window.prestashop.component.initComponents([
    'TranslatableField',
    'TinyMCEEditor',
    'TranslatableInput',
  ]);

  // Dynamically load states when country changes
  const countrySelect = document.querySelector<HTMLSelectElement>('[data-states-url]');
  const stateSelect = document.querySelector<HTMLSelectElement>('[data-country-id]');

  if (countrySelect && stateSelect) {
    const statesUrl = countrySelect.dataset.statesUrl;

    countrySelect.addEventListener('change', () => {
      const countryId = countrySelect.value;

      if (!statesUrl || !countryId) {
        return;
      }

      fetch(`${statesUrl}?id_country=${countryId}`)
        .then((response) => response.json())
        .then((states: Record<string, string>) => {
          // Clear existing options
          stateSelect.innerHTML = '<option value="0">--</option>';

          Object.entries(states).forEach(([id, name]) => {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = name;
            stateSelect.appendChild(option);
          });

          // Show/hide state select based on whether country has states
          const hasStates = Object.keys(states).length > 0;
          const stateRow = stateSelect.closest<HTMLElement>('.form-group, .row');
          if (stateRow) {
            stateRow.style.display = hasStates ? '' : 'none';
          }
        })
        .catch(console.error);
    });
  }
});
