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

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use PrestaShop\PrestaShop\Core\Domain\ApiAccess\Query\GetApiAccessForEditing;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;

#[ApiResource(
    operations: [
        new CQRSGet(
            uriTemplate: '/api-access/infos',
            openapiContext: [
                'summary' => 'Get current API Access details',
                'description' => 'Get API Access public details only, sensitive information like secrets is not returned',
            ],
            provider: QueryProvider::class,
            CQRSQuery: GetApiAccessForEditing::class,
            scopes: [],
            CQRSQueryMapping: [
                '[_context][apiClientId]' => '[apiAccessId]',
            ],
        ),
    ],
)]
class ApiAccess
{
    #[ApiProperty(identifier: true)]
    public int $apiAccessId;
    public string $apiClientId;

    public string $clientName;

    public string $description;

    public bool $enabled;

    public int $lifetime;

    public array $scopes;
}
