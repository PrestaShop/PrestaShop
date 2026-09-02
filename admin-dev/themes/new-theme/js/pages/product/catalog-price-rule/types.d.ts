/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

export interface CatalogPriceRuleForListing {
  id: number,
  shop: string,
  currency: string,
  country: string,
  group: string,
  name: string,
  impact: string,
  startDate: string,
  endDate: string,
  fromQuantity: string,
}

export interface CatalogPriceRulePeriod {
  from: string,
  to: string
}
