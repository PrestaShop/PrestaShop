<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources\ApiPlatform\Resources;

use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Resource with localized values in its sub objects, at several levels.
 */
class LocalizedSubObjectsResource
{
    #[LocalizedValue]
    public array $names;

    #[Assert\Valid]
    public LocalizedSubObject $item;

    /**
     * @var LocalizedSubObject[]
     */
    public array $items;
}
