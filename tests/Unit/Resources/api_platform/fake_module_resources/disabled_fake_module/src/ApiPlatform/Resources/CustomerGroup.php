<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Resources\api_platform\fake_module_resources\disabled_fake_module\src\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\Command\AddCustomerGroupCommand;
use PrestaShopBundle\ApiPlatform\Processor\CommandProcessor;

#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/customers/group',
            processor: CommandProcessor::class,
            extraProperties: [
                'command' => AddCustomerGroupCommand::class,
                'scopes' => ['customer_group_read'],
            ],
        ),
    ]
)]
class CustomerGroup
{
    public array $localizedNames;

    public float $reductionPercent;

    public bool $displayPriceTaxExcluded;

    public bool $showPrice;

    public array $shopIds;
}
