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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\GetProductForEditing;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCreate;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSDelete;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSUpdate;

#[ApiResource(
    operations: [
        new CQRSGet(
            uriTemplate: '/product/{productId}',
            CQRSQuery: GetProductForEditing::class,
            scopes: [
                'product_read',
            ],
            CQRSQueryMapping: Product::QUERY_MAPPING,
        ),
        new CQRSCreate(
            uriTemplate: '/product',
            CQRSCommand: AddProductCommand::class,
            CQRSQuery: GetProductForEditing::class,
            scopes: [
                'product_write',
            ],
            CQRSQueryMapping: Product::QUERY_MAPPING,
            CQRSCommandMapping: [
                '[type]' => '[productType]',
                '[names]' => '[localizedNames]',
            ],
        ),
        new CQRSUpdate(
            uriTemplate: '/product/{productId}',
            CQRSCommand: UpdateProductCommand::class,
            CQRSQuery: GetProductForEditing::class,
            scopes: [
                'product_write',
            ],
            CQRSQueryMapping: Product::QUERY_MAPPING,
            CQRSCommandMapping: Product::UPDATE_MAPPING,
        ),
        new CQRSDelete(
            uriTemplate: '/product/{productId}',
            CQRSQuery: DeleteProductCommand::class,
            scopes: [
                'product_write',
            ],
        ),
    ],
    exceptionToStatus: [ProductNotFoundException::class => 404],
)]
class Product
{
    #[ApiProperty(identifier: true)]
    public int $productId;

    public string $type;

    public bool $active;

    public array $names;

    public array $descriptions;

    public const QUERY_MAPPING = [
        '[langId]' => '[displayLanguageId]',
        '[basicInformation][localizedNames]' => '[names]',
        '[basicInformation][localizedDescriptions]' => '[descriptions]',
    ];

    public const UPDATE_MAPPING = [
        '[type]' => '[productType]',
        '[names]' => '[localizedNames]',
        '[descriptions]' => '[localizedDescriptions]',
    ];
}
