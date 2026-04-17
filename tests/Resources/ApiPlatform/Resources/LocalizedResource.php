<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
