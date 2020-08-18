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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeForEditing;
use PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult\EditableEmployee;

/**
 * Provides data for employee forms.
 */
final class EmployeeFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var bool
     */
    private $isMultistoreFeatureActive;

    /**
     * @var array
     */
    private $defaultShopAssociation;

    /**
     * @param CommandBusInterface $queryBus
     * @param bool $isMultistoreFeatureActive
     * @param array $defaultShopAssociation
     */
    public function __construct(
        CommandBusInterface $queryBus,
        $isMultistoreFeatureActive,
        array $defaultShopAssociation
    ) {
        $this->queryBus = $queryBus;
        $this->isMultistoreFeatureActive = $isMultistoreFeatureActive;
        $this->defaultShopAssociation = $defaultShopAssociation;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($employeeId)
    {
        /** @var EditableEmployee $editableEmployee */
        $editableEmployee = $this->queryBus->handle(new GetEmployeeForEditing((int) $employeeId));

        return [
            'firstname' => $editableEmployee->getFirstName()->getValue(),
            'lastname' => $editableEmployee->getLastName()->getValue(),
            'email' => $editableEmployee->getEmail()->getValue(),
            'default_page' => $editableEmployee->getDefaultPageId(),
            'language' => $editableEmployee->getLanguageId(),
            'active' => $editableEmployee->isActive(),
            'profile' => $editableEmployee->getProfileId(),
            'shop_association' => $editableEmployee->getShopAssociation(),
            'has_enabled_gravatar' => $editableEmployee->hasEnabledGravatar(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        $data = [
            'active' => true,
            'has_enabled_gravatar' => false,
        ];

        if ($this->isMultistoreFeatureActive) {
            $data['shop_association'] = $this->defaultShopAssociation;
        }

        return $data;
    }
}
