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
