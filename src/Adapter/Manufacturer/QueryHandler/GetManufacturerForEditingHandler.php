<?php

/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\QueryHandler;

use Manufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\EditableManufacturer;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\Handler\GetManufacturerForEditingInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\VO\ManufacturerId;

/**
 * Looks up a manufacturer for editing
 */
class GetManufacturerForEditingHandler implements GetManufacturerForEditingInterface
{

    /**
     * @param GetManufacturerForEditing $query
     *
     * @return EditableManufacturer
     * @throws ManufacturerNotFoundException
     * @throws ManufacturerException
     */
    public function handle(GetManufacturerForEditing $query)
    {
        $entity = $this->loadById($query->getManufacturerId());

        return $this->buildEditableManufacturer($entity);
    }

    /**
     * @param ManufacturerId $manufacturerId
     *
     * @return Manufacturer
     * @throws ManufacturerNotFoundException
     */
    private function loadById(ManufacturerId $manufacturerId)
    {
        $entity = new \Manufacturer($manufacturerId->getValue());

        if ($entity->id <= 0) {
            throw new ManufacturerNotFoundException(
                "Could not find the requested Manufacturer"
            );
        }
        if ((int) $entity->id !== $manufacturerId->getValue()) {
            throw new ManufacturerNotFoundException(
                sprintf(
                    "The retrieved id %s does not match the requested Manufacturer %s",
                    var_export($entity->id, true),
                    var_export($manufacturerId->getValue(), true)
                )
            );
        }

        return $entity;
    }

    /**
     * @param Manufacturer $entity
     *
     * @return EditableManufacturer
     * @throws ManufacturerException
     */
    private function buildEditableManufacturer(Manufacturer $entity)
    {
        return new EditableManufacturer(
            new ManufacturerId($entity->id),
            $entity->name,
            $entity->description,
            $entity->short_description,
            $entity->meta_title,
            $entity->meta_keywords,
            $entity->meta_description,
            $entity->active
        );
    }
}
