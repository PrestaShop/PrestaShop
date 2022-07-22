<?php
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

declare(strict_types=1);

/**
 * @todo: "AdminController\Action" is misleading namespace as it could easily be confused with Controller Action
 *        (while its actually only the toolbar action in a header)
 *        not yet sure if these actions going to evolve much, but probably this class belongs elsewhere (need to double-check legacy behavior),
 */
namespace PrestaShopBundle\Bridge\AdminController\Configuration;

/**
 * This class is the object to instantiate if you want to add an action in the header toolbar of your page.
 * @todo: this is the only action not related to the list,
 *      not sure if it needs an interface at all, but it probably shouldn't use the same interface that is used for list actions
 *      (even though for now they seem to be similar, but the purpose is different and they shouldn't be coupled)
 */
class HeaderToolbarAction
{
}
