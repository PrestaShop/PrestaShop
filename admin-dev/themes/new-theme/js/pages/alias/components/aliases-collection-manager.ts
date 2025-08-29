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

import AliasFormMap from '@pages/alias/form/alias-form.map';

const {$} = window;

/**
  * This component is used in alias form page to manage the behavior of the aliases collection.
  */
export default class AliasesCollectionManager {
  $collection: JQuery;

  idxAlias: number;

  constructor() {
    // Get dom element of the collection
    this.$collection = $(AliasFormMap.aliasesCollection);
    this.idxAlias = this.$collection.children(AliasFormMap.aliasItem).length - 1;
    // Initialize listeners
    this.initListeners();
    // If we have no alias, we add one
    if (this.$collection.children(AliasFormMap.aliasItem).length === 0) {
      this.onAddAlias(null, false);
    }
  }

  // Initialize listeners to manage the collection properly.
  private initListeners(): void {
    this.$collection.parent().on('click', AliasFormMap.addAliasButton, (e: Event) => this.onAddAlias(e));
    this.$collection.on('click', AliasFormMap.deleteAliasButton, (e: Event) => this.onDeleteAlias(e));
    this.$collection.on('keydown', AliasFormMap.aliasItemInput, (e: Event) => this.onKeyDownAlias(e as KeyboardEvent));
  }

  // On click in add alias button
  private onAddAlias(e: Event|null = null, needFocus: boolean = true): void {
    if (e) {
      e.preventDefault();
    }
    // +1 idx
    this.idxAlias += 1;
    // Retrieve the prototype and format it
    let prototype = this.$collection.data('prototype');
    prototype = prototype.replace(/__name__/g, this.idxAlias);
    // Then, add it at the bottom of the collection
    this.$collection.append(prototype);
    // We set active to true on the added alias
    this.$collection.children().last().find('[name$="[active]"][value=1]').prop('checked', true);
    // We set focus on last added input if needed
    if (needFocus) {
      this.$collection.children().last().find(AliasFormMap.aliasItemInput).focus();
    }
    // Check if we need to display delete buttons or not
    this.refreshDeleteAliasButtons();
  }

  // On click on delete alias button
  private onDeleteAlias(e: Event): void {
    e.preventDefault();
    // Remove the alias element related to this button
    const $item = $(e.target as HTMLElement);
    $item.parents(AliasFormMap.aliasItem).remove();
    // Check if we need to display delete buttons or not
    this.refreshDeleteAliasButtons();
  }

  // On key down in alias item input => if it's a comma (and the value is already set), add a new alias and focus on new input
  private onKeyDownAlias(e: KeyboardEvent): void {
    if (e.key === ',') {
      e.preventDefault();
      if (this.$collection.children().last().find('input').val() !== '') {
        this.onAddAlias(e);
      }
    }
  }

  // Check if we need to display delete buttons or not (if there is only one alias, we hide the delete buttons)
  private refreshDeleteAliasButtons(): void {
    if (this.$collection.children().length === 1) {
      this.$collection.children().find(AliasFormMap.deleteAliasButton).addClass('d-none');
    } else {
      this.$collection.children().find(AliasFormMap.deleteAliasButton).removeClass('d-none');
    }
  }
};
