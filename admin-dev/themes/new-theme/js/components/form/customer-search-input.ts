/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
import EntitySearchInput from '@components/entity-search-input';
import EventEmitter from '@components/event-emitter';

const SUGGESTION_FIELD = 'fullname_and_email';
const DEFAULT_DISABLED_LABEL = 'Disabled';
const DEFAULT_GUEST_LABEL = 'Guest';

export default class CustomerSearchInput extends EntitySearchInput {
  private readonly disablingSwitchEvent: string | undefined;

  private readonly customerItemSelector: string;

  private readonly $container: JQuery;

  constructor(
    customerSearchContainer: string,
    customerItemSelector: string,
    shopIdCallback: () => number|null,
    disablingSwitchEvent?: string|undefined,
  ) {
    const $container = $(customerSearchContainer);
    super($container, {
      extraQueryParams: () => ({
        shopId: shopIdCallback(),
      }),
      responseTransformer: (response: any) => {
        if (!response || !response.customers) {
          return [];
        }
        const customers = Array.isArray(response.customers)
          ? response.customers
          : Object.values(response.customers);

        return customers;
      },
      suggestionTemplate: (entity: any) => this.renderSuggestion(entity),
    });
    this.$container = $container;
    this.disablingSwitchEvent = disablingSwitchEvent;
    this.customerItemSelector = customerItemSelector;
    this.listenDisablingSwitch();
  }

  private renderSuggestion(entity: any): string {
    let entityImage = '';

    if (Object.prototype.hasOwnProperty.call(entity, 'image')) {
      entityImage = `<img src="${entity.image}" /> `;
    }

    const text = entity[SUGGESTION_FIELD] ?? '';
    const isDisabled = entity.active === 0 || entity.active === '0';
    const isGuest = Boolean(entity.is_guest);

    const disabledLabel = this.$container.data('disabledBadgeLabel') ?? DEFAULT_DISABLED_LABEL;
    const guestLabel = this.$container.data('guestBadgeLabel') ?? DEFAULT_GUEST_LABEL;

    const badges: string[] = [];
    if (isDisabled) {
      badges.push(`<span class="badge badge-secondary rounded ml-2">${disabledLabel}</span>`);
    }
    if (isGuest) {
      badges.push(`<span class="badge badge-secondary rounded ml-2">${guestLabel}</span>`);
    }
    const badgeHtml = badges.length > 0 ? ` ${badges.join(' ')}` : '';

    return `<div class="search-suggestion d-flex align-items-center justify-content-between flex-wrap">${entityImage}<span>${text}</span>${badgeHtml}</div>`;
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
