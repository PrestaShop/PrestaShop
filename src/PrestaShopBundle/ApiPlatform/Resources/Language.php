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
use PrestaShopBundle\ApiPlatform\Metadata\DQBPaginatedList;

#[ApiResource(
    operations: [
        new DQBPaginatedList(
            uriTemplate: '/languages',
            ApiResourceMapping: [
                '[id_lang]' => '[langId]',
                '[iso_code]' => '[isoCode]',
                '[language_code]' => '[languageCode]',
                '[date_format_lite]' => '[dateFormat]',
                '[date_format_full]' => '[dateTimeFormat]',
                '[is_rtl]' => '[isRtl]',
            ],
            queryBuilder: 'prestashop.core.grid.query.builder.language',
            filtersMapping: [
                '[langId]' => '[id_lang]',
                '[isoCode]' => '[iso_code]',
                '[languageCode]' => '[language_code]',
                '[dateFormat]' => '[date_format_lite]',
                '[dateTimeFormat]' => '[date_format_full]',
                '[isRtl]' => '[is_rtl]',
            ],
        ),
    ],
)]
class Language
{
    #[ApiProperty(identifier: true)]
    public int $langId;

    public string $name;

    public string $isoCode;

    public string $languageCode;

    public string $locale;

    public string $dateFormat;

    public string $dateTimeFormat;

    public bool $isRtl;

    public bool $active;
}
