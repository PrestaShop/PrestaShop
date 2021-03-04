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
use PrestaShop\PrestaShop\Core\Domain\Webservice\Query\GetWebserviceKeyForEditing;
use PrestaShop\PrestaShop\Core\Domain\Webservice\QueryResult\EditableWebserviceKey;

/**
 * Provides data for webservice key form
 */
final class WebserviceKeyFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var int[]
     */
    private $shopIds;

    /**
     * @param CommandBusInterface $queryBus
     * @param int[] $shopIds
     */
    public function __construct(CommandBusInterface $queryBus, array $shopIds)
    {
        $this->queryBus = $queryBus;
        $this->shopIds = $shopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($webserviceKeyId)
    {
        /** @var EditableWebserviceKey $editableWebserviceKey */
        $editableWebserviceKey = $this->queryBus->handle(new GetWebserviceKeyForEditing($webserviceKeyId));

        return [
            'key' => $editableWebserviceKey->getKey(),
            'description' => $editableWebserviceKey->getDescription(),
            'status' => $editableWebserviceKey->getStatus(),
            'permissions' => $this->normalizeResourcePermissions(
                $editableWebserviceKey->getResourcePermissions()
            ),
            'shop_association' => $editableWebserviceKey->getAssociatedShops(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return [
            'status' => true,
            'shop_association' => $this->shopIds,
        ];
    }

    /**
     * Normalizes resource permissions to be in format that is accepted by form
     *
     * @param array $resourcePermissions
     *
     * @return array
     */
    private function normalizeResourcePermissions(array $resourcePermissions)
    {
        $normalizedResourcePermissions = [];

        foreach ($resourcePermissions as $resource => $permissions) {
            foreach ($permissions as $permission) {
                $normalizedResourcePermissions[$permission][] = $resource;
            }
        }

        return $normalizedResourcePermissions;
    }
}
