/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * This component toggles forms children based on a radio inputs.
 */
export default class ToggleChildrenChoice {
  private readonly toggleChildrenChoice: string;

  private readonly childrenSelect: string;

  private readonly childrenRadios: string;

  private readonly toggleChild: string;

  private readonly selectedChild: (childName: string) => string;

  constructor(options: Record<string, any> = {}) {
    const opts = options || {};

    this.toggleChildrenChoice = opts.toggleChildrenChoice || '.toggle-children-choice';
    this.childrenSelect = opts.childrenSelect || ':scope > .form-group > .toggle-children-choice-select';
    this.childrenRadios = `${this.childrenSelect} input[type="radio"]`;
    this.toggleChild = opts.toggleChild || ':scope > .toggle-children-choice-container > .toggle-children-choice-child';
    this.selectedChild = (childName: string) => `${this.toggleChild}[data-toggle-name=${childName}]`;

    this.init();
  }

  private init(): void {
    document.querySelectorAll<HTMLElement>(this.toggleChildrenChoice).forEach((toggleChildrenChoice: HTMLElement) => {
      toggleChildrenChoice.querySelectorAll<HTMLInputElement>(this.childrenRadios).forEach((radio: HTMLInputElement) => {
        radio.addEventListener('change', () => {
          toggleChildrenChoice.querySelectorAll<HTMLElement>(this.toggleChild).forEach((formChild: HTMLElement) => {
            formChild.classList.add('d-none');
          });

          // Value can be empty when a placeholder has been set
          if (radio.value) {
            const selectedChild = toggleChildrenChoice.querySelector<HTMLElement>(this.selectedChild(radio.value));

            if (selectedChild) {
              selectedChild.classList.remove('d-none');
            }
          }
          const {eventEmitter} = window.prestashop.instance;
          eventEmitter.emit('ToggleChildrenChoice:toggled', radio);
        });
      });
    });
  }
}
