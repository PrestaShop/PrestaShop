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
import {EventEmitter} from 'events';
import ProductMap from '@pages/product/product-map';
import RendererType from '@PSTypes/renderers';
import {CatalogPriceRuleForListing} from '@pages/product/catalog-price-rule/types';

const CatalogPriceRuleMap = ProductMap.catalogPriceRule;

export default class CatalogPriceRuleRenderer implements RendererType {
  private eventEmitter: EventEmitter;

  private listContainer: HTMLElement;

  private $loadingSpinner: JQuery;

  private $listTable: JQuery;

  constructor() {
    this.listContainer = <HTMLElement>document.querySelector(CatalogPriceRuleMap.listContainer);
    this.eventEmitter = window.prestashop.instance.eventEmitter;
    this.$loadingSpinner = $(ProductMap.catalogPriceRule.loadingSpinner);
    this.$listTable = $(ProductMap.catalogPriceRule.listTable);
  }

  public setLoading(loading: boolean): void {
    this.$loadingSpinner.toggle(loading);
    this.$listTable.toggle(!loading);
  }

  public render(data: Record<string, any>): void {
    const {listFields} = CatalogPriceRuleMap;
    const tbody = this.listContainer.querySelector<HTMLElement>(`${CatalogPriceRuleMap.listContainer} tbody`);

    if (!tbody) {
      console.error(`Error: ${CatalogPriceRuleMap.listContainer} element not found`);
      return;
    }
    const trTemplateContainer = this.listContainer.querySelector<HTMLScriptElement>(CatalogPriceRuleMap.listRowTemplate);

    if (!trTemplateContainer) {
      console.error(`Error: ${CatalogPriceRuleMap.listRowTemplate} element not found`);
      return;
    }
    const rowContainer = document.querySelector<HTMLElement>(CatalogPriceRuleMap.blockContainer);

    if (!rowContainer) {
      console.error(`Error: ${CatalogPriceRuleMap.blockContainer} element not found`);
      return;
    }
    const editCatalogPriceRuleUrl = rowContainer.dataset.catalogPriceUrl;

    if (!editCatalogPriceRuleUrl) {
      console.error('Error: Catalog price rule url not found');
      return;
    }

    const trTemplate = trTemplateContainer.innerHTML as string;
    tbody.innerHTML = '';

    const catalogPriceRules = data.catalogPriceRules as Array<CatalogPriceRuleForListing>;

    this.toggleListVisibility(catalogPriceRules.length > 0);
    try {
      catalogPriceRules.forEach((catalogPriceRule: CatalogPriceRuleForListing) => {
        const temporaryContainer = document.createElement('tbody');
        temporaryContainer.innerHTML = trTemplate.trim();

        const trClone = <HTMLElement>temporaryContainer.firstChild;
        const idField = this.selectListField(trClone, listFields.catalogPriceRuleId);
        const shopField = this.selectListField(trClone, listFields.shop);
        const currencyField = this.selectListField(trClone, listFields.currency);
        const countryField = this.selectListField(trClone, listFields.country);
        const groupField = this.selectListField(trClone, listFields.group);
        const nameField = this.selectListField(trClone, listFields.name);
        const fromQuantityField = this.selectListField(trClone, listFields.fromQuantity);
        const impactField = this.selectListField(trClone, listFields.impact);
        const startDateField = this.selectListField(trClone, listFields.from);
        const endDateField = this.selectListField(trClone, listFields.to);

        const editBtn = this.selectLink(trClone, listFields.editBtn);
        idField.textContent = String(catalogPriceRule.id);
        shopField.textContent = catalogPriceRule.shop;
        currencyField.textContent = catalogPriceRule.currency;
        countryField.textContent = catalogPriceRule.country;
        groupField.textContent = catalogPriceRule.group;
        nameField.textContent = catalogPriceRule.name;
        fromQuantityField.textContent = catalogPriceRule.fromQuantity;
        impactField.textContent = catalogPriceRule.impact;
        startDateField.textContent = catalogPriceRule.startDate;
        endDateField.textContent = catalogPriceRule.endDate;
        editBtn.href = editCatalogPriceRuleUrl.replace('%catalog_price_rule_id%', String(catalogPriceRule.id));
        tbody.append(trClone);
      });
    } catch (e) {
      console.error(e);
    }
  }

  private toggleListVisibility(show: boolean): void {
    this.listContainer.classList.toggle('d-none', !show);
  }

  private selectListField(templateTrClone: HTMLElement, selector: string): HTMLElement {
    const field = templateTrClone.querySelector<HTMLElement>(selector);

    if (field === null) {
      throw new Error(`Error: ${selector} element not found`);
    }
    return field;
  }

  private selectLink(templateTrClone: HTMLElement, selector: string): HTMLLinkElement {
    const field = templateTrClone.querySelector<HTMLLinkElement>(selector);

    if (field === null) {
      throw new Error(`Error: ${selector} element not found`);
    }
    return field;
  }
}
