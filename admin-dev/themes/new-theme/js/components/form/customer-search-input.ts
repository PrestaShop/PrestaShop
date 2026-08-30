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
    const $container = $(customerSearchContainer);
    const disabledBadgeLabel: string = $container.data('disabledBadgeLabel') as string;
    const guestBadgeLabel: string = $container.data('guestBadgeLabel') as string;

    super($container, {
      extraQueryParams: () => ({
        shopId: shopIdCallback(),
      }),
      responseTransformer: (response: any) => {
        if (!response || response.customers.length === 0) {
          return [];
        }

        return Object.values(response.customers);
      },
      suggestionTemplate: (entity: any) => {
        const guestBadge = String(entity.is_guest) === '1'
          ? `<span class="customer-suggestion-guest-badge badge badge-pill badge-secondary">${guestBadgeLabel}</span> `
          : '';
        const disabledBadge = String(entity.active) === '0'
          ? ` <span class="customer-suggestion-disabled-badge badge badge-pill badge-secondary">${disabledBadgeLabel}</span>`
          : '';

        return `<div class="search-suggestion">${guestBadge}${entity.fullname_and_email}${disabledBadge}</div>`;
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
