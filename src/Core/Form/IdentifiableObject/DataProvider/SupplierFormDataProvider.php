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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Exception\SupplierException;
use PrestaShop\PrestaShop\Core\Domain\Supplier\Query\GetSupplierForEditing;
use PrestaShop\PrestaShop\Core\Domain\Supplier\QueryResult\EditableSupplier;

/**
 * Provides data for suppliers add/edit forms
 */
final class SupplierFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var bool
     */
    private $multistoreEnabled;

    /**
     * @var int[]
     */
    private $defaultShopAssociation;

    /**
     * @var int
     */
    private $contextCountryId;

    /**
     * @param CommandBusInterface $queryBus
     * @param bool $multistoreEnabled
     * @param int[] $defaultShopAssociation
     * @param int $contextCountryId
     */
    public function __construct(
        CommandBusInterface $queryBus,
        $multistoreEnabled,
        array $defaultShopAssociation,
        $contextCountryId
    ) {
        $this->queryBus = $queryBus;
        $this->multistoreEnabled = $multistoreEnabled;
        $this->defaultShopAssociation = $defaultShopAssociation;
        $this->contextCountryId = $contextCountryId;
    }

    /**
     * {@inheritdoc}
     *
     * @throws SupplierException
     */
    public function getData($supplierId)
    {
        /** @var EditableSupplier $editableSupplier */
        $editableSupplier = $this->queryBus->handle(new GetSupplierForEditing((int) $supplierId));

        $data = [
            'name' => $editableSupplier->getName(),
            'description' => $editableSupplier->getLocalizedDescriptions(),
            'phone' => $editableSupplier->getPhone(),
            'mobile_phone' => $editableSupplier->getMobilePhone(),
            'address' => $editableSupplier->getAddress(),
            'address2' => $editableSupplier->getAddress2(),
            'post_code' => $editableSupplier->getPostCode(),
            'city' => $editableSupplier->getCity(),
            'id_country' => $editableSupplier->getCountryId(),
            'id_state' => $editableSupplier->getStateId(),
            'meta_title' => $editableSupplier->getLocalizedMetaTitles(),
            'meta_description' => $editableSupplier->getLocalizedMetaDescriptions(),
            'meta_keyword' => $editableSupplier->getLocalizedMetaKeywords(),
            'is_enabled' => $editableSupplier->isEnabled(),
            'dni' => $editableSupplier->getDni(),
        ];

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $editableSupplier->getAssociatedShops();
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $data['is_enabled'] = false;
        $data['id_country'] = $this->contextCountryId;

        if ($this->multistoreEnabled) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        return $data;
    }
}
