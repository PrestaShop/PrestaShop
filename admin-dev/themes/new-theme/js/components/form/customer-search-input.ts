/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import EntitySearchInput from '@components/entity-search-input';
import EventEmitter from '@components/event-emitter';

export default class CustomerSearchInput extends EntitySearchInput {
  private readonly disablingSwitchEvent: string | undefined;

  private readonly customerItemSelector: string;

  constructor(
    customerSearchContainer: string,
    customerItemSelector: string,
    shopIdCallback: () => number|null,
    disablingSwitchEvent?: string|undefined,
  ) {
    super($(customerSearchContainer), {
      extraQueryParams: () => ({
        shopId: shopIdCallback(),
      }),
      responseTransformer: (response: any) => {
        if (!response || response.customers.length === 0) {
          return [];
        }

        return Object.values(response.customers);
      },

    });
    this.disablingSwitchEvent = disablingSwitchEvent;
    this.customerItemSelector = customerItemSelector;
    this.listenDisablingSwitch();
  }

  private listenDisablingSwitch(): void {
    if (this.disablingSwitchEvent === undefined) {
      return;
    }

    const eventEmitter = <typeof EventEmitter> window.prestashop.instance.eventEmitter;

    // When customer search is disabled we also disable the selected item (if present)
    eventEmitter.on(this.disablingSwitchEvent, (event: any) => {
      $(this.customerItemSelector).toggleClass('disabled', event.disable);
    });
  }
}
