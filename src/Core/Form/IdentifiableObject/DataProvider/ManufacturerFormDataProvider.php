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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Query\GetManufacturerForEditing;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\QueryResult\EditableManufacturer;

/**
 * Provides data for manufacturers add/edit forms
 */
final class ManufacturerFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $bus;

    /**
     * @var bool
     */
    private $multistoreEnabled;

    /**
     * @var int[]
     */
    private $defaultShopAssociation;

    /**
     * @param CommandBusInterface $bus
     * @param bool $multistoreEnabled
     * @param int[] $defaultShopAssociation
     */
    public function __construct(
        CommandBusInterface $bus,
        $multistoreEnabled,
        array $defaultShopAssociation
    ) {
        $this->bus = $bus;
        $this->multistoreEnabled = $multistoreEnabled;
        $this->defaultShopAssociation = $defaultShopAssociation;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($manufacturerId)
    {
        /** @var EditableManufacturer $editableManufacturer */
        $editableManufacturer = $this->bus->handle(new GetManufacturerForEditing((int) $manufacturerId));

        $data = [
            'name' => $editableManufacturer->getName(),
            'short_description' => $editableManufacturer->getLocalizedShortDescriptions(),
            'description' => $editableManufacturer->getLocalizedDescriptions(),
            'meta_title' => $editableManufacturer->getLocalizedMetaTitles(),
            'meta_description' => $editableManufacturer->getLocalizedMetaDescriptions(),
            'meta_keyword' => $editableManufacturer->getLocalizedMetaKeywords(),
            'is_enabled' => $editableManufacturer->isEnabled(),
        ];

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $editableManufacturer->getAssociatedShops();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $data['is_enabled'] = true;

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        return $data;
    }
}
