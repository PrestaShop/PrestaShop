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
use ApiPlatform\Metadata\Put;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\EditCustomerGroupCommand;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Query\GetCustomerGroupForEditing;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCommand;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSQuery;
use PrestaShopBundle\ApiPlatform\Processor\CommandProcessor;

#[ApiResource(
    operations: [
        new CQRSQuery(
            uriTemplate: '/customers/group/{customerGroupId}',
            extraProperties: [
                'queryNormalizationMapping' => [
                    '[id]' => '[customerGroupId]',
                    '[reduction]' => '[reductionPercent]',
                ],
            ],
            CQRSQuery: GetCustomerGroupForEditing::class,
            scopes: [
                'customer_group_read',
            ],
        ),
        new CQRSCommand(
            uriTemplate: '/customers/group',
            extraProperties: [
                'commandNormalizationMapping' => [
                    '[value]' => '[customerGroupId]',
                ],
                'queryNormalizationMapping' => [
                    '[id]' => '[customerGroupId]',
                    '[reduction]' => '[reductionPercent]',
                ],
            ],
            CQRSCommand: AddCustomerGroupCommand::class,
            CQRSQuery: GetCustomerGroupForEditing::class,
            scopes: [
                'customer_group_write',
            ],
        ),
        new Put(
            uriTemplate: '/customers/group/{customerGroupId}',
            processor: CommandProcessor::class,
            extraProperties: [
                'CQRSCommand' => EditCustomerGroupCommand::class,
                'CQRSQuery' => GetCustomerGroupForEditing::class,
                'queryNormalizationMapping' => [
                    '[id]' => '[customerGroupId]',
                    '[reduction]' => '[reductionPercent]',
                ],
                'scopes' => [
                    'customer_group_write',
                ],
            ],
        ),
    ]
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
