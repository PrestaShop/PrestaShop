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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;

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
     * @param bool $isMultistoreFeatureActive
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
    public function getData($languageId)
    {
        //@todo: implement
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        //@todo: implement
        return [];
    }
}
