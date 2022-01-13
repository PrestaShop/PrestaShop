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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Carrier;
use Configuration;
use Context;
use Group;
use PrestaShopException;

class CarrierFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @todo: It is a temporary method to use sharedStorage and should be improved once Carrier creation is migrated.
     *
     * @Given carrier :carrierReference named :carrierName exists
     *
     * @param string $carrierReference
     * @param string $carrierName
     *
     * @throws PrestaShopException
     */
    public function createDefaultIfNotExists(string $carrierReference, string $carrierName): void
    {
        if ($this->getSharedStorage()->exists($carrierReference)) {
            return;
        }

        $carrier = new Carrier(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $carrier->name = $carrierName;
        $carrier->shipping_method = Carrier::SHIPPING_METHOD_PRICE;
        $carrier->delay = '28 days later';
        $carrier->active = true;
        $carrier->add();

        $groups = Group::getGroups(Context::getContext()->language->id);
        $groupIds = [];
        foreach ($groups as $group) {
            $groupIds[] = $group['id_group'];
        }

        $carrier->setGroups($groupIds);

        $this->getSharedStorage()->set($carrierReference, (int) $carrier->id);
    }
}
