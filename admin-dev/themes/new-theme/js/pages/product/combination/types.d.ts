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
import {AutoCompleteSearchConfig} from '@js/components/auto-complete-search';
import PerfectScrollbar from 'perfect-scrollbar';

export interface AttributesSelectorStates {
  dataSetConfig: AutoCompleteSearchConfig | Record<string, any>;
  searchSource: Record<string, any>;
  scrollbar: PerfectScrollbar | null;
  hasGeneratedCombinations: boolean;
  checkboxList: Array<Record<string, any>>;
}

export interface AttributeGroup {
  id: number;
  name: string;
  publicName: string;
  attributes: Array<Attribute>;
}

/* eslint-disable camelcase */
export interface Attribute {
  id: number;
  color: string;
  group_id: number;
  group_name: string;
  name: string;
  texture: string|null;
}
/* eslint-enable camelcase */
