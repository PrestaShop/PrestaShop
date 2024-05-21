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

namespace PrestaShop\PrestaShop\Adapter\Carrier\Repository;

use Carrier;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotAddCarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CannotUpdateCarrierException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

/**
 * Provides access to carrier data source
 */
class CarrierRepository extends AbstractMultiShopObjectModelRepository
{
    /**
     * @param CarrierId $carrierId
     *
     * @return Carrier
     *
     * @throws AttributeNotFoundException
     * @throws CoreException
     */
    public function get(CarrierId $carrierId): Carrier
    {
        /** @var Carrier $carrier */
        $carrier = $this->getObjectModel(
            $carrierId->getValue(),
            Carrier::class,
            CarrierNotFoundException::class
        );

        return $carrier;
    }

    public function add(Carrier $carrier): CarrierId
    {
        $carrierId = $this->addObjectModel(
            $carrier,
            CannotAddCarrierException::class
        );

        return new CarrierId($carrierId);
    }

    public function updateInNewVersion(CarrierId $carrierId, Carrier $carrier): CarrierId
    {
        // Get old carrier to softly delete it
        /** @var Carrier $oldCarrier */
        $oldCarrier = $this->get($carrierId);
        /** @var Carrier $newCarrier */
        $newCarrier = $oldCarrier->duplicateObject();
        $oldCarrier->deleted = true;
        $this->partiallyUpdateObjectModel($oldCarrier, ['deleted'], CannotUpdateCarrierException::class);

        // Then create a new carrier with a new id reference
        /* @var Carrier $newCarrier */
        if ($carrier->name) {
            $newCarrier->name = $carrier->name;
        }
        if ($carrier->grade) {
            $newCarrier->grade = $carrier->grade;
        }
        if ($carrier->url) {
            $newCarrier->url = $carrier->url;
        }
        if ($carrier->position) {
            $newCarrier->position = $carrier->position;
        }
        if ($carrier->active) {
            $newCarrier->active = $carrier->active;
        }
        if ($carrier->delay) {
            $newCarrier->delay = $carrier->delay;
        }
        $newCarrier->deleted = false; // just to be sure...

        // Copy all others information like ranges, shops associated, ...
        $newCarrier->copyCarrierData($carrierId->getValue());
        $this->updateObjectModel($newCarrier, CannotUpdateCarrierException::class);

        return new CarrierId($newCarrier->id);
    }
}
