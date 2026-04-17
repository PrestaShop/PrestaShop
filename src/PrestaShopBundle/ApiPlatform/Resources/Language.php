<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Resources;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use PrestaShopBundle\ApiPlatform\Metadata\PaginatedList;

#[ApiResource(
    operations: [
        new PaginatedList(
            uriTemplate: '/languages',
            ApiResourceMapping: [
                '[id_lang]' => '[langId]',
                '[iso_code]' => '[isoCode]',
                '[language_code]' => '[languageCode]',
                '[date_format_lite]' => '[dateFormat]',
                '[date_format_full]' => '[dateTimeFormat]',
                '[is_rtl]' => '[rtl]',
                '[active]' => '[enabled]',
            ],
            gridDataFactory: 'prestashop.core.grid.factory.language_decorator',
            filtersMapping: [
                '[langId]' => '[id_lang]',
                '[isoCode]' => '[iso_code]',
                '[languageCode]' => '[language_code]',
                '[dateFormat]' => '[date_format_lite]',
                '[dateTimeFormat]' => '[date_format_full]',
                '[rtl]' => '[is_rtl]',
                '[enabled]' => '[active]',
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

    public bool $rtl;

    public bool $enabled;

    public string $flag;
}
