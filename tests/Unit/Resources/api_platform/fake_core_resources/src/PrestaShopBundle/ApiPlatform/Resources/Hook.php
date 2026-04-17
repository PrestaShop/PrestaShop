<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Resources\PrestaShopBundle\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use PrestaShop\PrestaShop\Core\Domain\Hook\Command\UpdateHookStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\HookNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHook;
use PrestaShopBundle\ApiPlatform\Processor\CommandProcessor;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;

#[ApiResource(
    operations: [
        new Put(
            uriTemplate: '/hook-status',
            processor: CommandProcessor::class,
            extraProperties: [
                'command' => UpdateHookStatusCommand::class,
                'scopes' => ['hook_write'],
            ]
        ),
        new Get(
            uriTemplate: '/hook/{id}',
            requirements: ['id' => '\d+'],
            exceptionToStatus: [HookNotFoundException::class => 404],
            provider: QueryProvider::class,
            extraProperties: [
                'query' => GetHook::class,
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
