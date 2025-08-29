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

namespace Tests\Resources\ApiPlatform\Resources;

use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use Symfony\Component\Serializer\Attribute\Context;

/**
 * This a test class showing how localized values can be handled in the API Platform resource classes:
 *   - you can manually specify the is_localized_value context option
 *   - you can use the customer helper LocalizedValue attribute
 *   - you can set it on class fields and/or getter methods
 *   - if you don't specify it the fields will be indexed by integer
 */
class LocalizedResource
{
    public function __construct(
        private readonly array $localizedLinks,
    ) {
    }

    public array $names;

    #[LocalizedValue]
    public array $descriptions;

    #[Context([LocalizedValue::IS_LOCALIZED_VALUE => true])]
    public array $titles;

    #[LocalizedValue]
    public function getLocalizedLinks(): array
    {
        return $this->localizedLinks;
    }
}
