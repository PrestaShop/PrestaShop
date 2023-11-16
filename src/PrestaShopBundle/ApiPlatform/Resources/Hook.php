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
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\UpdateHookStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHook;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHooks;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus;
use PrestaShopBundle\ApiPlatform\Processor\CommandProcessor;
use PrestaShopBundle\ApiPlatform\Provider\QueryListProvider;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/hook-status/{id}',
            requirements: ['id' => '\d+'],
            openapiContext: [
                'summary' => 'Get hook status A',
                'description' => 'Get hook status B',
                'parameters' => [
                    [
                        'name' => 'id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [
                            'type' => 'string',
                        ],
                        'description' => 'Id of the hook you are requesting the status from',
                    ],
                    [
                        'name' => 'Authorization',
                        'in' => 'scopes',
                        'description' => 'hook_read <br> hook_write ',
                    ],
                ],
            ],
            exceptionToStatus: [HookNotFoundException::class => 404],
            provider: QueryProvider::class,
            extraProperties: [
                'query' => GetHookStatus::class,
                'scopes' => ['hook_read'],
            ]
        ),
        new Put(
            uriTemplate: '/hook-status',
            processor: CommandProcessor::class,
            extraProperties: [
                'command' => UpdateHookStatusCommand::class,
                'scopes' => ['hook_write'],
            ]
        ),
        new Get(
            uriTemplate: '/hooks/{id}',
            requirements: ['id' => '\d+'],
            exceptionToStatus: [HookNotFoundException::class => 404],
            provider: QueryProvider::class,
            extraProperties: [
                'query' => GetHook::class,
                'scopes' => ['hook_read'],
            ]
        ),
        new GetCollection(
            uriTemplate: '/hooks',
            provider: QueryListProvider::class,
            extraProperties: [
                'query_builder' => 'prestashop.core.api.query_builder.hook',
                'scopes' => ['hook_read'],
            ]
        ),
    ],
)]
class Hook
{
    #[ApiProperty(identifier: true)]
    public int $id;

    public bool $active;

    public string $name;

    public string $title;

    public string $description;
}
