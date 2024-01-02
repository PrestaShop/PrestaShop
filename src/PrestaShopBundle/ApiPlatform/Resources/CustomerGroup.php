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

namespace PrestaShopBundle\ApiPlatform\Resources;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\DeleteCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\EditCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Exception\GroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\QueryResult\EditableCustomerGroup;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCreate;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSDelete;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSUpdate;

#[ApiResource(
    operations: [
        new CQRSGet(
            uriTemplate: '/customers/group/{customerGroupId}',
            CQRSQuery: GetCustomerGroupForEditing::class,
            scopes: [
                'customer_group_read',
            ],
            // QueryResult format doesn't match with ApiResource, so we can specify a mapping so that it is normalized with extra fields adapted for the ApiResource DTO
            CQRSQueryMapping: [
                // EditableCustomerGroup::$id is normalized as [customerGroupId]
                '[id]' => '[customerGroupId]',
                // EditableCustomerGroup::$reduction is normalized as [reductionPercent]
                '[reduction]' => '[reductionPercent]',
            ],
        ),
        new CQRSCreate(
            uriTemplate: '/customers/group',
            CQRSCommand: AddCustomerGroupCommand::class,
            CQRSQuery: GetCustomerGroupForEditing::class,
            scopes: [
                'customer_group_write',
            ],
            // Here, we use query mapping to adapt normalized query result for the ApiPlatform DTO
            CQRSQueryMapping: [
                '[id]' => '[customerGroupId]',
                '[reduction]' => '[reductionPercent]',
            ],
            // Here, we use command mapping to adapt the normalized command result for the CQRS query
            CQRSCommandMapping: [
                '[groupId]' => '[customerGroupId]',
            ],
        ),
        new CQRSUpdate(
            uriTemplate: '/customers/group/{customerGroupId}',
            CQRSCommand: EditCustomerGroupCommand::class,
            CQRSQuery: GetCustomerGroupForEditing::class,
            scopes: [
                'customer_group_write',
            ],
            // Here we use the ApiResource DTO mapping to transform the normalized query result
            ApiResourceMapping: [
                '[id]' => '[customerGroupId]',
                '[reduction]' => '[reductionPercent]',
            ],
        ),
        new CQRSDelete(
            uriTemplate: '/customers/group/{customerGroupId}',
            CQRSQuery: DeleteCustomerGroupCommand::class,
            scopes: [
                'customer_group_write',
            ],
            // Here, we use query mapping to adapt URI parameters to the expected constructor parameter name
            CQRSQueryMapping: [
                '[customerGroupId]' => '[groupId]',
            ],
        ),
    ],
    exceptionToStatus: [GroupNotFoundException::class => 404],
)]
class CustomerGroup
{
    #[ApiProperty(identifier: true)]
    public int $customerGroupId;

    public array $localizedNames;

    public float $reductionPercent;

    public bool $displayPriceTaxExcluded;

    public bool $showPrice;

    public array $shopIds;
}
