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

import EventEmitterClass from 'events';

function overloadProperties(obj, ignoreList, afterFn) {
  console.log(Object.getOwnPropertyNames(obj));
  Object.getOwnPropertyNames(obj).forEach(propName => {
    const prop = obj[propName];

    if (Object.prototype.toString.call(prop) === '[object Function]' && !ignoreList.includes(propName)) {
      obj[propName] = (function(fnName) {
        return function(...args) {
          prop.apply(this, args);
          afterFn.call(this, fnName, args);
        };
      })(propName);
    }
  });
}

export default function EventHOC(Component, ignoreList = []) {
  const EventEmitter = new EventEmitterClass();

  overloadProperties(Component.prototype, ignoreList, (fnName, args) => {
    console.log(`${Component.name}.${fnName}`, args);
    EventEmitter.emit(`${Component.name}.${fnName}`, args);
  });

  return Component;
}
